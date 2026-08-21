<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage; // 🌟 WAJIB DITAMBAH UNTUK EMAIL

class OrderStatusUpdated extends Notification
{
    use Queueable;

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    // 🌟 KITA PERINTAHKAN KIRIM LEWAT DATABASE (LONCENG) & EMAIL (GMAIL) 🌟
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    // 🌟 FORMAT DESAIN EMAIL (GAYA BLAST SC KEKINIAN) 🌟
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('📣 [SC Inventory] Update Pesanan: ' . $this->order->order_number)
            ->greeting('Halo, ' . $notifiable->name . '! 👋')
            ->line('Ada kabar terbaru nih soal pengajuan inventaris kamu untuk kegiatan **' . $this->order->proker_name . '**. Berkas/pesanan kamu baru saja dicek oleh Admin kami.')
            ->line('Status pesananmu saat ini telah diperbarui menjadi:')
            ->line('**✨ ' . strtoupper($this->order->status) . ' ✨**')
            ->line('Yuk, segera cek detail pesananmu di website! Pastikan kamu membaca tahapan selanjutnya (seperti upload MoU, Bukti TF, Kwitansi, atau form lainnya) agar persiapan proker kamu makin lancar dan nggak ada yang terlewat.')
            ->action('Cek Status Pesanan Sekarang', route('student.loans'))
            ->line('Semangat terus persiapannya dan sukses selalu untuk acaranya! 🚀')
            ->salutation("Salam hangat,\nAdmin Student Council");
    }

    // 🌟 FORMAT DATA YANG MASUK KE LONCENG WEB (TETAP SAMA) 🌟
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