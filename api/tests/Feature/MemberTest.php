<?php

namespace Tests\Feature;

use App\Models\Admin;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_supporter(): void
    {
        $this->seed();

        $this->post('api/member', [
            'type' => 'supporter',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->address(),
                'postal_code' => fake()->postcode(),
                'city' => fake()->city(),
                'phone' => fake()->phoneNumber(),
                'job' => fake()->jobTitle()
            ]
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
            'amount' => null
        ]);
    }

    public function test_create_active(): void
    {
        $this->seed();

        $this->post('api/member', [
            'type' => 'active',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->address(),
                'postal_code' => fake()->postcode(),
                'city' => fake()->city(),
                'phone' => fake()->phoneNumber(),
                'job' => fake()->jobTitle()
            ],
            'boat' => [
                'name' => fake()->word(),
                'brand' => fake()->word(),
                'model' => fake()->word(),
                'year' => fake()->date(),
                'length' => fake()->randomFloat(),
                'width' => fake()->randomFloat(),
                'type' => 'engine',
                'homeport' => 'hercule'
            ]
        ]);

        $this->assertDatabaseCount('members', 1);
        $this->assertDatabaseCount('member_member_type', 1);
        $this->assertDatabaseCount('contributions', 1);
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

        $this->post('api/member', [
            'type' => 'committee',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->address(),
                'postal_code' => fake()->postcode(),
                'city' => fake()->city(),
                'phone' => fake()->phoneNumber(),
                'job' => fake()->jobTitle()
            ],
            'boat' => [
                'name' => fake()->word(),
                'brand' => fake()->word(),
                'model' => fake()->word(),
                'year' => fake()->date(),
                'length' => fake()->randomFloat(),
                'width' => fake()->randomFloat(),
                'type' => 'sail',
                'homeport' => 'fontvieille'
            ]
        ]);

        $this->assertDatabaseCount('members', 1);
        $this->assertDatabaseCount('member_member_type', 1);
        $this->assertDatabaseCount('contributions', 1);
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
        $contribution = fake()->numberBetween(10, 100);

        $this->post('api/member', [
            'type' => 'supporter',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->address(),
                'postal_code' => fake()->postcode(),
                'city' => fake()->city(),
                'phone' => fake()->phoneNumber(),
                'job' => fake()->jobTitle()
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

    public function test_contribution_null_fallback(): void
    {
        $this->seed();

        $this->post('api/member', [
            'type' => 'supporter',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->address(),
                'postal_code' => fake()->postcode(),
                'city' => fake()->city(),
                'phone' => fake()->phoneNumber(),
                'job' => fake()->jobTitle()
            ],
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
            'amount' => null
        ]);
    }

    public function test_w_coowner(): void
    {
        $this->seed();

        $this->post('api/member', [
            'type' => 'committee',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->address(),
                'postal_code' => fake()->postcode(),
                'city' => fake()->city(),
                'phone' => fake()->phoneNumber(),
                'job' => fake()->jobTitle()
            ],
            'boat' => [
                'name' => fake()->word(),
                'brand' => fake()->word(),
                'model' => fake()->word(),
                'year' => fake()->date(),
                'length' => fake()->randomFloat(),
                'width' => fake()->randomFloat(),
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
        $this->assertDatabaseCount('contributions', 1);
        $this->assertDatabaseCount('boats', 1);
        $this->assertDatabaseCount('coowners', 1);
    }

    public function test_patch_supporter(): void
    {
        $this->seed();

        $city = fake()->city();
        $phone = fake()->phoneNumber();
        $job = fake()->jobTitle();

        $this->post('api/member', [
            'type' => 'supporter',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->address(),
                'postal_code' => fake()->postcode(),
                'city' => $city,
                'phone' => $phone,
                'job' => $job
            ],
        ]);

        $newFirstname = fake()->firstName();
        $newLastname = fake()->lastName();

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

    public function test_patch_boat(): void
    {
        $this->seed();

        $this->post('api/member', [
            'type' => 'committee',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->address(),
                'postal_code' => fake()->postcode(),
                'city' => fake()->city(),
                'phone' => fake()->phoneNumber(),
                'job' => fake()->jobTitle()
            ],
            'boat' => [
                'name' => fake()->word(),
                'brand' => fake()->word(),
                'model' => fake()->word(),
                'year' => fake()->date(),
                'length' => fake()->randomFloat(),
                'width' => fake()->randomFloat(),
                'type' => 'sail',
                'homeport' => 'fontvieille'
            ],
            'coowner' => [
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'nationality' => fake()->word()
            ]
        ]);

        $newFirstname = fake()->firstName();
        $newLastname = fake()->lastName();
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

        $city = fake()->city();
        $phone = fake()->phoneNumber();
        $job = fake()->jobTitle();

        $this->post('api/member', [
            'type' => 'supporter',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->address(),
                'postal_code' => fake()->postcode(),
                'city' => $city,
                'phone' => $phone,
                'job' => $job
            ],
        ]);

        $newFirstname = fake()->firstName();
        $newLastname = fake()->lastName();

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
                'length' => fake()->randomFloat(),
                'width' => fake()->randomFloat(),
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

        $city = fake()->city();
        $phone = fake()->phoneNumber();
        $job = fake()->jobTitle();

        $this->post('api/member', [
            'type' => 'supporter',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->address(),
                'postal_code' => fake()->postcode(),
                'city' => $city,
                'phone' => $phone,
                'job' => $job
            ],
        ]);

        $newFirstname = fake()->firstName();
        $newLastname = fake()->lastName();

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
                'length' => fake()->randomFloat(),
                'width' => fake()->randomFloat(),
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

        $this->post('api/member', [
            'type' => 'active',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->address(),
                'postal_code' => fake()->postcode(),
                'city' => fake()->city(),
                'phone' => fake()->phoneNumber(),
                'job' => fake()->jobTitle()
            ],
            'boat' => [
                'name' => fake()->word(),
                'brand' => fake()->word(),
                'model' => fake()->word(),
                'year' => fake()->date(),
                'length' => fake()->randomFloat(),
                'width' => fake()->randomFloat(),
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

        $this->post('api/member', [
            'type' => 'active',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->address(),
                'postal_code' => fake()->postcode(),
                'city' => fake()->city(),
                'phone' => fake()->phoneNumber(),
                'job' => fake()->jobTitle()
            ],
            'boat' => [
                'name' => fake()->word(),
                'brand' => fake()->word(),
                'model' => fake()->word(),
                'year' => fake()->date(),
                'length' => fake()->randomFloat(),
                'width' => fake()->randomFloat(),
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

        $this->post('api/member', [
            'type' => 'active',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->address(),
                'postal_code' => fake()->postcode(),
                'city' => fake()->city(),
                'phone' => fake()->phoneNumber(),
                'job' => fake()->jobTitle()
            ],
            'boat' => [
                'name' => fake()->word(),
                'brand' => fake()->word(),
                'model' => fake()->word(),
                'year' => fake()->date(),
                'length' => fake()->randomFloat(),
                'width' => fake()->randomFloat(),
                'type' => 'sail',
                'homeport' => 'fontvieille'
            ],
            'coowner' => [
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'nationality' => fake()->word()
            ]
        ]);

        $newFirstname = fake()->firstName();
        $newLastname = fake()->lastName();
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

        $this->post('api/member', [
            'type' => 'active',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->address(),
                'postal_code' => fake()->postcode(),
                'city' => fake()->city(),
                'phone' => fake()->phoneNumber(),
                'job' => fake()->jobTitle()
            ],
            'boat' => [
                'name' => fake()->word(),
                'brand' => fake()->word(),
                'model' => fake()->word(),
                'year' => fake()->date(),
                'length' => fake()->randomFloat(),
                'width' => fake()->randomFloat(),
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

        $this->post('api/member', [
            'type' => 'committee',
            'member' => [
                'email' => fake()->email(),
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'birthdate' => fake()->date(),
                'address' => fake()->address(),
                'postal_code' => fake()->postcode(),
                'city' => fake()->city(),
                'phone' => fake()->phoneNumber(),
                'job' => fake()->jobTitle()
            ],
            'boat' => [
                'name' => fake()->word(),
                'brand' => fake()->word(),
                'model' => fake()->word(),
                'year' => fake()->date(),
                'length' => fake()->randomFloat(),
                'width' => fake()->randomFloat(),
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
}
