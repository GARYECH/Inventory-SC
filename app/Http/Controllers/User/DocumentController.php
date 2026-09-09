<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Notifications\AdminNotification;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentController extends Controller
{
    /**
     * =========================================================
     * EXPORT SECTION (GENERATE PDF DENGAN DOMPDF)
     * =========================================================
     */

    public function downloadMou(Order $order)
    {
        // 1. Validasi Keamanan
        if (auth()->id() !== $order->user_id && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $view = '';

        // 2. 🌟 LOGIKA PABRIK MoU (SWITCH-CASE CATEGORY) 🌟
        switch ($order->order_type) {
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
                
            case 'ATK':
            case 'Obat':
                // Barang habis pakai (Consumable) tidak punya MoU!
                abort(404, 'Barang Habis Pakai tidak memerlukan dokumen MoU.');
                break;
                
            case 'Merchandise':
            case 'Sale':
                // Cek spesifik nama barang pertama yang ada di dalam order
                $itemName = strtolower($order->orderItems->first()->item->name ?? '');
                
                if (str_contains($itemName, 'id card') || str_contains($itemName, 'idcard') || str_contains($itemName, 'lanyard')) {
                    $view = 'admin.pdf.mou_merch_idcard';
                } else {
                    $view = 'admin.pdf.mou_merch_baju'; // Default Merch = Baju
                }
                break;

            // 🌟 BACKWARD COMPATIBILITY (Mencegah Error Untuk Arsip Orderan Lama) 🌟
            case 'Vendor Rental':
                $view = 'admin.pdf.mou_vendor';
                break;
            case 'Internal Rental':
            default:
                $view = 'admin.pdf.mou_internal';
                break;
        }

        // 3. Render PDF
        $pdf = Pdf::loadView($view, compact('order'));
        $pdf->setPaper('a4', 'portrait');

        $filename = 'MoU_' . str_replace(' ', '_', $order->proker_name) . '_' . $order->order_number . '.pdf';
        return $pdf->stream($filename); // STREAM agar terbuka di browser dulu
    }

    public function downloadInvoice(Order $order)
    {
        if (auth()->id() !== $order->user_id && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $pdf = Pdf::loadView('admin.pdf.invoice', compact('order'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Invoice_' . $order->order_number . '.pdf');
    }

    public function downloadKwitansi(Order $order)
    {
        if (auth()->id() !== $order->user_id && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $pdf = Pdf::loadView('admin.pdf.kwitansi', compact('order'));
        $pdf->setPaper('a4', 'landscape'); // Landscape untuk bentuk kwitansi

        return $pdf->stream('Kwitansi_' . $order->order_number . '.pdf');
    }

    public function downloadBeritaAcara(Order $order)
    {
        if (auth()->id() !== $order->user_id && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $pdf = Pdf::loadView('admin.pdf.berita_acara', compact('order'));
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->stream('Berita_Acara_' . $order->order_number . '.pdf');
    }

    /**
     * =========================================================
     * UPLOAD SECTION
     * =========================================================
     */

    public function uploadSignedMou(Request $request, Order $order)
    {
        $request->validate(['signed_mou' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120']);

        if ($request->hasFile('signed_mou')) {
            $file = $request->file('signed_mou');
            $filename = 'Signed_MoU_' . $order->order_number . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('signed_mous', $filename, 'public');

            $order->update(['signed_mou' => $path, 'status' => 'Pending Review MoU']);

            // 🌟 KABARIN ADMIN 🌟
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new AdminNotification("Pembaruan Dokumen! Mahasiswa mengunggah MoU untuk pesanan: {$order->order_number}"));
            }

            return back()->with('success', 'File MoU bertanda tangan berhasil dikirim! Menunggu verifikasi Admin SC.');
        }
        return back()->with('error', 'Gagal mengupload file.');
    }
    
    public function uploadPaymentReceipt(Request $request, Order $order)
    {
        $request->validate(['payment_receipt' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120']);

        if ($request->hasFile('payment_receipt')) {
            $file = $request->file('payment_receipt');
            $filename = 'Payment_' . $order->order_number . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('payment_receipts', $filename, 'public');

            $order->update(['payment_receipt' => $path, 'status' => 'Pending Review Payment']);

            // 🌟 KABARIN ADMIN 🌟
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new AdminNotification("Bukti TF Masuk! Mahasiswa mengunggah Bukti Pembayaran untuk pesanan: {$order->order_number}"));
            }

            return back()->with('success', 'Bukti pembayaran berhasil di-upload! Menunggu verifikasi Admin SC.');
        }
        return back()->with('error', 'Gagal mengupload file.');
    }

    public function uploadSignedKwitansi(Request $request, Order $order)
    {
        $request->validate(['signed_kwitansi' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120']);

        if ($request->hasFile('signed_kwitansi')) {
            $file = $request->file('signed_kwitansi');
            $filename = 'Signed_KWT_' . $order->order_number . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('signed_kwitansis', $filename, 'public');

            $order->update(['signed_kwitansi' => $path, 'status' => 'Pending Review Kwitansi']);

            // 🌟 KABARIN ADMIN 🌟
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new AdminNotification("Pembaruan Dokumen! Mahasiswa mengunggah Kwitansi TTD untuk pesanan: {$order->order_number}"));
            }

            return back()->with('success', 'Kwitansi bertanda tangan berhasil di-upload! Menunggu verifikasi akhir Admin SC.');
        }
        return back()->with('error', 'Gagal mengupload file kwitansi.');
    }

    public function submitReturnLink(Request $request, Order $order)
    {
        $request->validate(['return_drive_link' => 'required|url']);

        $order->update(['return_drive_link' => $request->return_drive_link, 'status' => 'Pending Return Review']);

        // 🌟 KABARIN ADMIN 🌟
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new AdminNotification("Pengembalian Barang! Mahasiswa mengirimkan Link Bukti Pengembalian untuk pesanan: {$order->order_number}"));
        }

        return back()->with('success', 'Link bukti pengembalian berhasil dikirim! Menunggu Admin SC melakukan pengecekan.');
    }

    public function uploadBeritaAcara(Request $request, Order $order)
    {
        $request->validate(['signed_ba_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120']);

        if ($request->hasFile('signed_ba_file')) {
            $file = $request->file('signed_ba_file');
            $filename = 'BA_' . $order->order_number . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('berita_acara', $filename, 'public');
            
            $order->update(['signed_ba_file' => $path, 'status' => 'Pending Review BA']);

            // 🌟 KABARIN ADMIN 🌟
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new AdminNotification("Pembayaran Denda! Mahasiswa mengunggah Berita Acara untuk pesanan: {$order->order_number}"));
            }

            return back()->with('success', 'Berita Acara dan Bukti Denda berhasil di-upload!');
        }
        return back()->with('error', 'Gagal mengupload file Berita Acara.');
    }
}