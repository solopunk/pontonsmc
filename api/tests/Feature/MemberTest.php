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

        $c = $this->post('api/member', [
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

        // dd($c);

        $this->assertDatabaseCount('members', 1);
        $this->assertDatabaseCount('member_member_type', 1);
        $this->assertDatabaseCount('contributions', 1);
        $this->assertDatabaseCount('boats', 1);
        $this->assertDatabaseCount('coowners', 1);
    }
}
