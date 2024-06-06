<?php

namespace Database\Factories;

use App\Models\Scoop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Scoop>
 */
class ScoopFactory extends Factory
{
    protected $model = Scoop::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'content_json' => json_encode(['content' => $this->faker->paragraph]),
            'content_html' => $this->faker->randomHtml(),
            'published' => $this->faker->boolean,
        ];
    }
}
