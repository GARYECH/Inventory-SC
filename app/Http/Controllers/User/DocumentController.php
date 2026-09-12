<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Notifications\AdminNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function downloadMou(Order $order)
    {
        if (
            auth()->id() !== $order->user_id &&
            auth()->user()->role !== 'admin'
        ) {
            abort(403, 'Unauthorized action.');
        }

        $order->load('orderItems.item');

        $firstItem = $order->orderItems->first();

        if (!$firstItem || !$firstItem->item) {
            abort(404, 'Item pada transaksi tidak ditemukan.');
        }

        $item = $firstItem->item;

        switch ($item->transaction_type) {
            case 'Peralatan':
                $view = 'admin.pdf.mou_peralatan';
                break;

            case 'HT UV-82':
                $view = 'admin.pdf.mou_ht_uv82';
                break;

            case 'HT 888s':
                $view = 'admin.pdf.mou_ht_888s';
                break;

            case 'HT UV-5R':
                $view = 'admin.pdf.mou_ht_uv5r';
                break;

            case 'Merchandise':
                if ($item->subcategory === 'Baju') {
                    $view = 'admin.pdf.mou_merch_baju';
                    break;
                }

                if ($item->subcategory === 'ID Card') {
                    $view = 'admin.pdf.mou_merch_idcard';
                    break;
                }

                abort(
                    404,
                    'Subkategori Merchandise ini tidak memiliki template MoU.'
                );

            case 'ATK':
            case 'Obat':
                abort(
                    404,
                    'Barang Habis Pakai tidak memerlukan dokumen MoU.'
                );

            case 'Internal Rental':
                $view = 'admin.pdf.mou_internal';
                break;

            case 'Vendor Rental':
                $view = 'admin.pdf.mou_vendor';
                break;

            default:
                abort(
                    404,
                    'Jenis transaksi tidak memiliki template MoU.'
                );
        }

        $pdf = Pdf::loadView(
            $view,
            compact('order')
        );

        $pdf->setPaper('a4', 'portrait');

        $filename =
            'MoU_' .
            str_replace(
                ' ',
                '_',
                $order->proker_name
            ) .
            '_' .
            $order->order_number .
            '.pdf';

        return $pdf->stream($filename);
    }

    public function downloadInvoice(Order $order)
    {
        $this->authorizeOrder($order);

        $order->load('orderItems.item');

        $pdf = Pdf::loadView(
            'admin.pdf.invoice',
            compact('order')
        );

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream(
            'Invoice_' . $order->order_number . '.pdf'
        );
    }

    public function downloadKwitansi(Order $order)
    {
        $this->authorizeOrder($order);

        $order->load('orderItems.item');

        $pdf = Pdf::loadView(
            'admin.pdf.kwitansi',
            compact('order')
        );

        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream(
            'Kwitansi_' . $order->order_number . '.pdf'
        );
    }

    public function downloadBeritaAcara(Order $order)
    {
        $this->authorizeOrder($order);

        $order->load('orderItems.item');

        $pdf = Pdf::loadView(
            'admin.pdf.berita_acara',
            compact('order')
        );

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream(
            'Berita_Acara_' . $order->order_number . '.pdf'
        );
    }

    public function uploadSignedMou(Request $request, Order $order)
    {
        $request->validate([
            'signed_mou' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120'
        ]);

        $file = $request->file('signed_mou');

        $filename =
            'Signed_MoU_' .
            $order->order_number .
            '.' .
            $file->getClientOriginalExtension();

        $path = $file->storeAs(
            'signed_mous',
            $filename,
            'public'
        );

        $order->update([
            'signed_mou' => $path,
            'status' => 'Pending Review MoU'
        ]);

        $this->notifyAdmins(
            "Pembaruan Dokumen! Mahasiswa mengunggah MoU untuk pesanan: {$order->order_number}"
        );

        return back()->with(
            'success',
            'File MoU bertanda tangan berhasil dikirim! Menunggu verifikasi Admin SC.'
        );
    }

    public function uploadPaymentReceipt(
        Request $request,
        Order $order
    ) {
        $request->validate([
            'payment_receipt' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120'
        ]);

        $file = $request->file('payment_receipt');

        $filename =
            'Payment_' .
            $order->order_number .
            '_' .
            time() .
            '.' .
            $file->getClientOriginalExtension();

        $path = $file->storeAs(
            'payment_receipts',
            $filename,
            'public'
        );

        $order->update([
            'payment_receipt' => $path,
            'status' => 'Pending Review Payment'
        ]);

        $this->notifyAdmins(
            "Bukti TF Masuk! Mahasiswa mengunggah Bukti Pembayaran untuk pesanan: {$order->order_number}"
        );

        return back()->with(
            'success',
            'Bukti pembayaran berhasil di-upload! Menunggu verifikasi Admin SC.'
        );
    }

    public function uploadSignedKwitansi(
        Request $request,
        Order $order
    ) {
        $request->validate([
            'signed_kwitansi' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120'
        ]);

        $file = $request->file('signed_kwitansi');

        $filename =
            'Signed_KWT_' .
            $order->order_number .
            '_' .
            time() .
            '.' .
            $file->getClientOriginalExtension();

        $path = $file->storeAs(
            'signed_kwitansis',
            $filename,
            'public'
        );

        $order->update([
            'signed_kwitansi' => $path,
            'status' => 'Pending Review Kwitansi'
        ]);

        $this->notifyAdmins(
            "Pembaruan Dokumen! Mahasiswa mengunggah Kwitansi TTD untuk pesanan: {$order->order_number}"
        );

        return back()->with(
            'success',
            'Kwitansi bertanda tangan berhasil di-upload! Menunggu verifikasi akhir Admin SC.'
        );
    }

    public function submitReturnLink(
        Request $request,
        Order $order
    ) {
        $request->validate([
            'return_drive_link' => 'required|url'
        ]);

        $order->update([
            'return_drive_link' => $request->return_drive_link,
            'status' => 'Pending Return Review'
        ]);

        $this->notifyAdmins(
            "Pengembalian Barang! Mahasiswa mengirimkan Link Bukti Pengembalian untuk pesanan: {$order->order_number}"
        );

        return back()->with(
            'success',
            'Link bukti pengembalian berhasil dikirim! Menunggu Admin SC melakukan pengecekan.'
        );
    }

    public function uploadBeritaAcara(
        Request $request,
        Order $order
    ) {
        $request->validate([
            'signed_ba_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120'
        ]);

        $file = $request->file('signed_ba_file');

        $filename =
            'BA_' .
            $order->order_number .
            '_' .
            time() .
            '.' .
            $file->getClientOriginalExtension();

        $path = $file->storeAs(
            'berita_acara',
            $filename,
            'public'
        );

        $order->update([
            'signed_ba_file' => $path,
            'status' => 'Pending Review BA'
        ]);

        $this->notifyAdmins(
            "Pembayaran Denda! Mahasiswa mengunggah Berita Acara untuk pesanan: {$order->order_number}"
        );

        return back()->with(
            'success',
            'Berita Acara dan Bukti Denda berhasil di-upload!'
        );
    }

    private function authorizeOrder(Order $order)
    {
        if (
            auth()->id() !== $order->user_id &&
            auth()->user()->role !== 'admin'
        ) {
            abort(403, 'Unauthorized action.');
        }
    }

    private function notifyAdmins($message)
    {
        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            $admin->notify(
                new AdminNotification($message)
            );
        }
    }
}