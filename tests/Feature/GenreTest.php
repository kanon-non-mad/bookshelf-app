<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Genre;
use App\Models\User;
use App\Models\Book;

class GenreTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_ジャンル一覧を表示できる(): void 
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/genres');

        $response->assertStatus(200);

        $response->assertViewHas('genres');
    }

    public function test_未ログインではジャンル一覧を表示できない(): void
    {
        $response = $this->get('/genres');

        $response->assertStatus(302);

        $response->assertRedirect('/login');
    }
    
    public function test_ジャンル登録成功(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $data = [
            'name' => '技術書',
        ];

        $response = $this->post('/genres',$data);

        $response->assertStatus(302);

        $this->assertDatabaseHas('genres',[
            'name' => '技術書',
        ]);
    }

    public function test_必須項目が空の時、ジャンルが登録できない(): void 
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $data = [];

        $response = $this->post('/genres',$data);

        $response->assertStatus(302);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_同じジャンル名は登録できない(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Genre::create([
            'name' => '技術書',
        ]);

        $data = [
            'name' => '技術書',
        ];

        $response = $this->post('/genres', $data);

        $response->assertStatus(302);

        $response->assertSessionHasErrors([
            'name'
        ]);
    }

    public function test_使用してないジャンルを削除できる(): void {
        $user = User::factory()->create();

        $this->actingAs($user);

        $genre = Genre::create([
            'name' => '技術書',
        ]);

        $response = $this->delete("/genres/{$genre->id}");

        $response->assertStatus(302);

        $this->assertDatabaseMissing('genres',[
            'id' => $genre->id,
        ]);
    }

    public function test_使用中のジャンルを削除できない(): void {
        $user = User::factory()->create();

        $this->actingAs($user);

        $genre = Genre::create([
            'name' => '技術書',
        ]);

        $book = Book::factory()->create();

        $book->genres()->attach($genre->id);

        $response = $this->delete("/genres/{$genre->id}");

        $response->assertStatus(302);

        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
        ]);
    }
}
