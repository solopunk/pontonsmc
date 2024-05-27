<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\BoatType;
use App\Models\Homeport;
use App\Models\MailType;
use App\Models\MemberType;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        Admin::create([
            'email' => 'webmaster@media-events.mc',
            'pw' => 'Mediadmin98!'
        ]);

        foreach ([
            [
                'name' => 'Sympathisant',
                'uid' => 'supporter'
            ],
            [
                'name' => 'Actif',
                'uid' => 'active'
            ],
            [
                'name' => 'Comité Directeur',
                'uid' => 'committee'
            ],
            [
                'name' => 'Retardataire',
                'uid' => 'latecommer'
            ],
        ] as $type) {
            MemberType::create([
                'name' => $type['name'],
                'uid' => $type['uid']
            ]);
        }

        foreach ([
            [
                'name' => 'Moteur',
                'uid' => 'engine'
            ],
            [
                'name' => 'Voile',
                'uid' => 'sail'
            ],
        ] as $type) {
            BoatType::create([
                'name' => $type['name'],
                'uid' => $type['uid']
            ]);
        }

        foreach ([
            [
                'name' => 'Hercule',
                'uid' => 'hercule'
            ],
            [
                'name' => 'Fontvieille',
                'uid' => 'fontvieille'
            ],
            [
                'name' => 'Autre',
                'uid' => 'other'
            ],
        ] as $type) {
            Homeport::create([
                'name' => $type['name'],
                'uid' => $type['uid']
            ]);
        }

        foreach ([
            [
                'name' => 'Nouvelle',
                'uid' => 'new'
            ],
            [
                'name' => 'Relance',
                'uid' => 'reminder'
            ],
        ] as $type) {
            MailType::create([
                'name' => $type['name'],
                'uid' => $type['uid']
            ]);
        };
    }
}
