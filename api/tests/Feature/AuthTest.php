<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_authed_admin_can_go_through_administration(): void
    {
        $this->seed();

        $simpleAdmin = Admin::find(1);

        $title = fake()->sentence();
        $do = $this->actingAs($simpleAdmin, 'admin')->patch('api/tab/1', [
            'title' => $title
        ]);

        $do->assertOk();
        $this->assertDatabaseHas('tabs', [
            'id' => 1,
            'title' => $title,
        ]);
    }

    public function test_authed_member_can_go_through_his_profile(): void
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

        $member = Member::find(1);

        $do = $this->actingAs($member, 'member')->get('profil');
        $do->assertOk();
    }
}
