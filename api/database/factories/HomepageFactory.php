<?php

namespace Database\Factories;

use App\Models\Homepage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Homepage>
 */
class HomepageFactory extends Factory
{
    protected $model = Homepage::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'intro_json' => json_encode(['intro' => $this->faker->paragraph]),
            'intro_html' => $this->faker->randomHtml(),
        ];
    }
}
