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
            'type' => ['required', Rule::in(['supporter', 'active', 'committee'])],
            'member.email' => 'required|email',
            'member.first' => 'required',
            'member.last' => 'required',
            'member.birthdate' => 'required',
            'member.address' => 'required',
            'member.postal_code' => 'required',
            'member.city' => 'required',
            'member.phone' => 'required',
            'member.job' => 'required',

            'boat.name' => 'required_if:type,active,committee',
            'boat.brand' => 'required_if:type,active,committee',
            'boat.model' => 'required_if:type,active,committee',
            'boat.year' => 'required_if:type,active,committee',
            'boat.length' => 'required_if:type,active,committee',
            'boat.width' => 'required_if:type,active,committee',
            'boat.type' => 'required_if:type,active,committee',
            'boat.homeport' => 'required_if:type,active,committee',

            'coowner.first' => 'prohibited_if:type,supporter',
            'coowner.last' => 'prohibited_if:type,supporter',
            'coowner.nationality' => 'prohibited_if:type,supporter',
        ]);

        $member = Member::create($request->input('member'));

        // type
        $type = MemberType::where('uid', $request->input('type'))->pluck('id');
        $member->member_types()->attach($type);

        // contribution
        $member->contributions()->createQuietly(
            $request->filled('contribution') ? ['amount' => $request->input('contribution')] : []
        );

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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        //
    }
}
