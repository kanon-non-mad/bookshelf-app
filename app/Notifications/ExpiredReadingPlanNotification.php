<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExpiredReadingPlanNotification extends Notification
{
    use Queueable;

    public $readingPlan;
    /**
     * Create a new notification instance.
     */
    public function __construct($readingPlan)
    {
        $this->readingPlan = $readingPlan;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => '期限切れのお知らせ',
            'body' => $this->readingPlan->book->title . 'の読書計画が期限切れになりました',
            'timing' => '期限切れ',
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
