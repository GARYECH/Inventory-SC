<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AdminNotification extends Notification
{
    use Queueable;

    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    // 🌟 FORMAT EMAIL ADMIN (PROFESIONAL & TO-THE-POINT) 🌟
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('[Inventory SC] Update Status Mahasiswa')
            ->greeting('Halo, Tim Admin SC.')
            ->line('Sistem mengidentifikasi adanya pembaruan aktivitas terkait peminjaman inventaris:')
            ->line('**' . $this->message . '**')
            ->line('Harap segera melakukan peninjauan dan validasi dokumen agar operasional kegiatan mahasiswa dapat berjalan lancar.')
            ->action('Buka Order Management', route('admin.orders'))
            ->salutation("Hormat kami,\nSistem Inventaris Student Council (SCIS)");
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->message,
        ];
    }
}