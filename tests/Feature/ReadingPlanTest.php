<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Enums\ReadingPlanStatus;

class ReadingPlanTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_読書計画一覧を表示する(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/reading-plans');

        $response->assertStatus(200);

        $response->assertSee('読書計画');
    }

    public function test_読書計画登録画面を表示する(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/reading-plans/create');

        $response->assertStatus(200);

        $response->assertSee('新規読書計画作成');
    }

    public function test_読書計画を登録できる(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $book = Book::factory()->create();

        $targetDate = '2027-01-01';

        $response = $this->post('/reading-plans',[
            'book_id' => $book->id,
            'target_date' => $targetDate,
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('reading_plans',[
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => $targetDate,
        ]);
    }

    public function test_読書計画を完了に変更できる(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->post(route('reading-plans.complete', $readingPlan));

        $response->assertRedirect(route('reading-plans.index'));

        $this->assertDatabaseHas('reading_plans', [
            'id' => $readingPlan->id,
            'status' => ReadingPlanStatus::Completed,
        ]);
    }

    public function test_読書計画編集画面を表示する(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->get(route('reading-plans.edit', $readingPlan));

        $response->assertStatus(200);

        $response->assertSee('読書計画編集');
    }

    public function test_読書計画を更新できる(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $targetDate = '2027-01-01';

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->put(route('reading-plans.update', $readingPlan),[
            'target_date' => $targetDate,
        ]);

        $response->assertRedirect('/reading-plans');

        $this->assertDatabaseHas('reading_plans',[
            'user_id' => $user->id,
            'target_date' => $targetDate,
        ]);
    }

    public function test_読書計画を削除できる(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->delete(route('reading-plans.destroy', $readingPlan));

        $response->assertRedirect('/reading-plans');

        $this->assertDatabaseMissing('reading_plans',[
            'id' => $readingPlan->id,
        ]);
    }

    public function test_他ユーザーの読書計画を更新できない(): void
    {
        $owner = User::factory()->create();

        $otherUser = User::factory()->create();

        $this->actingAs($otherUser);

        $targetDate = '2027-01-01';

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $owner->id,
        ]);

        $response = $this->put(route('reading-plans.update', $readingPlan),[
            'target_date' => $targetDate,
        ]);

        $response->assertStatus(403);

        $this->assertDatabaseHas('reading_plans',[
            'user_id' => $owner->id,
            'target_date' => $targetDate,
        ]);
    }

    public function test_他ユーザーの読書計画を削除できない(): void
    {
        $user = User::factory()->create();

        $otherUser = User::factory()->create();

        $this->actingAs($otherUser);

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->delete(route('reading-plans.destroy', $readingPlan));

        $response->assertStatus(403);
    }

    public function test_book_idが未入力だと登録できない(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/reading-plans', [
            'target_date' => '2027-01-01',
        ]);

        $response->assertSessionHasErrors([
            'book_id',
        ]);
    }

    public function test_target_dateが未入力だと登録できない(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $book = Book::factory()->create();

        $response = $this->post('/reading-plans', [
            'book_id' => $book->id,
        ]);

        $response->assertSessionHasErrors([
            'target_date',
        ]);
    }

    public function test_読書計画更新時にtarget_dateが未入力だと更新できない(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->put(route('reading-plans.update', $readingPlan),[
            'target_date' => '',
        ]);

        $response->assertSessionHasErrors([
            'target_date',
        ]);
    }
}
