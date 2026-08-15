<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentController extends Controller
{
    /**
     * Cetak Surat Perjanjian (MoU)
     */
    public function downloadMou(Order $order)
    {
        if (auth()->id() !== $order->user_id && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $view = $order->order_type === 'Vendor Rental' ? 'admin.pdf.mou_vendor' : 'admin.pdf.mou_internal';

        $pdf = Pdf::loadView($view, compact('order'));
        $pdf->setPaper('a4', 'portrait');

        $filename = 'MoU_' . str_replace(' ', '_', $order->proker_name) . '_' . $order->order_number . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Cetak Invoice (Tagihan)
     */
    public function downloadInvoice(Order $order)
    {
        if (auth()->id() !== $order->user_id && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $pdf = Pdf::loadView('admin.pdf.invoice', compact('order'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('Invoice_' . $order->order_number . '.pdf');
    }

    /**
     * Cetak Kwitansi (Official Receipt)
     */
    public function downloadKwitansi(Order $order)
    {
        if (auth()->id() !== $order->user_id && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $pdf = Pdf::loadView('admin.pdf.kwitansi', compact('order'));
        $pdf->setPaper('a4', 'landscape'); // Kwitansi biasanya berbentuk memanjang (landscape)

        // Kita gunakan stream() agar PDF-nya terbuka di tab baru browser, bukan langsung terdownload
        return $pdf->stream('Kwitansi_' . $order->order_number . '.pdf');
    }

    /**
     * Upload MoU Bertanda Tangan
     */
    public function uploadSignedMou(Request $request, Order $order)
    {
        // Validasi file
        $request->validate([
            'signed_mou' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // Maksimal 5MB
        ]);

        if ($request->hasFile('signed_mou')) {
            $file = $request->file('signed_mou');
            $filename = 'Signed_MoU_' . $order->order_number . '.' . $file->getClientOriginalExtension();
            
            // Simpan ke storage/app/public/signed_mous
            $path = $file->storeAs('signed_mous', $filename, 'public');

            // Update database dan ubah status agar Admin tahu
            $order->update([
                'signed_mou' => $path,
                'status' => 'Pending Review MoU'
            ]);

            return back()->with('success', 'File MoU bertanda tangan berhasil dikirim! Menunggu verifikasi Admin SC.');
        }

        return back()->with('error', 'Gagal mengupload file.');
    }
    
    /**
     * Upload Bukti Transfer
     */
    public function uploadPaymentReceipt(Request $request, Order $order)
    {
        $request->validate([
            'payment_receipt' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('payment_receipt')) {
            $file = $request->file('payment_receipt');
            $filename = 'Payment_' . $order->order_number . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            $path = $file->storeAs('payment_receipts', $filename, 'public');

            $order->update([
                'payment_receipt' => $path,
                // Status tidak otomatis berubah jadi Paid, biar Admin yang verifikasi
            ]);

            return back()->with('success', 'Bukti pembayaran berhasil di-upload! Menunggu verifikasi Admin.');
        }

        return back()->with('error', 'Gagal mengupload file.');
    }

    /**
     * 🌟 FUNGSI BARU: Upload Kwitansi Bertanda Tangan Mahasiswa 🌟
     */
    public function uploadSignedKwitansi(Request $request, Order $order)
    {
        $request->validate([
            'signed_kwitansi' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('signed_kwitansi')) {
            $file = $request->file('signed_kwitansi');
            $filename = 'Signed_KWT_' . $order->order_number . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Simpan ke folder public/signed_kwitansis
            $path = $file->storeAs('signed_kwitansis', $filename, 'public');

            $order->update([
                'signed_kwitansi' => $path,
                'status' => 'Pending Review Kwitansi' // Otomatis ubah status biar Admin ngeh
            ]);

            return back()->with('success', 'Kwitansi bertanda tangan berhasil di-upload! Menunggu verifikasi akhir Admin SC.');
        }

        return back()->with('error', 'Gagal mengupload file kwitansi.');
    }
    /**
     * 🌟 FUNGSI BARU: Submit Link Drive Bukti Pengembalian 🌟
     */
    public function submitReturnLink(Request $request, Order $order)
    {
        $request->validate([
            'return_drive_link' => 'required|url', // Wajib berupa link URL
        ]);

        $order->update([
            'return_drive_link' => $request->return_drive_link,
            'status' => 'Pending Return Review' // Otomatis ubah status biar Admin ngecek
        ]);

        return back()->with('success', 'Link bukti pengembalian berhasil dikirim! Menunggu Admin SC melakukan pengecekan barang fisik.');
    }
    public function downloadBeritaAcara(Order $order)
    {
        $pdf = Pdf::loadView('admin.pdf.berita_acara', compact('order'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('Berita_Acara_' . $order->order_number . '.pdf');
    }

    public function uploadBeritaAcara(Request $request, Order $order)
    {
        $request->validate(['signed_ba_file' => 'required|file|mimes:pdf|max:5120']);

        if ($request->hasFile('signed_ba_file')) {
            $path = $request->file('signed_ba_file')->storeAs('berita_acara', 'BA_'.$order->order_number.'_'.time().'.pdf', 'public');
            $order->update([
                'signed_ba_file' => $path,
                'status' => 'Resolved (Fine Paid)' // Selesai
            ]);
            return back()->with('success', 'Berita Acara dan Bukti Denda berhasil di-upload! Kasus selesai.');
        }
        return back()->with('error', 'Gagal mengupload file.');
    }
}