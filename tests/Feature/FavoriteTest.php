<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_お気に入り一覧表示(): void
    {
        $user = User::factory()->create();

        $book = Book::factory()->create();

        Favorite::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $this->actingAs($user);

        $response = $this->get('/favorites');

        $response->assertStatus(200);

        $response->assertViewHas('books');
    }

    public function test_未ログインではお気に入り一覧を表示できない(): void
    {
        $response = $this->get('/favorites');

        $response->assertStatus(302);

        $response->assertRedirect('/login');
    }

    public function test_お気に入り登録成功(): void
    {
        $user = User::factory()->create();

        $book = Book::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->post("/books/{$book->id}/favorites");

        $response->assertStatus(302);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }

    public function test_お気に入り解除成功(): void
    {
        $user = User::factory()->create();

        $book = Book::factory()->create(['user_id' => $user->id]);

        Favorite::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $this->actingAs($user);

        $response = $this->post("/books/{$book->id}/favorites");

        $response->assertStatus(302);

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }
}
