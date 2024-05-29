<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ScoopTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_scoop(): void
    {
        $this->seed();
        $this->actingAs(Admin::find(1), 'admin');

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

        $this->post('api/scoop', [
            'title' => fake()->word(),
            'content' => $json
        ]);

        $this->assertDatabaseHas('scoops', [
            'content_json' => $json,
            'published' => false
        ]);
    }

    public function test_create_scoop_w_cover(): void
    {
        $this->seed();
        $this->actingAs(Admin::find(1), 'admin');

        $mediaName = 'avatar.jpg';
        $collectionName = 'covers';

        $this->post('api/scoop', [
            'title' => fake()->word(),
            'cover' => UploadedFile::fake()->image($mediaName)
        ]);

        $this->assertDatabaseHas('scoops', [
            'content_json' => null,
            'published' => false
        ]);

        $this->assertDatabaseHas('media', [
            'model_type' => 'App\Models\Scoop',
            'generated_conversions' => '{"cropped":true}',
            'file_name' => $mediaName,
            'collection_name' => $collectionName
        ]);
    }

    public function test_create_scoop_w_attachments(): void
    {
        $this->seed();
        $this->actingAs(Admin::find(1), 'admin');

        $mediaName1 = 'doc1.pdf';
        $mediaName2 = 'doc2.pdf';

        $this->post('api/scoop', [
            'title' => fake()->word(),
            'attachments' => [
                UploadedFile::fake()->create($mediaName1),
                UploadedFile::fake()->create($mediaName2),
            ],
        ]);

        $this->assertDatabaseHas('scoops', [
            'content_json' => null,
            'published' => false
        ]);

        $this->assertDatabaseCount('media', 2);
    }

    public function test_patch_scoop(): void
    {
        $this->seed();
        $this->actingAs(Admin::find(1), 'admin');

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

        $this->post('api/scoop', [
            'title' => fake()->word(),
            'content' => $json
        ]);

        $this->assertDatabaseHas('scoops', [
            'content_json' => $json,
            'published' => false
        ]);

        $newJson = json_encode([
            'time' => 1716647560421,
            'blocks' => [
                [
                    'id' => 'mhTl6ghSkV',
                    'type' => 'paragraph',
                    'data' => [
                        'text' => 'new'
                    ]
                ]
            ],
            'version' => '2.16.1'
        ]);

        $this->patch('api/scoop/1', [
            'title' => 'new',
            'content' => $newJson
        ]);

        $this->assertDatabaseCount('scoops', 1);
        $this->assertDatabaseHas('scoops', [
            'title' => 'new',
            'content_json' => $newJson,
            'published' => false
        ]);
    }

    public function test_patch_scoop_cover(): void
    {
        $this->seed();
        $this->actingAs(Admin::find(1), 'admin');

        $mediaName = 'avatar.jpg';
        $collectionName = 'covers';

        $this->post('api/scoop', [
            'title' => fake()->word(),
            'cover' => UploadedFile::fake()->image($mediaName)
        ]);

        $this->assertDatabaseHas('scoops', [
            'content_json' => null,
            'published' => false
        ]);

        $this->assertDatabaseHas('media', [
            'model_type' => 'App\Models\Scoop',
            'generated_conversions' => '{"cropped":true}',
            'file_name' => $mediaName,
            'collection_name' => $collectionName
        ]);

        $mediaName2 = 'avatar2.jpg';

        $this->patch('api/scoop/1', [
            'title' => 'new',
            'cover' => UploadedFile::fake()->image($mediaName2)
        ]);

        $this->assertDatabaseHas('scoops', [
            'title' => 'new',
        ]);

        $this->assertDatabaseHas('media', [
            'model_type' => 'App\Models\Scoop',
            'generated_conversions' => '{"cropped":true}',
            'file_name' => $mediaName2,
            'collection_name' => $collectionName
        ]);
    }

    public function test_toggle_visibility(): void
    {
        $this->seed();
        $this->actingAs(Admin::find(1), 'admin');

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

        $this->post('api/scoop', [
            'title' => fake()->word(),
            'content' => $json
        ]);

        $this->get('api/scoop/1/toggle-visibility');

        $this->assertDatabaseHas('scoops', [
            'published' => true
        ]);
    }

    public function test_delete_attachment(): void
    {
        $this->seed();
        $this->actingAs(Admin::find(1), 'admin');

        $mediaName = 'doc1.pdf';
        $this->post('api/scoop', [
            'title' => fake()->word(),
            'attachments' => [
                UploadedFile::fake()->create($mediaName),
            ],
        ]);

        $this->assertDatabaseCount('scoops', 1);
        $this->assertDatabaseCount('media', 1);

        $this->delete('api/scoop/1/delete-attachment/0');

        $this->assertDatabaseCount('media', 0);
    }

    public function test_delete_scoop(): void
    {
        $this->seed();
        $this->actingAs(Admin::find(1), 'admin');

        $this->post('api/scoop', [
            'title' => fake()->word(),
            'cover' => UploadedFile::fake()->image('img.png'),
            'attachments' => [
                UploadedFile::fake()->create('doc.pdf'),
            ],
        ]);

        $this->assertDatabaseCount('scoops', 1);
        $this->assertDatabaseCount('media', 2);

        $this->delete('api/scoop/1');

        $this->assertDatabaseCount('scoops', 0);
        $this->assertDatabaseCount('media', 0);
    }
}
