<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated extends Notification
{
    use Queueable;

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    // 🌟 KITA PERINTAHKAN LARAVEL KIRIM KE DATABASE (LONCENG WEB) SAJA 🌟
    public function via($notifiable)
    {
        return ['database'];
    }

    // 🌟 FORMAT DATA YANG MASUK KE LONCENG WEB 🌟
    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'status' => $this->order->status,
            'message' => 'Status pesanan ' . $this->order->order_number . ' diperbarui menjadi: ' . $this->order->status,
        ];
    }
}