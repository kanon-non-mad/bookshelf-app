<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Like;
use App\Models\User;
use App\Models\Book;
use App\Models\Review;

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

        $this->assertDatabaseHas('likes',[
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

        $this->assertDatabaseMissing('likes',[
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
