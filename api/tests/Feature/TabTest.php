<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TabTest extends TestCase
{
    use RefreshDatabase;

    public function test_patch_tab(): void
    {
        $this->seed();

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

        $this->patch('api/tab/1', [
            'title' => $title,
            'content' => $json
        ]);

        $this->assertDatabaseHas('tabs', [
            'id' => 1,
            'title' => $title,
            'content_json' => $json
        ]);
    }
}
