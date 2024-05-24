<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Admin;
use App\Models\Member;
use App\Models\MemberType;
use App\Models\Scoop;
use Tests\TestCase;

class ExampleTest extends TestCase
{

    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    // public function test_admin_creation(): void
    // {
    //     Admin::create([
    //         'email' => 'admin@example.com',
    //         'pw' => 'admin'
    //     ]);

    //     $this->assertDatabaseCount('admins', 1);
    // }

    // public function test_scoop_creation(): void
    // {
    //     Scoop::create([
    //         'title' => fake()->title(),
    //         'content_json' => fake()->text(),
    //         'content_html' => fake()->text(),
    //     ]);

    //     $this->assertDatabaseCount('admins', 0);
    //     $this->assertDatabaseCount('scoops', 1);
    // }

    // public function test_member_creation(): void
    // {
    //     $member = Member::create([
    //         'email' => fake()->email(),
    //         'first' => fake()->firstName(),
    //         'last' => fake()->lastName(),
    //         'birthdate' => fake()->date(),
    //         'address' => fake()->address(),
    //         'postal_code' => fake()->postcode(),
    //         'city' => fake()->city(),
    //         'phone' => fake()->phoneNumber(),
    //         'job' => fake()->jobTitle()
    //     ]);

    //     $type = MemberType::create([
    //         'name' => 'Actif',
    //         'uid' => 'active'
    //     ]);

    //     $member->member_types()->attach(1);

    //     $this->assertDatabaseCount('member_types', 1);
    //     $this->assertDatabaseCount('members', 1);
    //     $this->assertDatabaseCount('member_member_type', 1);
    // }

    // public function test_api_route_work(): void
    // {
    //     $resp = $this->get('api/member');

    //     $resp->assertOk();
    // }
}
