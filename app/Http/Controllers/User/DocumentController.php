<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // 🌟 Library sakti kita

class DocumentController extends Controller
{
    /**
     * Cetak Surat Perjanjian (MoU)
     */
    public function downloadMou(Order $order)
    {
        // Pastikan hanya pemilik kuitansi atau admin yang bisa download
        if (auth()->id() !== $order->user_id && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        // Tentukan template MoU berdasarkan tipe (Internal atau Vendor)
        $view = $order->order_type === 'Vendor Rental' ? 'pdf.mou_vendor' : 'pdf.mou_internal';

        // Render PDF
        $pdf = Pdf::loadView($view, compact('order'));
        
        // Atur ukuran kertas ke A4
        $pdf->setPaper('a4', 'portrait');

        // Nama file dinamis
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

        $pdf = Pdf::loadView('pdf.invoice', compact('order'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('Invoice_' . $order->order_number . '.pdf');
    }
}