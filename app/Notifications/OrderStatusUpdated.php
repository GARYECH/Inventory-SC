<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated
    extends Notification
    implements ShouldQueue
{
    use Queueable;


    public $order;


    public $tries = 2;


    public $timeout = 10;


    public function __construct(
        $order
    ) {

        $this->order =
            $order;


        /*
        |--------------------------------------------------------------------------
        | QUEUE
        |--------------------------------------------------------------------------
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
                '[Inventory SC] Update Status ' .
                $this->order->order_number
            )
            ->greeting(
                'Halo, ' .
                $notifiable->name .
                '.'
            )
            ->line(
                'Status pengajuan inventaris untuk kegiatan **' .
                $this->order->proker_name .
                '** telah diperbarui.'
            )
            ->line(
                'Status saat ini: **' .
                strtoupper(
                    $this->order->status
                ) .
                '**'
            )
            ->action(
                'Cek Status Pesanan',
                route(
                    'student.loans'
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

            'order_id' =>
                $this->order->id,

            'order_number' =>
                $this->order->order_number,

            'status' =>
                $this->order->status,

            'message' =>
                'Status pesanan ' .
                $this->order->order_number .
                ' diperbarui menjadi: ' .
                $this->order->status,
        ];
    }
}