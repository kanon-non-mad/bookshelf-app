<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * ユーザーの通知一覧を表示する
     */
    public function index(): View
    {
        $user = auth()->user();

        $notifications = $user->notifications;

        return view('notifications.index', [
            'notifications' => $notifications,
        ]);
    }

    /**
     * 通知を既読状態に変更する
     */
    public function read(DatabaseNotification $notification): RedirectResponse
    {
        if ($notification->notifiable_id !== auth()->id()) {
            abort(403);
        }
        $notification->markAsRead();

        return redirect()->route('notifications.index');
    }
}
