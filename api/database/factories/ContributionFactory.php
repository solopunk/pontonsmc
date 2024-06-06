<?php

namespace Database\Factories;

use App\Models\Contribution;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contribution>
 */
class ContributionFactory extends Factory
{
    protected $model = Contribution::class;

    public function definition()
    {
        return [
            'member_id' => Member::factory(),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
        ];
    }
}
