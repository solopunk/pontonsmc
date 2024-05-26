<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdhesionTest extends TestCase
{
    use RefreshDatabase;

    public function test_unknown_request_an_adhesion_for_supporter(): void
    {
        $this->seed();

        $this->post('do/request-adhesion', [
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

        $this->assertDatabaseHas('members', [
            'pending' => true
        ]);
        $this->assertDatabaseCount('member_member_type', 1);
        $this->assertDatabaseCount('boats', 0);
        $this->assertDatabaseCount('coowners', 0);
        $this->assertDatabaseCount('contributions', 0);
    }

    public function test_unknown_request_an_adhesion_for_active(): void
    {
        $this->seed();

        $this->post('do/request-adhesion', [
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
            ],
        ]);

        $this->assertDatabaseHas('members', [
            'pending' => true
        ]);
        $this->assertDatabaseCount('member_member_type', 1);
        $this->assertDatabaseCount('boats', 1);
        $this->assertDatabaseCount('coowners', 1);
        $this->assertDatabaseCount('contributions', 0);
    }

    public function test_accept_adhesion(): void
    {
        $this->seed();

        $this->post('do/request-adhesion', [
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

        $this->assertDatabaseHas('members', [
            'pending' => true
        ]);

        $this->get('api/accept-adhesion/1');

        $this->assertDatabaseHas('members', [
            'pending' => false
        ]);
    }

    public function test_decline_adhesion(): void
    {
        $this->seed();

        $this->post('do/request-adhesion', [
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
            ],
        ]);

        $this->assertDatabaseHas('members', [
            'pending' => true
        ]);
        $this->assertDatabaseCount('member_member_type', 1);
        $this->assertDatabaseCount('boats', 1);
        $this->assertDatabaseCount('coowners', 1);

        $this->get('api/decline-adhesion/1');

        $this->assertDatabaseCount('members', 0);
        $this->assertDatabaseCount('member_member_type', 0);
        $this->assertDatabaseCount('boats', 0);
        $this->assertDatabaseCount('coowners', 0);
    }
}
