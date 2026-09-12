<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminNotification
    extends Notification
    implements ShouldQueue
{
    use Queueable;


    public $message;


    public $tries = 2;


    public $timeout = 10;


    public function __construct(
        $message
    ) {

        $this->message =
            $message;


        /*
        |--------------------------------------------------------------------------
        | IMPORTANT
        |--------------------------------------------------------------------------
        | Jangan gunakan onQueue('notifications')
        | karena worker development kamu saat ini
        | memproses default queue.
        |
        | Dengan ini notification masuk ke
        | default database queue.
        */

        $this->afterCommit();
    }


    public function via(
        $notifiable
    ) {

        return [
            'database',
            'mail',
        ];
    }


    public function toMail(
        $notifiable
    ) {

        return (new MailMessage)
            ->subject(
                '[Inventory SC] Update Status Mahasiswa'
            )
            ->greeting(
                'Halo, Tim Admin SC.'
            )
            ->line(
                'Sistem menerima pembaruan aktivitas inventaris.'
            )
            ->line(
                '**' .
                $this->message .
                '**'
            )
            ->action(
                'Buka Order Management',
                route(
                    'admin.orders'
                )
            )
            ->salutation(
                "Hormat kami,\nSistem Inventaris Student Council"
            );
    }


    public function toArray(
        $notifiable
    ) {

        return [
            'message' =>
                $this->message,
        ];
    }
}