<?php

namespace Database\Factories;

use App\Models\Boat;
use App\Models\Coowner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coowner>
 */
class CoownerFactory extends Factory
{
    protected $model = Coowner::class;

    public function definition()
    {
        return [
            'boat_id' => Boat::factory(),
            'first' => $this->faker->firstName,
            'last' => $this->faker->lastName,
            'nationality' => $this->faker->country,
        ];
    }
}
