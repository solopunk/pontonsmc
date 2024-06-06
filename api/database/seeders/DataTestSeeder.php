<?php

namespace Database\Seeders;

use App\Models\Boat;
use App\Models\BoatType;
use App\Models\Contribution;
use App\Models\Coowner;
use App\Models\Homepage;
use App\Models\Homeport;
use App\Models\Mail;
use App\Models\Member;
use App\Models\MemberType;
use App\Models\Scoop;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DataTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed the Member table
        $members = Member::factory()
            ->count(50)
            ->create()
            ->each(function ($member) {
                // Sélectionner un type parmi 'supporter', 'active', 'committee'
                $selectedType = MemberType::whereIn('uid', ['supporter', 'active', 'committee'])
                    ->inRandomOrder()
                    ->first();

                // Attacher le nouveau type sélectionné
                $member->member_types()->attach($selectedType->id);

                // Attribuer aléatoirement le type 'latecomer'
                if (rand(0, 1)) {
                    $latecomerType = MemberType::where('uid', 'latecomer')->first();
                    $member->member_types()->attach($latecomerType->id);
                }
            });

        // Seed the Boat table and Coowner table
        $membersActiveCommittee = Member::whereHas('member_types', function ($query) {
            $query->whereIn('uid', ['active', 'committee']);
        })->get();

        foreach ($membersActiveCommittee as $member) {
            // Créer un bateau pour chaque membre
            $boat = Boat::factory()->create([
                'homeport_id' => Homeport::inRandomOrder()->first()->id,
                'boat_type_id' => BoatType::inRandomOrder()->first()->id,
                'member_id' => $member->id,
            ]);

            // Créer un co-propriétaire pour chaque bateau
            Coowner::factory()->create([
                'boat_id' => $boat->id,
            ]);
        }


        // Seed the Contribution table
        Contribution::factory()
            ->count(100)
            ->create();

        // // Seed the Coowner table
        // Coowner::factory()
        //     ->count(20)
        //     ->create();

        // Seed the Homepage table
        Homepage::factory()->count(1)->create();

        // Seed the Mail table
        Mail::factory()
            ->count(50)
            ->create();

        // Seed the Scoop table
        Scoop::factory()
            ->count(15)
            ->create();
    }
}
