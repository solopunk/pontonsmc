<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_go_through_admin_funcs(): void
    {
        $this->seed();

        $simpleAdmin = Admin::find(1);

        $title = fake()->sentence();
        $do = $this->actingAs($simpleAdmin, 'admin')->patch('api/tab/1', [
            'title' => $title
        ]);

        $do->assertOk();
        $this->assertDatabaseHas('tabs', [
            'id' => 1,
            'title' => $title,
        ]);
    }
}
