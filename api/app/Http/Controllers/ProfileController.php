<?php

namespace App\Http\Controllers;

use App\Models\BoatType;
use App\Models\Homeport;
use App\Models\Member;
use App\Models\MemberType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function patchInfos(Request $request, Member $member)
    {
        $request->validate([
            'first' => 'sometimes|required|string',
            'last' => 'sometimes|required|string',
            'birthdate' => 'sometimes|required|string',
            'address' => 'sometimes|required|string',
            'postal_code' => 'sometimes|required|string',
            'city' => 'sometimes|required|string',
            'phone' => 'sometimes|required|string',
            'job' => 'sometimes|required|string',
        ]);

        $member->updateQuietly($request->all());
    }

    public function addBoat(Request $request, Member $member)
    {
        if ($member->member_types()->where('uid', 'supporter')->exists()) {
            $request->validate([
                'name' => 'required|string',
                'brand' => 'required|string',
                'model' => 'required|string',
                'year' => 'required|date',
                'length' => 'required|decimal:0,2',
                'width' => 'required|decimal:0,2',
                'type' => 'required|string',
                'homeport' => 'required|string',
            ]);

            $data = $request->only(['name', 'brand', 'model', 'year', 'length', 'width']);

            $data['boat_type_id'] = BoatType::where('uid', $request->input('type'))->pluck('id')->first();
            $data['homeport_id'] = Homeport::where('uid', $request->input('homeport'))->pluck('id')->first();

            $member->boat()->createQuietly($data);

            // change member type
            $member->member_types()->detach($member->member_types()->where('uid', '!=', 'latecomer')->first()->id);
            $type = MemberType::where('uid', 'active')->pluck('id');
            $member->member_types()->attach($type);
        }
    }

    public function patchBoat(Request $request, Member $member)
    {
        if (!$member->member_types()->where('uid', 'supporter')->exists()) {
            $request->validate([
                'name' => 'sometimes|required|string',
                'brand' => 'sometimes|required|string',
                'model' => 'sometimes|required|string',
                'year' => 'sometimes|required|date',
                'length' => 'sometimes|required|decimal:0,2',
                'width' => 'sometimes|required|decimal:0,2',
                'type' => 'sometimes|required|string',
                'homeport' => 'sometimes|required|string',
            ]);

            // Récupérer les IDs des relations
            $boatTypeId = BoatType::where('uid', $request->input('type'))->pluck('id')->first();
            $homeportId = Homeport::where('uid', $request->input('homeport'))->pluck('id')->first();

            // Préparer les données à mettre à jour
            $data = $request->only(['name', 'brand', 'model', 'year', 'length', 'width']);
            if ($boatTypeId) {
                $data['boat_type_id'] = $boatTypeId;
            }
            if ($homeportId) {
                $data['homeport_id'] = $homeportId;
            }

            // Mettre à jour le bateau
            $member->boat->updateQuietly($data);

            return response()->json(['message' => 'Boat updated successfully'], 200);
        }
    }

    public function deleteBoat(Member $member)
    {
        $member->boat()->delete();

        // change member type
        $member->member_types()->detach($member->member_types()->where('uid', '!=', 'latecomer')->first()->id);
        $type = MemberType::where('uid', 'supporter')->pluck('id');
        $member->member_types()->attach($type);
    }

    public function addCoowner(Request $request, Member $member)
    {
        if (!$member->member_types()->where('uid', 'supporter')->exists()) {
            $request->validate([
                'first' => 'required|string',
                'last' => 'required|string',
                'nationality' => 'required|string',
            ]);

            $member->boat->coowner()->createQuietly($request->only('first', 'last', 'nationality'));
        }
    }

    public function patchCoowner(Request $request, Member $member)
    {
        if (!$member->member_types()->where('uid', 'supporter')->exists()) {
            $request->validate([
                'first' => 'sometimes|required|string',
                'last' => 'sometimes|required|string',
                'nationality' => 'sometimes|required|string',
            ]);

            $member->boat->coowner()->update($request->only('first', 'last', 'nationality'));
        }
    }

    public function deleteCoowner(Member $member)
    {
        $member->boat->coowner()->delete();
    }

    public function updateEmail(Request $request, Member $member)
    {
        $request->validate([
            'email' => 'required|email|unique:members'
        ]);

        $member->email = $request->input('email');
        $member->saveQuietly();
    }

    public function updatePassword(Request $request, Member $member)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed|different:current_password'
        ]);

        if (!Hash::check($request->current_password, $member->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        $member->password = Hash::make($request->input('password'));
        $member->saveQuietly();
    }
}
