<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_via_profile_page_patch_member_infos(): void
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
        $this->assertDatabaseCount('contributions', 0);
        $this->assertDatabaseCount('boats', 0);
        $this->assertDatabaseCount('coowners', 0);

        $this->actingAs(Member::find(1), 'member');

        $first = fake()->firstName();
        $last = fake()->lastName();
        $birthdate = fake()->date();
        $address = fake()->word();
        $this->patch('profile/1/infos', [
            'first' => $first,
            'last' => $last,
            'birthdate' => $birthdate,
            'address' => $address,
        ]);

        $this->assertDatabaseHas('members', [
            'first' => $first,
            'last' => $last,
            'birthdate' => $birthdate,
            'address' => $address,
        ]);
    }

    public function test_via_profile_page_patch_email(): void
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
        $this->assertDatabaseCount('contributions', 0);
        $this->assertDatabaseCount('boats', 0);
        $this->assertDatabaseCount('coowners', 0);

        $this->actingAs(Member::find(1), 'member');

        $email = fake()->email();
        $this->post('profile/1/email', [
            'email' => $email,
        ]);

        $this->assertDatabaseHas('members', [
            'email' => $email,
        ]);
    }

    public function test_via_profile_page_update_password(): void
    {
        $this->seed();
        $currentPw = fake()->password(10);
        $member = Member::create([
            'email' => fake()->email(),
            'password' => Hash::make($currentPw),
            'first' => fake()->firstName(),
            'last' => fake()->lastName(),
            'birthdate' => fake()->date(),
            'address' => fake()->word(),
            'postal_code' => fake()->word(),
            'city' => fake()->word(),
            'phone' => fake()->word(),
            'job' => fake()->word(),
            'pending' => false
        ]);

        $this->assertDatabaseCount('members', 1);
        $this->assertDatabaseCount('contributions', 0);
        $this->assertDatabaseCount('boats', 0);
        $this->assertDatabaseCount('coowners', 0);

        $this->actingAs(Member::find(1), 'member');

        $pw = fake()->password(10);
        $this->post('profile/1/password', [
            'current_password' => $currentPw,
            'password' => $pw,
            'password_confirmation' => $pw
        ]);

        $this->assertTrue(Hash::check($pw, $member->fresh()->password));
    }

    public function test_member_cannot_update_password_with_invalid_current_password(): void
    {
        $this->seed();
        $member = Member::create([
            'email' => fake()->email(),
            'password' => Hash::make(fake()->password(10)),
            'first' => fake()->firstName(),
            'last' => fake()->lastName(),
            'birthdate' => fake()->date(),
            'address' => fake()->word(),
            'postal_code' => fake()->word(),
            'city' => fake()->word(),
            'phone' => fake()->word(),
            'job' => fake()->word(),
            'pending' => false
        ]);

        $response = $this->actingAs($member, 'member')->post('profile/1/password', [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('current_password');
    }

    public function test_via_profile_page_patch_boat(): void
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
        $this->assertDatabaseCount('boats', 1);
        $this->assertDatabaseCount('coowners', 0);

        $this->actingAs(Member::find(1), 'member');

        $name = fake()->word();
        $brand = fake()->word();
        $model = fake()->word();
        $this->patch('profile/1/boat', [
            'name' => $name,
            'brand' => $brand,
            'model' => $model,
            'type' => 'sail',
            'homeport' => 'fontvieille'
        ]);

        $this->assertDatabaseHas('boats', [
            'name' => $name,
            'brand' => $brand,
            'model' => $model,
            'boat_type_id' => 2,
            'homeport_id' => 2
        ]);
    }

    public function test_via_profile_page_patch_coowner(): void
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
            ],
            'coowner' => [
                'first' => fake()->firstName(),
                'last' => fake()->lastName(),
                'nationality' => fake()->word()
            ]
        ]);
        $this->assertDatabaseCount('members', 1);
        $this->assertDatabaseCount('boats', 1);
        $this->assertDatabaseCount('coowners', 1);

        $this->actingAs(Member::find(1), 'member');

        $first = fake()->word();
        $last = fake()->word();
        $nationality = fake()->word();
        $this->patch('profile/1/coowner', [
            'first' => $first,
            'last' => $last,
            'nationality' => $nationality,
        ]);

        $this->assertDatabaseHas('coowners', [
            'first' => $first,
            'last' => $last,
            'nationality' => $nationality,
        ]);
    }

    public function test_via_profile_page_add_boat_then_delete(): void
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
        $this->assertDatabaseCount('boats', 0);
        $this->assertDatabaseCount('coowners', 0);
        $this->assertDatabaseHas('member_member_type', [
            'member_id' => 1,
            'member_type_id' => 1
        ]);

        $this->actingAs(Member::find(1), 'member');

        $this->post('profile/1/boat', [
            'name' => fake()->word(),
            'brand' => fake()->word(),
            'model' => fake()->word(),
            'year' => fake()->date(),
            'length' => fake()->randomFloat(2, 2),
            'width' => fake()->randomFloat(2, 2),
            'type' => 'sail',
            'homeport' => 'fontvieille'
        ]);

        $this->assertDatabaseCount('boats', 1);
        $this->assertDatabaseCount('member_member_type', 1);
        $this->assertDatabaseHas('member_member_type', [
            'member_id' => 1,
            'member_type_id' => 2
        ]);

        $this->delete('profile/1/boat');

        $this->assertDatabaseCount('boats', 0);
        $this->assertDatabaseCount('coowners', 0);
        $this->assertDatabaseCount('member_member_type', 1);
        $this->assertDatabaseHas('member_member_type', [
            'member_id' => 1,
            'member_type_id' => 1
        ]);
    }

    public function test_via_profile_page_add_coowner_then_delete(): void
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
            ]
        ]);
        $this->assertDatabaseCount('members', 1);
        $this->assertDatabaseCount('boats', 1);
        $this->assertDatabaseCount('coowners', 0);
        $this->assertDatabaseHas('member_member_type', [
            'member_id' => 1,
            'member_type_id' => 2
        ]);

        $this->actingAs(Member::find(1), 'member');

        $this->post('profile/1/coowner', [
            'first' => fake()->firstName(),
            'last' => fake()->lastName(),
            'nationality' => fake()->word()
        ]);

        $this->assertDatabaseCount('boats', 1);
        $this->assertDatabaseCount('coowners', 1);

        $this->delete('profile/1/coowner');

        $this->assertDatabaseCount('boats', 1);
        $this->assertDatabaseCount('coowners', 0);
    }
}
