<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    use RefreshDatabase;

    public function test_patch_homepage(): void
    {
        $this->seed();
        $this->actingAs(Admin::find(1), 'admin');

        $title = fake()->sentence();
        $json = json_encode([
            'time' => 1716647560421,
            'blocks' => [
                [
                    'id' => 'mhTl6ghSkV',
                    'type' => 'paragraph',
                    'data' => [
                        'text' => 'Hey. Meet the new Editor. On this picture you can see it in action. Then, try a demo.'
                    ]
                ]
            ],
            'version' => '2.16.1'
        ]);

        $this->patch('api/homepage', [
            'title' => $title,
            'intro' => $json
        ]);

        $this->assertDatabaseHas('homepage', [
            'id' => 1,
            'title' => $title,
            'intro_json' => $json,
        ]);
    }
}
