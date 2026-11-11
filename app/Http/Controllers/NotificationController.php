<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index () {
        $user = auth()->user();

        $notifications = $user->notifications;

        return view('notifications.index',[
            'notifications' => $notifications,
        ]);
    }

    public function read(DatabaseNotification $notification)
    {
        if($notification->notifiable_id !== auth()->id()){
            abort(403);
        }
        $notification->markAsRead();

        return redirect()->route('notifications.index');
    }
}
