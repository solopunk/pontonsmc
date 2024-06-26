<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Member;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_supporter(): void
    {
        $this->seed();
        $this->actingAs(Admin::find(1), 'admin');

        $this->post('api/member', [
            'type' => 'supporter',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->word(),
                'postal_code' => fake()->word(),
                'city' => fake()->word(),
                'phone' => fake()->word(),
                'job' => fake()->word()
            ]
        ]);

        $this->assertDatabaseCount('members', 1);
        $this->assertDatabaseCount('member_member_type', 1);
        $this->assertDatabaseHas('member_member_type', [
            'member_id' => 1,
            'member_type_id' => 1
        ]);
        $this->assertDatabaseCount('contributions', 0);
    }

    public function test_create_active(): void
    {
        $this->seed();
        $this->actingAs(Admin::find(1), 'admin');

        $this->post('api/member', [
            'type' => 'active',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->word(),
                'postal_code' => fake()->word(),
                'city' => fake()->word(),
                'phone' => fake()->word(),
                'job' => fake()->word()
            ],
            'boat' => [
                'name' => fake()->word(),
                'brand' => fake()->word(),
                'model' => fake()->word(),
                'year' => fake()->date(),
                'length' => fake()->randomFloat(2, 2),
                'width' => fake()->randomFloat(2, 2),
                'type' => 'engine',
                'homeport' => 'hercule'
            ]
        ]);

        $this->assertDatabaseCount('members', 1);
        $this->assertDatabaseCount('member_member_type', 1);
        $this->assertDatabaseCount('contributions', 0);
        $this->assertDatabaseHas('member_member_type', [
            'member_id' => 1,
            'member_type_id' => 2
        ]);
        $this->assertDatabaseCount('boats', 1);
        $this->assertDatabaseHas('boats', [
            'member_id' => 1,
            'homeport_id' => 1,
            'boat_type_id' => 1,
        ]);
    }

    public function test_create_committee(): void
    {
        $this->seed();
        $this->actingAs(Admin::find(1), 'admin');

        $this->post('api/member', [
            'type' => 'committee',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->word(),
                'postal_code' => fake()->word(),
                'city' => fake()->word(),
                'phone' => fake()->word(),
                'job' => fake()->word()
            ],
            'boat' => [
                'name' => fake()->word(),
                'brand' => fake()->word(),
                'model' => fake()->word(),
                'year' => fake()->date(),
                'length' => fake()->randomFloat(2, 2),
                'width' => fake()->randomFloat(2, 2),
                'type' => 'sail',
                'homeport' => 'fontvieille'
            ]
        ]);

        $this->assertDatabaseCount('members', 1);
        $this->assertDatabaseCount('member_member_type', 1);
        $this->assertDatabaseCount('contributions', 0);
        $this->assertDatabaseHas('member_member_type', [
            'member_id' => 1,
            'member_type_id' => 3
        ]);
        $this->assertDatabaseCount('boats', 1);
        $this->assertDatabaseHas('boats', [
            'member_id' => 1,
            'homeport_id' => 2,
            'boat_type_id' => 2,
        ]);
    }

    public function test_w_contribution(): void
    {
        $this->seed();
        $this->actingAs(Admin::find(1), 'admin');
        $contribution = fake()->numberBetween(10, 100);

        $this->post('api/member', [
            'type' => 'supporter',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->word(),
                'postal_code' => fake()->word(),
                'city' => fake()->word(),
                'phone' => fake()->word(),
                'job' => fake()->word()
            ],
            'contribution' => $contribution
        ]);

        $this->assertDatabaseCount('members', 1);
        $this->assertDatabaseCount('member_member_type', 1);
        $this->assertDatabaseHas('member_member_type', [
            'member_id' => 1,
            'member_type_id' => 1
        ]);
        $this->assertDatabaseCount('contributions', 1);
        $this->assertDatabaseHas('contributions', [
            'member_id' => 1,
            'amount' => $contribution
        ]);
    }

    public function test_w_coowner(): void
    {
        $this->seed();
        $this->actingAs(Admin::find(1), 'admin');

        $this->post('api/member', [
            'type' => 'committee',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->word(),
                'postal_code' => fake()->word(),
                'city' => fake()->word(),
                'phone' => fake()->word(),
                'job' => fake()->word()
            ],
            'boat' => [
                'name' => fake()->word(),
                'brand' => fake()->word(),
                'model' => fake()->word(),
                'year' => fake()->date(),
                'length' => fake()->randomFloat(2, 2),
                'width' => fake()->randomFloat(2, 2),
                'type' => 'sail',
                'homeport' => 'fontvieille'
            ],
            'coowner' => [
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'nationality' => fake()->word()
            ]
        ]);

        $this->assertDatabaseCount('members', 1);
        $this->assertDatabaseCount('member_member_type', 1);
        $this->assertDatabaseCount('contributions', 0);
        $this->assertDatabaseCount('boats', 1);
        $this->assertDatabaseCount('coowners', 1);
    }

    public function test_patch_supporter(): void
    {
        $this->seed();
        $this->actingAs(Admin::find(1), 'admin');

        $city = fake()->word();
        $phone = fake()->word();
        $job = fake()->word();

        $this->post('api/member', [
            'type' => 'supporter',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->word(),
                'postal_code' => fake()->word(),
                'city' => $city,
                'phone' => $phone,
                'job' => $job
            ],
        ]);

        $newFirstname = fake()->word();
        $newLastname = fake()->word();

        $this->patch('api/member/1', [
            'member' => [
                'first' => $newFirstname,
                'last' => $newLastname,
            ],
        ]);

        $this->assertDatabaseHas('members', [
            'first' => $newFirstname,
            'last' => $newLastname,
            'city' => $city,
            'phone' => $phone,
            'job' => $job
        ]);
    }

    public function test_patch_infos_and_boat(): void
    {
        $this->seed();
        $this->actingAs(Admin::find(1), 'admin');

        $this->post('api/member', [
            'type' => 'committee',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->word(),
                'postal_code' => fake()->word(),
                'city' => fake()->word(),
                'phone' => fake()->word(),
                'job' => fake()->word()
            ],
            'boat' => [
                'name' => fake()->word(),
                'brand' => fake()->word(),
                'model' => fake()->word(),
                'year' => fake()->date(),
                'length' => fake()->randomFloat(2, 2),
                'width' => fake()->randomFloat(2, 2),
                'type' => 'sail',
                'homeport' => 'fontvieille'
            ],
            'coowner' => [
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'nationality' => fake()->word()
            ]
        ]);

        $newFirstname = fake()->word();
        $newLastname = fake()->word();
        $newBoatName = fake()->word();
        $newBoatBrand = fake()->word();

        $this->patch('api/member/1', [
            'member' => [
                'first' => $newFirstname,
                'last' => $newLastname,
            ],
            'boat' => [
                'name' => $newBoatName,
                'brand' => $newBoatBrand,
            ]
        ]);

        $this->assertDatabaseHas('members', [
            'first' => $newFirstname,
            'last' => $newLastname,
        ]);
        $this->assertDatabaseHas('boats', [
            'name' => $newBoatName,
            'brand' => $newBoatBrand,
        ]);
    }

    public function test_from_supporter_to_active(): void
    {
        $this->seed();
        $this->actingAs(Admin::find(1), 'admin');

        $city = fake()->word();
        $phone = fake()->word();
        $job = fake()->word();

        $this->post('api/member', [
            'type' => 'supporter',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->word(),
                'postal_code' => fake()->word(),
                'city' => $city,
                'phone' => $phone,
                'job' => $job
            ],
        ]);

        $newFirstname = fake()->word();
        $newLastname = fake()->word();

        $this->patch('api/member/1', [
            'type' => 'active',
            'member' => [
                'first' => $newFirstname,
                'last' => $newLastname,
            ],
            'boat' => [
                'name' => fake()->word(),
                'brand' => fake()->word(),
                'model' => fake()->word(),
                'year' => fake()->date(),
                'length' => fake()->randomFloat(2, 2),
                'width' => fake()->randomFloat(2, 2),
                'type' => 'sail',
                'homeport' => 'fontvieille'
            ],
        ]);

        $this->assertDatabaseHas('members', [
            'first' => $newFirstname,
            'last' => $newLastname,
            'city' => $city,
            'phone' => $phone,
            'job' => $job
        ]);
        $this->assertDatabaseCount('boats', 1);
        $this->assertDatabaseCount('member_member_type', 1);
        $this->assertDatabaseHas('member_member_type', [
            'member_id' => 1,
            'member_type_id' => 2
        ]);
    }

    public function test_from_supporter_to_committee_w_coowner(): void
    {
        $this->seed();
        $this->actingAs(Admin::find(1), 'admin');

        $city = fake()->word();
        $phone = fake()->word();
        $job = fake()->word();

        $this->post('api/member', [
            'type' => 'supporter',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->word(),
                'postal_code' => fake()->word(),
                'city' => $city,
                'phone' => $phone,
                'job' => $job
            ],
        ]);

        $newFirstname = fake()->word();
        $newLastname = fake()->word();

        $this->patch('api/member/1', [
            'type' => 'committee',
            'member' => [
                'first' => $newFirstname,
                'last' => $newLastname,
            ],
            'boat' => [
                'name' => fake()->word(),
                'brand' => fake()->word(),
                'model' => fake()->word(),
                'year' => fake()->date(),
                'length' => fake()->randomFloat(2, 2),
                'width' => fake()->randomFloat(2, 2),
                'type' => 'sail',
                'homeport' => 'fontvieille'
            ],
            'coowner' => [
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'nationality' => fake()->word()
            ]
        ]);

        $this->assertDatabaseHas('members', [
            'first' => $newFirstname,
            'last' => $newLastname,
            'city' => $city,
            'phone' => $phone,
            'job' => $job
        ]);
        $this->assertDatabaseCount('boats', 1);
        $this->assertDatabaseCount('member_member_type', 1);
        $this->assertDatabaseHas('member_member_type', [
            'member_id' => 1,
            'member_type_id' => 3
        ]);
        $this->assertDatabaseCount('coowners', 1);
    }

    public function test_from_active_to_committee(): void
    {
        $this->seed();
        $this->actingAs(Admin::find(1), 'admin');

        $this->post('api/member', [
            'type' => 'active',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->word(),
                'postal_code' => fake()->word(),
                'city' => fake()->word(),
                'phone' => fake()->word(),
                'job' => fake()->word()
            ],
            'boat' => [
                'name' => fake()->word(),
                'brand' => fake()->word(),
                'model' => fake()->word(),
                'year' => fake()->date(),
                'length' => fake()->randomFloat(2, 2),
                'width' => fake()->randomFloat(2, 2),
                'type' => 'sail',
                'homeport' => 'fontvieille'
            ],
        ]);

        $this->assertDatabaseHas('member_member_type', [
            'member_id' => 1,
            'member_type_id' => 2
        ]);

        $this->patch('api/member/1', [
            'type' => 'committee',
        ]);

        $this->assertDatabaseCount('boats', 1);
        $this->assertDatabaseHas('member_member_type', [
            'member_id' => 1,
            'member_type_id' => 3
        ]);
    }

    public function test_active_add_coowner(): void
    {
        $this->seed();
        $this->actingAs(Admin::find(1), 'admin');

        $this->post('api/member', [
            'type' => 'active',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->word(),
                'postal_code' => fake()->word(),
                'city' => fake()->word(),
                'phone' => fake()->word(),
                'job' => fake()->word()
            ],
            'boat' => [
                'name' => fake()->word(),
                'brand' => fake()->word(),
                'model' => fake()->word(),
                'year' => fake()->date(),
                'length' => fake()->randomFloat(2, 2),
                'width' => fake()->randomFloat(2, 2),
                'type' => 'sail',
                'homeport' => 'fontvieille'
            ],
        ]);

        $this->patch('api/member/1', [
            'coowner' => [
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'nationality' => fake()->word()
            ]
        ]);

        $this->assertDatabaseCount('boats', 1);
        $this->assertDatabaseCount('member_member_type', 1);
        $this->assertDatabaseHas('member_member_type', [
            'member_id' => 1,
            'member_type_id' => 2
        ]);
        $this->assertDatabaseCount('coowners', 1);
    }

    public function test_active_patch_coowner(): void
    {
        $this->seed();
        $this->actingAs(Admin::find(1), 'admin');

        $this->post('api/member', [
            'type' => 'active',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->word(),
                'postal_code' => fake()->word(),
                'city' => fake()->word(),
                'phone' => fake()->word(),
                'job' => fake()->word()
            ],
            'boat' => [
                'name' => fake()->word(),
                'brand' => fake()->word(),
                'model' => fake()->word(),
                'year' => fake()->date(),
                'length' => fake()->randomFloat(2, 2),
                'width' => fake()->randomFloat(2, 2),
                'type' => 'sail',
                'homeport' => 'fontvieille'
            ],
            'coowner' => [
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'nationality' => fake()->word()
            ]
        ]);

        $newFirstname = fake()->word();
        $newLastname = fake()->word();
        $newNatio = fake()->word();

        $this->patch('api/member/1', [
            'coowner' => [
                'first' => $newFirstname,
                'last' => $newLastname,
                'nationality' => $newNatio
            ]
        ]);

        $this->assertDatabaseCount('boats', 1);
        $this->assertDatabaseCount('member_member_type', 1);
        $this->assertDatabaseHas('member_member_type', [
            'member_id' => 1,
            'member_type_id' => 2
        ]);
        $this->assertDatabaseHas('coowners', [
            'first' => $newFirstname,
            'last' => $newLastname,
            'nationality' => $newNatio
        ]);
    }

    public function test_delete_coowner(): void
    {
        $this->seed();
        $this->actingAs(Admin::find(1), 'admin');

        $this->post('api/member', [
            'type' => 'active',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->word(),
                'postal_code' => fake()->word(),
                'city' => fake()->word(),
                'phone' => fake()->word(),
                'job' => fake()->word()
            ],
            'boat' => [
                'name' => fake()->word(),
                'brand' => fake()->word(),
                'model' => fake()->word(),
                'year' => fake()->date(),
                'length' => fake()->randomFloat(2, 2),
                'width' => fake()->randomFloat(2, 2),
                'type' => 'sail',
                'homeport' => 'fontvieille'
            ],
            'coowner' => [
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'nationality' => fake()->word()
            ]
        ]);

        $this->assertDatabaseCount('coowners', 1);

        $this->patch('api/member/1', [
            'delete-coowner' => true
        ]);

        $this->assertDatabaseCount('boats', 1);
        $this->assertDatabaseCount('member_member_type', 1);
        $this->assertDatabaseHas('member_member_type', [
            'member_id' => 1,
            'member_type_id' => 2
        ]);
        $this->assertDatabaseCount('coowners', 0);
    }

    public function test_from_committee_w_coowner_to_supporter(): void
    {
        $this->seed();
        $this->actingAs(Admin::find(1), 'admin');

        $this->post('api/member', [
            'type' => 'committee',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->word(),
                'postal_code' => fake()->word(),
                'city' => fake()->word(),
                'phone' => fake()->word(),
                'job' => fake()->word()
            ],
            'boat' => [
                'name' => fake()->word(),
                'brand' => fake()->word(),
                'model' => fake()->word(),
                'year' => fake()->date(),
                'length' => fake()->randomFloat(2, 2),
                'width' => fake()->randomFloat(2, 2),
                'type' => 'sail',
                'homeport' => 'fontvieille'
            ],
            'coowner' => [
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'nationality' => fake()->word()
            ]
        ]);

        $this->assertDatabaseHas('member_member_type', [
            'member_id' => 1,
            'member_type_id' => 3
        ]);

        $this->patch('api/member/1', [
            'type' => 'supporter',
        ]);

        $this->assertDatabaseCount('boats', 0);
        $this->assertDatabaseCount('coowners', 0);
        $this->assertDatabaseHas('member_member_type', [
            'member_id' => 1,
            'member_type_id' => 1
        ]);
    }

    public function test_add_contribution(): void
    {
        $this->seed();
        $this->actingAs(Admin::find(1), 'admin');

        $this->post('api/member', [
            'type' => 'supporter',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->word(),
                'postal_code' => fake()->word(),
                'city' => fake()->word(),
                'phone' => fake()->word(),
                'job' => fake()->word()
            ],
        ]);

        $this->assertDatabaseCount('contributions', 0);

        $this->patch('api/member/1', [
            'contribution' => 80
        ]);

        $this->assertDatabaseHas('contributions', [
            'member_id' => 1,
            'amount' => 80
        ]);
    }

    public function test_update_contribution(): void
    {
        $this->seed();
        $this->actingAs(Admin::find(1), 'admin');

        $this->post('api/member', [
            'type' => 'supporter',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->word(),
                'postal_code' => fake()->word(),
                'city' => fake()->word(),
                'phone' => fake()->word(),
                'job' => fake()->word()
            ],
            'contribution' => 80
        ]);

        $this->assertDatabaseCount('contributions', 1);
        $this->assertDatabaseHas('contributions', [
            'member_id' => 1,
            'amount' => 80,
            'created_at' => now()
        ]);

        $this->patch('api/member/1', [
            'contribution' => 180
        ]);

        $this->assertDatabaseCount('contributions', 1);
        $this->assertDatabaseHas('contributions', [
            'member_id' => 1,
            'amount' => 180,
            'created_at' => now()
        ]);
    }

    public function test_new_year_add_null_contribution_if_there_was_none(): void
    {
        // $this->seed();

        // $this->post('api/member', [
        //     'type' => 'supporter',
        //     'member' => [
        //         'email' => fake()->email(),
        //         'first' => fake()->firstName(),
        //         'last' => fake()->lastName(),
        //         'birthdate' => fake()->date(),
        //         'address' => fake()->word(),
        //         'postal_code' => fake()->word(),
        //         'city' => fake()->word(),
        //         'phone' => fake()->word(),
        //         'job' => fake()->word()
        //     ],
        // ]);

        // $this->assertDatabaseCount('contributions', 0);

        // $this->travel(1)->year();

        // $this->assertDatabaseHas('contributions', [
        //     'member_id' => 1,
        //     'amount' => null,
        //     'created_at' => now()->subYear()
        // ]);
    }

    public function test_delete_supporter(): void
    {
        $this->seed();
        $this->actingAs(Admin::find(1), 'admin');

        $this->post('api/member', [
            'type' => 'supporter',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->word(),
                'postal_code' => fake()->word(),
                'city' => fake()->word(),
                'phone' => fake()->word(),
                'job' => fake()->word()
            ],
        ]);

        $this->assertDatabaseCount('members', 1);
        $this->assertDatabaseCount('member_member_type', 1);

        $this->delete('api/member/1');

        $this->assertDatabaseCount('members', 0);
        $this->assertDatabaseCount('member_member_type', 0);
    }

    public function test_delete_active_w_coowner(): void
    {
        $this->seed();
        $this->actingAs(Admin::find(1), 'admin');

        $this->post('api/member', [
            'type' => 'active',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->word(),
                'postal_code' => fake()->word(),
                'city' => fake()->word(),
                'phone' => fake()->word(),
                'job' => fake()->word()
            ],
            'boat' => [
                'name' => fake()->word(),
                'brand' => fake()->word(),
                'model' => fake()->word(),
                'year' => fake()->date(),
                'length' => fake()->randomFloat(2, 2),
                'width' => fake()->randomFloat(2, 2),
                'type' => 'sail',
                'homeport' => 'fontvieille'
            ],
            'coowner' => [
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'nationality' => fake()->word()
            ]
        ]);

        $this->assertDatabaseCount('members', 1);
        $this->assertDatabaseCount('member_member_type', 1);
        $this->assertDatabaseCount('boats', 1);
        $this->assertDatabaseCount('coowners', 1);
        $this->assertDatabaseCount('homeports', 3);
        $this->assertDatabaseCount('boat_types', 2);

        $this->delete('api/member/1');

        $this->assertDatabaseCount('members', 0);
        $this->assertDatabaseCount('member_member_type', 0);
        $this->assertDatabaseCount('boats', 0);
        $this->assertDatabaseCount('coowners', 0);
        $this->assertDatabaseCount('homeports', 3);
        $this->assertDatabaseCount('boat_types', 2);
    }

    public function test_delete_committee_w_coowner_and_contribution(): void
    {
        $this->seed();
        $this->actingAs(Admin::find(1), 'admin');

        $this->post('api/member', [
            'type' => 'committee',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->word(),
                'postal_code' => fake()->word(),
                'city' => fake()->word(),
                'phone' => fake()->word(),
                'job' => fake()->word()
            ],
            'boat' => [
                'name' => fake()->word(),
                'brand' => fake()->word(),
                'model' => fake()->word(),
                'year' => fake()->date(),
                'length' => fake()->randomFloat(2, 2),
                'width' => fake()->randomFloat(2, 2),
                'type' => 'sail',
                'homeport' => 'fontvieille'
            ],
            'coowner' => [
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'nationality' => fake()->word()
            ],
            'contribution' => 80
        ]);

        $this->assertDatabaseCount('members', 1);
        $this->assertDatabaseCount('member_member_type', 1);
        $this->assertDatabaseCount('boats', 1);
        $this->assertDatabaseCount('coowners', 1);
        $this->assertDatabaseCount('contributions', 1);

        $this->assertDatabaseCount('homeports', 3);
        $this->assertDatabaseCount('boat_types', 2);

        $this->delete('api/member/1');

        $this->assertDatabaseCount('members', 0);
        $this->assertDatabaseCount('member_member_type', 0);
        $this->assertDatabaseCount('boats', 0);
        $this->assertDatabaseCount('coowners', 0);
        $this->assertDatabaseCount('contributions', 0);

        $this->assertDatabaseCount('homeports', 3);
        $this->assertDatabaseCount('boat_types', 2);
    }

    public function test_create_supporter_w_pw_reset(): void
    {
        $this->seed();
        $this->actingAs(Admin::find(1), 'admin');
        Notification::fake();

        $memberEmail = fake()->email();
        $this->post('api/member', [
            'type' => 'supporter',
            'member' => [
                'email' => $memberEmail,
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->word(),
                'postal_code' => fake()->word(),
                'city' => fake()->word(),
                'phone' => fake()->word(),
                'job' => fake()->word()
            ],
            'welcome' => true
        ]);

        $this->assertDatabaseCount('members', 1);
        $this->assertDatabaseCount('member_member_type', 1);
        $this->assertDatabaseHas('member_member_type', [
            'member_id' => 1,
            'member_type_id' => 1
        ]);
        $this->assertDatabaseCount('contributions', 0);

        Notification::assertSentTo([Member::find(1)], ResetPassword::class);
    }

    public function test_send_pw_reset(): void
    {
        $this->seed();
        $this->actingAs(Admin::find(1), 'admin');
        Notification::fake();

        $memberEmail = fake()->email();
        $this->post('api/member', [
            'type' => 'supporter',
            'member' => [
                'email' => $memberEmail,
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->word(),
                'postal_code' => fake()->word(),
                'city' => fake()->word(),
                'phone' => fake()->word(),
                'job' => fake()->word()
            ],
        ]);

        $this->assertDatabaseCount('members', 1);

        $this->get('api/member/1/welcome');

        Notification::assertSentTo([Member::find(1)], ResetPassword::class);
    }
}
