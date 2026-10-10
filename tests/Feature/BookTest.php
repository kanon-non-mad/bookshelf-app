<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Book;
use App\Models\User;
use App\Models\Genre;

class BookTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_書籍一覧表示(): void
    {
        $book = Book::factory()->create();

        $response = $this->get('/books');

        $response->assertStatus(200);

        $response->assertSee($book->title);
    }

    public function test_書籍詳細表示(): void
    {
        $book = Book::factory()->create();

        $response = $this->get("/books/{$book->id}");

        $response->assertStatus(200);

        $response->assertSee($book->title);

        $response->assertSee($book->isbn);

        $response->assertSee($book->author);

        $response->assertSee($book->published_at);
    }

    public function test_書籍登録画面表示(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/books/create');

        $response->assertStatus(200);

        $response->assertSee('書籍の登録');
    }

    public function test_書籍登録成功(): void
    {
        $user = User::factory()->create();

        $genre = Genre::create([
        'name' => '技術書',
        ]);

        $this->actingAs($user);

        $data = [
        'isbn' => '1234567890123',
        'title' => 'テスト本',
        'author' => '著者',
        'published_at' => '2026-01-01',
        'description' => 'テスト用',
        'genres' => [1],
        ];

        $response = $this->post('/books',$data);

        $response->assertStatus(302);

        $this->assertDatabaseHas('books',[
            'title' => 'テスト本',
            'isbn' => '1234567890123',
            'author' => '著者',
        ]);
    }

    public function test_ISBN重複で登録できない(): void
    {
        $user = User::factory()->create();

        $genre = Genre::create([
        'name' => '技術書',
        ]);

        $this->actingAs($user);

        Book::factory()->create([
            'isbn' => '1234567890123',
        ]);

        $data = [
        'isbn' => '1234567890123',
        'title' => 'テスト本',
        'author' => '著者',
        'published_at' => '2026-01-01',
        'description' => 'テスト用',
        'genres' => [$genre->id],
        ];

        $response = $this->post('/books',$data);

        $response->assertStatus(302);

        $response->assertSessionHasErrors([
            'isbn'
        ]);
    }

    public function test_必須項目が空の時、書籍登録できない(): void {
        $user = User::factory()->create();

        $genre = Genre::create([
        'name' => '技術書',
        ]);

        $this->actingAs($user);

        $data = [
        'isbn' => '1234567890123',
        'published_at' => '2026-01-01',
        'description' => 'テスト用',
        'genres' => [$genre->id],
        ];

        $response = $this->post('/books',$data);

        $response->assertStatus(302);

        $response->assertSessionHasErrors([
            'title','author',
        ]);
    }

    public function test_自分が登録した書籍を編集できる(): void {
        $user = User::factory()->create();

        $genre = Genre::create([
        'name' => '技術書',
        ]);

        $this->actingAs($user);
        
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $data = [
        'isbn' => '1234567890124',
        'title' => 'テスト本 改訂版',
        'author' => '著者',
        'published_at' => '2026-01-01',
        'description' => 'テスト用',
        'genres' => [$genre->id],
        ];

        $response = $this->patch("/books/{$book->id}",$data);

        $response->assertStatus(302);

        $this->assertDatabaseHas('books',[
            'isbn' => '1234567890124',
            'title' => 'テスト本 改訂版',
        ]);
    }

    public function test_他人が登録した書籍を編集できない(): void {
        $owner = User::factory()->create();

        $genre = Genre::create([
        'name' => '技術書',
        ]);

        $book = Book::factory()->create([
            'user_id' => $owner->id,
        ]);

        $data = [
        'isbn' => '1234567890124',
        'title' => 'テスト本 改訂版',
        'author' => '著者',
        'published_at' => '2026-01-01',
        'description' => 'テスト用',
        'genres' => [$genre->id],
        ];

        $otherUser = User::factory()->create();

        $this->actingAs($otherUser);

        $response = $this->patch("/books/{$book->id}",$data);

        $response->assertStatus(403);
    }

    public function test_自分が登録した書籍を削除できる(): void {
        $user = User::factory()->create();

        $this->actingAs($user);
        
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $data = [
        'isbn' => '1234567890123',
        'title' => 'テスト本',
        'author' => '著者',
        'published_at' => '2026-01-01',
        'description' => 'テスト用',
        'genres' => [1],
        ];

        $response = $this->delete("/books/{$book->id}");

        $response->assertStatus(302);

        $this->assertDatabaseMissing('books',[
            'id' => $book->id,
        ]);
    }

    public function test_他人が登録した書籍を削除できない(): void {
        $owner = User::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $owner->id,
        ]);

        $otherUser = User::factory()->create();

        $this->actingAs($otherUser);

        $response = $this->delete("/books/{$book->id}");

        $response->assertStatus(403);
    }
}
