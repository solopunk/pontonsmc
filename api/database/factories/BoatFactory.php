<?php

namespace Database\Factories;

use App\Models\Boat;
use App\Models\BoatType;
use App\Models\Homeport;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Boat>
 */
class BoatFactory extends Factory
{
    protected $model = Boat::class;

    public function definition()
    {
        return [
            'member_id' => Member::inRandomOrder()->first()->id,
            'homeport_id' => Homeport::inRandomOrder()->first()->id,
            'boat_type_id' => BoatType::inRandomOrder()->first()->id,
            'name' => $this->faker->word,
            'brand' => $this->faker->company,
            'model' => $this->faker->word,
            'year' => $this->faker->date,
            'length' => $this->faker->randomFloat(2, 5, 30),
            'width' => $this->faker->randomFloat(2, 2, 10),
        ];
    }
}
