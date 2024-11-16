<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Post;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store()
    {
        $this->withoutExceptionHandling();
        $response = $this->json('POST', '/api/posts', [
            'title' => 'El post de prueba'
        ]);

        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
            ->assertJson(['title' => 'El post de prueba'])
            ->assertStatus(201);

        $this->assertDatabaseHas('posts', ['title' => 'El post de prueba']);
    }

    public function test_show()
    {
        $post = factory(Post::class)->create();

        $response = $this->json('GET', "api/posts/$post->id"); //id = 1

        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
            ->assertJson(['title' => $post->title])
            ->assertStatus(200); //OK
    }

    public function test_404_show()
    {
        $response = $this->json('GET', "api/posts/500"); 
        $response->assertStatus(404); //NOK
    }

    public function test_update()
    {
        $post = factory(Post::class)->create();

        $response = $this->json('PUT', "/api/posts/$post->id", [
            'title' => 'nuevo'
        ]);

        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
            ->assertJson(['title' => 'nuevo'])
            ->assertStatus(200);

        $this->assertDatabaseHas('posts', ['title' => 'nuevo']);
    }
    public function test_delete()
    {
        $post = factory(Post::class)->create();

        $response = $this->json('DELETE', "/api/posts/$post->id");

        $response->assertSee(null)
            ->assertStatus(204); //Sin contenido...

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }
}
