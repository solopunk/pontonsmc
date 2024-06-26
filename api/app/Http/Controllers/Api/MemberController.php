<?php

namespace App\Http\Controllers\Api;

use App\ErrorResponseTrait;
use App\Http\Controllers\Controller;
use App\JsonResponseTrait;
use App\Models\BoatType;
use App\Models\Homeport;
use App\Models\Member;
use App\Models\MemberType;
use App\Utils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MemberController extends Controller
{
    use JsonResponseTrait, ErrorResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $members = Member::paginate(10);

        foreach ($members as $member) {
            if ($member->member_types()->where('uid', 'latecomer')->exists()) {
                $member->upToDate = false;
            } else {
                $member->upToDate = true;
            }

            $member->type = $member->member_types()->where('uid', '!=', 'latecomer')->first()->name;
        }

        return response()->json($members, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'type' => ['required', 'string', Rule::in(['supporter', 'active', 'committee'])],

                'member.email' => 'required|email',
                'member.first' => 'required|string',
                'member.last' => 'required|string',
                'member.birthdate' => 'required|string',
                'member.address' => 'required|string',
                'member.postal_code' => 'required|string',
                'member.city' => 'required|string',
                'member.phone' => 'required|string',
                'member.job' => 'required|string',

                'boat.name' => 'required_if:type,active,committee|string',
                'boat.brand' => 'required_if:type,active,committee|string',
                'boat.model' => 'required_if:type,active,committee|string',
                'boat.year' => 'required_if:type,active,committee|date',
                'boat.length' => 'required_if:type,active,committee|decimal:0,2',
                'boat.width' => 'required_if:type,active,committee|decimal:0,2',
                'boat.type' => 'required_if:type,active,committee|string',
                'boat.homeport' => 'required_if:type,active,committee|string',

                'coowner.first' => 'prohibited_if:type,supporter|string',
                'coowner.last' => 'prohibited_if:type,supporter|string',
                'coowner.nationality' => 'prohibited_if:type,supporter|string',

                'contribution' => 'numeric|gt:0',

                'welcome' => 'boolean'
            ]);

            $member = Member::create($request->input('member'));

            // type
            $type = MemberType::where('uid', $request->input('type'))->pluck('id');
            $member->member_types()->attach($type);

            // contribution
            if ($request->filled('contribution')) {
                $member->contributions()->createQuietly(['amount' => $request->input('contribution')]);
            }

            // boat
            if ($request->input('type') !== 'supporter') {
                $boatData = array_intersect_key($request->input('boat'), array_flip(['name', 'brand', 'model', 'year', 'length', 'width']));
                $boatData['boat_type_id'] = BoatType::where('uid', $request->input('boat.type'))->pluck('id')->first();
                $boatData['homeport_id'] = Homeport::where('uid', $request->input('boat.homeport'))->pluck('id')->first();

                $boat = $member->boat()->createQuietly($boatData);

                // coowner
                if ($request->filled('coowner')) {
                    $boat->coowner()->createQuietly($request->input('coowner'));
                }
            }

            // welcome mail
            if ($request->filled('welcome')) {
                Utils::sendPasswordReset($member->email);
            }

            return $this->successResponse($member);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return $this->errorResponse();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member)
    {
        $member->load('contributions');

        $member->hasCoowner = false;
        if ($member->boat()->exists()) {
            $member->load('boat');
            $member->boat->type = BoatType::where('id', $member->boat->boat_type_id)->pluck('uid')->first();
            $member->boat->homeport = Homeport::where('id', $member->boat->homeport_id)->pluck('uid')->first();

            if ($member->boat->coowner()->exists()) {
                $member->hasCoowner = true;
                $member->coowner = $member->boat->coowner;
            }
        }

        $member->boatTypes = BoatType::all();
        $member->homeports = Homeport::all();

        // check if he is latecomer
        $member->isLatecomer = true;
        if ($member->member_types()->where('uid', 'latecomer')->exists()) {
            $member->isLatecomer = true;
        }

        // get current member type
        $member->type = $member->member_types()->where('uid', '!=', 'latecomer')->first();

        // send all possible member types
        $member->memberTypes = MemberType::where('uid', '!=', 'latecomer')->get();

        return response()->json($member, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Member $member)
    {
        try {
            $request->validate([
                'type' => 'sometimes|required|string',

                'member.email' => 'sometimes|required|email',
                'member.first' => 'sometimes|required|string',
                'member.last' => 'sometimes|required|string',
                'member.birthdate' => 'sometimes|required|string',
                'member.address' => 'sometimes|required|string',
                'member.postal_code' => 'sometimes|required|string',
                'member.city' => 'sometimes|required|string',
                'member.phone' => 'sometimes|required|string',
                'member.job' => 'sometimes|required|string',

                'boat.name' => 'sometimes|required|string',
                'boat.brand' => 'sometimes|required|string',
                'boat.model' => 'sometimes|required|string',
                'boat.year' => 'sometimes|required|date',
                'boat.length' => 'sometimes|required|decimal:0,2',
                'boat.width' => 'sometimes|required|decimal:0,2',
                'boat.type' => 'sometimes|required|string',
                'boat.homeport' => 'sometimes|required|string',

                'coowner.first' => 'sometimes|required|string',
                'coowner.last' => 'sometimes|required|string',
                'coowner.nationality' => 'sometimes|required|string',

                'contribution' => 'sometimes|required|numeric|gt:0',
            ]);

            // update member infos
            if ($request->filled('member')) {
                $member->updateQuietly($request->input('member'));
            }

            // add or update contribution
            if ($request->filled('contribution')) {
                $member
                    ->contributions()
                    ->whereDate('created_at', date('Y-m-d'))
                    ->updateOrCreate([], ['amount' => $request->input('contribution')]);
            }

            if (!$member->member_types()->where('uid', '=', 'supporter')->exists()) {
                // update boat for != supporter
                if ($request->filled('boat')) {
                    $boatTypeId = BoatType::where('uid', $request->input('boat.type'))->pluck('id')->first();
                    $homeportId = Homeport::where('uid', $request->input('boat.homeport'))->pluck('id')->first();

                    $data = array_intersect_key($request->input('boat'), array_flip(['name', 'brand', 'model', 'year', 'length', 'width']));
                    if ($boatTypeId) {
                        $data['boat_type_id'] = $boatTypeId;
                    }
                    if ($homeportId) {
                        $data['homeport_id'] = $homeportId;
                    }

                    $member->boat->updateQuietly($data);
                }

                // update or create coowner
                if ($request->filled('coowner')) {
                    if ($member->boat->coowner()->exists()) {
                        $data = array_intersect_key($request->input('coowner'), array_flip(['first', 'last', 'nationality']));
                        $member->boat->coowner()->update($data);
                    } else {
                        $request->validate([
                            'coowner.first' => 'required|string',
                            'coowner.last' => 'required|string',
                            'coowner.nationality' => 'required|string',
                        ]);
                        $member->boat->coowner()->createQuietly($request->input('coowner'));
                    }
                }

                if (boolval($request->input('delete-coowner'))) {
                    $member->boat->coowner()->delete();
                }
            }

            // change type
            if ($request->filled('type')) {
                $pastType = $member->member_types()->where('uid', '!=', 'latecomer')->first();

                // from supporter to ...
                if ($pastType->uid === 'supporter') {
                    $request->validate([
                        'type' => ['string', Rule::in(['active', 'committee'])],

                        'boat.name' => 'required|string',
                        'boat.brand' => 'required|string',
                        'boat.model' => 'required|string',
                        'boat.year' => 'required|date',
                        'boat.length' => 'required|decimal:0,2',
                        'boat.width' => 'required|decimal:0,2',
                        'boat.type' => 'required|string',
                        'boat.homeport' => 'required|string',
                    ]);

                    $boat = $member->boat()->createQuietly([
                        'name' => $request->input('boat.name'),
                        'brand' => $request->input('boat.brand'),
                        'model' => $request->input('boat.model'),
                        'year' => $request->input('boat.year'),
                        'length' => $request->input('boat.length'),
                        'width' => $request->input('boat.width'),
                        'boat_type_id' => BoatType::where('uid', $request->input('boat.type'))->pluck('id')->first(),
                        'homeport_id' => Homeport::where('uid', $request->input('boat.homeport'))->pluck('id')->first()
                    ]);

                    if ($request->filled('coowner')) {
                        $request->validate([
                            'coowner.first' => 'required|string',
                            'coowner.last' => 'required|string',
                            'coowner.nationality' => 'required|string',
                        ]);

                        $boat->coowner()->createQuietly($request->input('coowner'));
                    }
                } else {
                    // from ... to supporter
                    if ($request->input('type') === 'supporter') {
                        $member->boat()->delete();
                    }
                }

                // detach past type
                $member->member_types()->detach($pastType->id);

                // attach new type
                $type = MemberType::where('uid', $request->input('type'))->pluck('id');
                $member->member_types()->attach($type);
            }

            return $this->successResponse($member);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return $this->errorResponse('', 500, $e);
        }
    }

    public function welcome(Member $member)
    {
        if (!boolval($member->password)) {
            Utils::sendPasswordReset($member->email);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        $member->deleteQuietly();
    }
}
