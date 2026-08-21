<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage; // 🌟 WAJIB DITAMBAH UNTUK EMAIL

class AdminNotification extends Notification
{
    use Queueable;

    public $message;

    // Kita bikin dinamis, jadi pesannya bisa diatur bebas nanti
    public function __construct($message)
    {
        $this->message = $message;
    }

    // 🌟 AKTIFKAN JALUR EMAIL UNTUK ADMIN JUGA 🌟
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    // 🌟 FORMAT EMAIL KHUSUS UNTUK ADMIN SC 🌟
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🚨 [Admin Alert] Ada Update dari Mahasiswa!')
            ->greeting('Halo Tim Admin SC! 🫡')
            ->line('Sistem mendeteksi ada aktivitas baru dari mahasiswa nih. Tolong segera dicek ya:')
            ->line('**' . $this->message . '**')
            ->line('Yuk, segera login ke Order Management untuk melakukan review dokumen (Approve/Reject) supaya proker teman-teman mahasiswa bisa jalan dengan lancar.')
            ->action('Buka Order Management', route('admin.orders'))
            ->line('Semangat terus ngurusin inventarisnya! 🔥')
            ->salutation("System Auto-Bot,\nSCIS Kabinet Keong");
    }

    // 🌟 MASUK KE LONCENG WEB 🌟
    public function toArray($notifiable)
    {
        return [
            'message' => $this->message,
        ];
    }
}