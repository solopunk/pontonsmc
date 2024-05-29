<?php

namespace App\Http\Controllers;

use App\Mail\DeclineRequestor;
use App\Models\BoatType;
use App\Models\Homeport;
use App\Models\Member;
use App\Models\MemberType;
use App\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class AdhesionController extends Controller
{
    public function requestAdhesion(Request $request)
    {
        $request->validate([
            'type' => ['required', Rule::in(['supporter', 'active'])],

            'member.email' => 'required|email',
            'member.first' => 'required',
            'member.last' => 'required',
            'member.birthdate' => 'required',
            'member.address' => 'required',
            'member.postal_code' => 'required',
            'member.city' => 'required',
            'member.phone' => 'required',
            'member.job' => 'required',

            'boat.name' => 'required_if:type,active',
            'boat.brand' => 'required_if:type,active',
            'boat.model' => 'required_if:type,active',
            'boat.year' => 'required_if:type,active',
            'boat.length' => 'required_if:type,active',
            'boat.width' => 'required_if:type,active',
            'boat.type' => 'required_if:type,active',
            'boat.homeport' => 'required_if:type,active',

            'coowner.first' => 'prohibited_if:type,supporter',
            'coowner.last' => 'prohibited_if:type,supporter',
            'coowner.nationality' => 'prohibited_if:type,supporter',
        ]);

        $member = Member::create($request->input('member') + ['pending' => true]);

        // type
        $type = MemberType::where('uid', $request->input('type'))->pluck('id');
        $member->member_types()->attach($type);

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
                $request->validate([
                    'coowner.first' => 'required',
                    'coowner.last' => 'required',
                    'coowner.nationality' => 'required',
                ]);

                $boat->coowner()->createQuietly($request->input('coowner'));
            }
        }
    }

    public function acceptAdhesion(Member $requestor)
    {
        $requestor->pending = false;
        $requestor->saveQuietly();

        Utils::sendPasswordReset($requestor->email);
    }

    public function declineAdhesion(Member $requestor)
    {
        $requestor->deleteQuietly();

        Mail::to($requestor->email)->send(new DeclineRequestor());
    }
}
