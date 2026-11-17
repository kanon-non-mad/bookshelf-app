<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RankingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_ランキング画面を表示できる(): void
    {
        $book = Book::factory()->create();

        Review::factory()->create([
            'book_id' => $book->id,
        ]);

        $response = $this->get('/ranking');

        $response->assertStatus(200);

        $response->assertSee($book->title);
    }

    public function test_ランキングを評価が高い順に表示できる(): void
    {
        $bookA = Book::factory()->create();

        $bookB = Book::factory()->create();

        Review::factory()->create([
            'book_id' => $bookA->id,
            'rating' => 5,
        ]);

        Review::factory()->create([
            'book_id' => $bookB->id,
            'rating' => 3,
        ]);

        $response = $this->get('/ranking');

        $response->assertStatus(200);

        $response->assertSeeInOrder([
            $bookA->title,
            $bookB->title,
        ]);
    }
}
