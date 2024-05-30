<?php

namespace Tests\Feature;

use App\Mail\DeclineRequestor;
use App\Models\Admin;
use App\Models\Member;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AdhesionTest extends TestCase
{
    use RefreshDatabase;

    public function test_unknown_request_an_adhesion_for_supporter(): void
    {
        $this->seed();

        $this->post('request-adhesion', [
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

        $this->post('request-adhesion', [
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
        Notification::fake();

        $this->post('request-adhesion', [
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

        $this->actingAs(Admin::find(1), 'admin');

        $this->get('api/accept-adhesion/1');

        $this->assertDatabaseHas('members', [
            'pending' => false
        ]);

        Notification::assertSentTo([Member::find(1)], ResetPassword::class);
    }

    public function test_decline_adhesion(): void
    {
        $this->seed();
        Mail::fake();

        $requestorMail = fake()->email();
        $this->post('request-adhesion', [
            'type' => 'active',
            'member' => [
                'email' => $requestorMail,
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

        $this->actingAs(Admin::find(1), 'admin');

        $this->get('api/decline-adhesion/1');

        $this->assertDatabaseCount('members', 0);
        $this->assertDatabaseCount('member_member_type', 0);
        $this->assertDatabaseCount('boats', 0);
        $this->assertDatabaseCount('coowners', 0);

        Mail::assertSent(DeclineRequestor::class, function (DeclineRequestor $mail) use ($requestorMail) {
            return
                $mail->hasFrom('flobono@me.com') &&
                $mail->hasTo($requestorMail);
        });
    }

    public function test_member_submit_password(): void
    {
        $this->seed();

        $email = fake()->email();
        $member = Member::create([
            'email' => $email,
            'first' => fake()->firstName(),
            'last' => fake()->lastName(),
            'birthdate' => fake()->date(),
            'address' => fake()->address(),
            'postal_code' => fake()->postcode(),
            'city' => fake()->city(),
            'phone' => fake()->phoneNumber(),
            'job' => fake()->jobTitle(),
            'pending' => false
        ]);

        $token = Password::createToken($member);

        Event::fake();

        $pw = fake()->password(10);

        $do = $this->post('submit-password', [
            'email' => $email,
            'password' => $pw,
            'password_confirmation' => $pw,
            'token' => $token
        ]);

        $do->assertRedirect('/login');
        $this->assertTrue(Hash::check($pw, Member::first()->password));

        Event::assertDispatched(PasswordReset::class);
    }

    public function test_forgot_pw_func_for_member(): void
    {

        $this->seed();
        Notification::fake();
        $email = fake()->email();

        $member = Member::create([
            'email' => $email,
            'password' => fake()->password(10),
            'first' => fake()->firstName(),
            'last' => fake()->lastName(),
            'birthdate' => fake()->date(),
            'address' => fake()->address(),
            'postal_code' => fake()->postcode(),
            'city' => fake()->city(),
            'phone' => fake()->phoneNumber(),
            'job' => fake()->jobTitle(),
            'pending' => false
        ]);

        $do = $this->post('forgot-password', [
            'email' => $email
        ]);

        $do->assertOk();

        Notification::assertSentTo([$member], ResetPassword::class);
    }
}
