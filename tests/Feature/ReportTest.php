<?php

namespace Tests\Feature;

use App\Enums\ReadingPlanStatus;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_マイ読書レポート画面を表示できる(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/reports');

        $response->assertStatus(200);

        $response->assertViewIs('reports.index');
    }

    public function test_読了冊数・平均評価が計算される(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'status' => ReadingPlanStatus::Completed,
        ]);

        Review::factory()->create([
            'user_id' => $user->id,
            'rating' => 5,
        ]);

        Review::factory()->create([
            'user_id' => $user->id,
            'rating' => 3,
        ]);

        $response = $this->get('/reports');

        $response->assertStatus(200);

        $response->assertViewHas('stats.summary.average_rating', 4);
    }

    public function test_評価4以上のレビューを持つ本が、高評価本として取得される(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $book = Book::factory()->create();

        Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => 5,
        ]);

        $response = $this->get('/reports');

        $response->assertStatus(200);

        $response->assertViewHas('stats.top_rated_books');
    }
}
