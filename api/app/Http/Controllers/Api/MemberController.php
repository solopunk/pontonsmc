<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BoatType;
use App\Models\Homeport;
use App\Models\Member;
use App\Models\MemberType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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

            // coowner
            if ($request->filled('coowner')) {
                $boat->coowner()->createQuietly($request->input('coowner'));
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Member $member)
    {
        $request->validate([
            'type' => 'string',

            'member.email' => 'email',
            'member.first' => 'string',
            'member.last' => 'string',
            'member.birthdate' => 'string',
            'member.address' => 'string',
            'member.postal_code' => 'string',
            'member.city' => 'string',
            'member.phone' => 'string',
            'member.job' => 'string',

            'boat.name' => 'string',
            'boat.brand' => 'string',
            'boat.model' => 'string',
            'boat.year' => 'date',
            'boat.length' => 'decimal:0,2',
            'boat.width' => 'decimal:0,2',
            'boat.type' => 'string',
            'boat.homeport' => 'string',

            'coowner.first' => 'string',
            'coowner.last' => 'string',
            'coowner.nationality' => 'string',

            'contribution' => 'numeric|gt:0',
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
                $member->boat->updateQuietly($request->input('boat'));
            }

            // update or create coowner
            if ($request->filled('coowner')) {
                if ($member->boat->coowner()->exists()) {
                    $member->boat->coowner()->update($request->input('coowner'));
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
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        $member->deleteQuietly();
    }
}
