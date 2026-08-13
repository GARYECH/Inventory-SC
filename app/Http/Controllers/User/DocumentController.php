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
     * Cetak Invoice / Kuitansi Lunas
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
     * 🌟 FUNGSI BARU: Upload MoU Bertanda Tangan 🌟
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
     * 🌟 FUNGSI BARU: Upload Bukti Transfer 🌟
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
}