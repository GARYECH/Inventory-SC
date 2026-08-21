<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OrderStatusUpdated extends Notification
{
    use Queueable;

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    // 🌟 FORMAT EMAIL MAHASISWA (PROFESIONAL & TO-THE-POINT) 🌟
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('[Inventory SC] Update Status ' . $this->order->order_number)
            ->greeting('Halo, ' . $notifiable->name . '.')
            ->line('Status pengajuan inventaris untuk kegiatan **' . $this->order->proker_name . '** telah diperbarui.')
            ->line('Status saat ini: **' . strtoupper($this->order->status) . '**')
            ->line('Silakan login ke sistem untuk melihat detail pesanan dan menyelesaikan tahapan dokumen selanjutnya (MoU, Bukti Transfer, Kwitansi, atau Bukti Pengembalian).')
            ->action('Cek Status Pesanan', route('student.loans'))
            ->salutation("Hormat kami,\nSistem Inventaris Student Council (SCIS)");
    }

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