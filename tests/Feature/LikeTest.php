<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Like;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_いいね登録成功(): void
    {
        $user = User::factory()->create();

        $book = Book::factory()->create(['user_id' => $user->id]);

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $this->actingAs($user);

        $response = $this->post("/reviews/{$review->id}/likes");

        $response->assertStatus(302);

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);
    }

    public function test_いいね解除成功(): void
    {
        $user = User::factory()->create();

        $book = Book::factory()->create(['user_id' => $user->id]);

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        Like::create([
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);

        $this->actingAs($user);

        $response = $this->post("/reviews/{$review->id}/likes");

        $response->assertStatus(302);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);
    }

    public function test_未ログインではいいね出来ない(): void
    {
        $user = User::factory()->create();

        $book = Book::factory()->create(['user_id' => $user->id]);

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $response = $this->post("/reviews/{$review->id}/likes");

        $response->assertStatus(302);

        $response->assertRedirect('/login');
    }
}
