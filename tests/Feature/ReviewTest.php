<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_自分のレビュー編集画面表示(): void
    {
        $user = User::factory()->create();

        $book = Book::factory()->create();

        $this->actingAs($user);

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $response = $this->get("/reviews/{$review->id}/edit");

        $response->assertStatus(200);
    }

    public function test_レビュー登録成功(): void
    {
        $user = User::factory()->create();

        $book = Book::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $data = [
            'content' => 'テスト用',
            'rating' => 5,
        ];

        $response = $this->post("/books/{$book->id}/reviews", $data);

        $response->assertStatus(302);

        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'content' => 'テスト用',
            'rating' => 5,
        ]);
    }

    public function test_同じ本に複数レビューできる(): void
    {
        $user = User::factory()->create();

        $book = Book::factory()->create();

        Review::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'content' => '1回目',
            'rating' => 5,
        ]);

        Review::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'content' => '2回目',
            'rating' => 4,
        ]);

        $this->assertDatabaseCount('reviews', 2);
    }

    public function test_未ログインではレビュー投稿できない(): void
    {
        $book = Book::factory()->create();

        $response = $this->post("/books/{$book->id}/reviews", [
            'content' => 'テスト',
            'rating' => 5,
        ]);

        $response->assertStatus(302);

        $response->assertRedirect('/login');
    }

    public function test_必須項目が空の時、レビューが登録できない(): void
    {
        $user = User::factory()->create();

        $book = Book::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $data = [
            'content' => 'テスト用',
        ];

        $response = $this->post("/books/{$book->id}/reviews", $data);

        $response->assertStatus(302);

        $response->assertSessionHasErrors(['rating']);
    }

    public function test_自分のレビューを編集できる(): void
    {
        $user = User::factory()->create();

        $book = Book::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $data = [
            'content' => '編集後のレビュー',
            'rating' => 4,
        ];

        $response = $this->patch("/reviews/{$review->id}", $data);

        $response->assertStatus(302);

        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'content' => '編集後のレビュー',
            'rating' => 4,
        ]);
    }

    public function test_他人が登録したレビューを編集できない(): void
    {
        $owner = User::factory()->create();

        $book = Book::factory()->create(['user_id' => $owner->id]);

        $review = Review::factory()->create([
            'user_id' => $owner->id,
            'book_id' => $book->id,
        ]);

        $data = [
            'content' => '編集後のレビュー',
            'rating' => 4,
        ];

        $otherUser = User::factory()->create();

        $this->actingAs($otherUser);

        $response = $this->patch("/reviews/{$review->id}", $data);

        $response->assertStatus(403);
    }

    public function test_自分が登録したレビューを削除できる(): void
    {
        $user = User::factory()->create();

        $book = Book::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $response = $this->delete("/reviews/{$review->id}");

        $response->assertStatus(302);

        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id,
        ]);
    }

    public function test_他人が登録したレビューを削除できない(): void
    {
        $owner = User::factory()->create();

        $book = Book::factory()->create(['user_id' => $owner->id]);

        $review = Review::factory()->create([
            'user_id' => $owner->id,
            'book_id' => $book->id,
        ]);

        $otherUser = User::factory()->create();

        $this->actingAs($otherUser);

        $response = $this->delete("/reviews/{$review->id}");

        $response->assertStatus(403);
    }
}
