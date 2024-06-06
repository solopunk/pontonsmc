<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Member>
 */
class MemberFactory extends Factory
{
    protected $model = Member::class;

    public function definition()
    {
        return [
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('password'), // ou Hash::make($this->faker->password)
            'first' => $this->faker->firstName,
            'last' => $this->faker->lastName,
            'birthdate' => $this->faker->date,
            'address' => $this->faker->address,
            'postal_code' => $this->faker->postcode,
            'city' => $this->faker->city,
            'phone' => $this->faker->phoneNumber,
            'job' => $this->faker->jobTitle,
            'pending' => $this->faker->boolean,
        ];
    }
}
