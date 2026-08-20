<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminNotification extends Notification
{
    use Queueable;

    public $message;

    // Kita bikin dinamis, jadi pesannya bisa diatur bebas nanti
    public function __construct($message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['database']; // Masuk ke lonceng web aja
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->message,
        ];
    }
}