<?php

namespace Database\Factories;

use App\Models\Mail;
use App\Models\MailType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mail>
 */
class MailFactory extends Factory
{
    protected $model = Mail::class;

    public function definition()
    {
        return [
            'mail_type_id' => MailType::inRandomOrder()->first()->id,
            'title' => $this->faker->sentence,
            'content_json' => json_encode(['content' => $this->faker->paragraph]),
            'content_html' => $this->faker->randomHtml(),
            'to' => json_encode([$this->faker->safeEmail]),
            'sent' => $this->faker->boolean,
            'sheet' => $this->faker->boolean,
        ];
    }
}
