<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use App\Notifications\ExpiredReadingPlanNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_期限切れ通知が送信される(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
        ]);

        $user->notify(new ExpiredReadingPlanNotification($readingPlan));

        Notification::assertSentTo(
            $user,
            ExpiredReadingPlanNotification::class
        );
    }

    public function test_通知内容が正しい(): void
    {
        $user = User::factory()->create();

        $book = Book::factory()->create([
            'title' => 'Laravel入門',
        ]);

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $notification = new ExpiredReadingPlanNotification($readingPlan);

        $data = $notification->toDatabase($user);

        $this->assertSame('期限切れのお知らせ', $data['title']);

        $this->assertSame('Laravel入門の読書計画が期限切れになりました', $data['body']);

        $this->assertSame('期限切れ', $data['timing']);
    }
}
