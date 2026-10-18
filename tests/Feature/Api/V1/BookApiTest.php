<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\Genre;
use Laravel\Sanctum\Sanctum;

class BookApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_APIで書籍一覧を取得できる(): void
    {
        Book::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/books');

        $response->assertStatus(200);

        $response->assertJsonCount(3);
    }

    public function test_APIで書籍の詳細を取得できる(): void 
    {
        $book = Book::factory()->create();

        $response = $this->getJson("/api/v1/books/{$book->id}");

        $response->assertStatus(200);

        $response->assertJson([
            'data' => [
                'title' => $book->title,
            ]
        ]);
    }

    public function test_APIでログインユーザーが書籍登録できる(): void {
        $user = User::factory()->create();

        $genre = Genre::create([
            'name' => '技術書',
        ]);

        Sanctum::actingAs($user);

        $data = [
            'isbn' => '1234567890123',
            'title' => 'APIテスト本',
            'author' => 'API著者',
            'published_at' => '2026-01-01',
            'description' => 'API用',
            'genres' => [$genre->id],
        ];

        $response = $this->postJson('/api/v1/books', $data);

        $response->assertStatus(201);

        $response->assertJson([
            'message' => 'Book created successfully.',
            'data' => [
                'title' => 'APIテスト本',
            ]
        ]);

        $this->assertDatabaseHas('books',[
            'title' => 'APIテスト本',
            'isbn' => '1234567890123',
            'user_id' => $user->id,
        ]);
    }

    public function test_APIでログインユーザーが書籍更新できる(): void {
        $user = User::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $genre = Genre::create([
            'name' => '技術書',
        ]);

        $data = [
            'isbn' => '1234567890123',
            'title' => 'APIテスト本',
            'author' => 'API著者',
            'published_at' => '2026-01-01',
            'description' => 'API用',
            'genres' => [$genre->id],
        ];

        Sanctum::actingAs($user);

        $response = $this->patchJson("/api/v1/books/{$book->id}", $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('books',[
            'title' => 'APIテスト本',
            'isbn' => '1234567890123',
            'id' => $book->id,
        ]);
    }

    public function test_APIでログインユーザーが書籍削除できる(): void {
        $user = User::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/v1/books/{$book->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('books',[
            'id' => $book->id,
        ]);
    }

    public function test_APIで他人の書籍を更新できない(): void 
    {
        $owner = User::factory()->create();

        $genre = Genre::create([
            'name' => '技術書',
        ]);

        $book = Book::factory()->create([
            'user_id' => $owner->id,
        ]);

        $data = [
            'isbn' => '1234567890123',
            'title' => 'APIテスト本',
            'author' => 'API著者',
            'published_at' => '2026-01-01',
            'description' => 'API用',
            'genres' => [$genre->id],
        ];

        $otherUser = User::factory()->create();

        Sanctum::actingAs($otherUser);

        $response = $this->patchJson("/api/v1/books/{$book->id}",$data);

        $response->assertStatus(403);
    }

    public function test_APIで他人の書籍を削除できない(): void 
    {
        $owner = User::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $owner->id,
        ]);

        $otherUser = User::factory()->create();

        Sanctum::actingAs($otherUser);

        $response = $this->deleteJson("/api/v1/books/{$book->id}");

        $response->assertStatus(403);
    }
}
