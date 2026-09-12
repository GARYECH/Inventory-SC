<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderMouDocument;
use App\Models\User;
use App\Notifications\AdminNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function downloadMou(
        Order $order,
        ?string $type = null
    ) {
        $this->authorizeOrder($order);

        $order->load(
            'orderItems.item',
            'mouDocuments'
        );

        if (!$type) {

            $document =
                $order->mouDocuments->first();

            if (!$document) {
                abort(
                    404,
                    'Transaksi ini tidak memiliki MoU.'
                );
            }

            $type =
                $document->mou_type;
        }

        $document =
            $order->mouDocuments
                ->firstWhere(
                    'mou_type',
                    $type
                );

        if (!$document) {
            abort(
                404,
                'MoU tersebut tidak diperlukan untuk transaksi ini.'
            );
        }

        $view =
            match ($type) {

                'peralatan' =>
                    'admin.pdf.mou_peralatan',

                'ht' =>
                    'admin.pdf.mou_ht_uv82',

                'internal' =>
                    'admin.pdf.mou_internal',

                'vendor' =>
                    'admin.pdf.mou_vendor',

                'baju' =>
                    'admin.pdf.mou_merch_baju',

                'id_card' =>
                    'admin.pdf.mou_merch_idcard',

                default =>
                    abort(
                        404,
                        'Template MoU tidak ditemukan.'
                    ),

            };

        $pdf =
            Pdf::loadView(
                $view,
                compact('order')
            );

        $pdf->setPaper(
            'a4',
            'portrait'
        );

        $filename =
            'MoU_' .
            $this->formatMouName($type) .
            '_' .
            $order->order_number .
            '.pdf';

        return $pdf->stream(
            $filename
        );
    }

    public function uploadSignedMou(
        Request $request,
        Order $order,
        ?string $type = null
    ) {
        $this->authorizeOrder($order);

        $order->load(
            'mouDocuments'
        );

        if (!$type) {

            if (
                $order->mouDocuments->count() !== 1
            ) {
                return back()->with(
                    'error',
                    'Transaksi ini memiliki lebih dari satu MoU. Pilih jenis MoU yang akan diupload.'
                );
            }

            $type =
                $order
                    ->mouDocuments
                    ->first()
                    ->mou_type;
        }

        $document =
            $order->mouDocuments
                ->firstWhere(
                    'mou_type',
                    $type
                );

        if (!$document) {
            abort(
                404,
                'Dokumen MoU tidak ditemukan.'
            );
        }

        $request->validate([
            'signed_mou' =>
                'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $file =
            $request
                ->file('signed_mou');

        $filename =
            'Signed_MoU_' .
            $order->order_number .
            '_' .
            $type .
            '.' .
            $file->getClientOriginalExtension();

        $path =
            $file->storeAs(
                'signed_mous',
                $filename,
                'public'
            );

        $document->update([
            'signed_file_path' =>
                $path,
        ]);

        $allUploaded =
            $order->mouDocuments()
                ->whereNull(
                    'signed_file_path'
                )
                ->doesntExist();

        if ($allUploaded) {

            $order->update([
                'status' =>
                    'Waiting for Payment'
            ]);

        } else {

            $order->update([
                'status' =>
                    'Pending Review MoU'
            ]);
        }

        $this->notifyAdmins(
            "MoU {$type} untuk {$order->order_number} berhasil diunggah."
        );

        return back()->with(
            'success',
            'MoU berhasil diupload.'
        );
    }

    public function downloadInvoice(
        Order $order
    ) {
        $this->authorizeOrder($order);

        $order->load(
            'orderItems.item'
        );

        $pdf =
            Pdf::loadView(
                'admin.pdf.invoice',
                compact('order')
            );

        $pdf->setPaper(
            'a4',
            'portrait'
        );

        return $pdf->stream(
            'Invoice_' .
            $order->order_number .
            '.pdf'
        );
    }

    public function downloadKwitansi(
        Order $order
    ) {
        $this->authorizeOrder($order);

        $order->load(
            'orderItems.item'
        );

        $pdf =
            Pdf::loadView(
                'admin.pdf.kwitansi',
                compact('order')
            );

        $pdf->setPaper(
            'a4',
            'landscape'
        );

        return $pdf->stream(
            'Kwitansi_' .
            $order->order_number .
            '.pdf'
        );
    }

    public function downloadBeritaAcara(
        Order $order
    ) {
        $this->authorizeOrder($order);

        $order->load(
            'orderItems.item'
        );

        $pdf =
            Pdf::loadView(
                'admin.pdf.berita_acara',
                compact('order')
            );

        $pdf->setPaper(
            'a4',
            'portrait'
        );

        return $pdf->stream(
            'Berita_Acara_' .
            $order->order_number .
            '.pdf'
        );
    }

    private function authorizeOrder(
        Order $order
    ) {
        if (
            auth()->id() !==
                $order->user_id &&
            auth()->user()->role !==
                'admin'
        ) {
            abort(
                403,
                'Unauthorized action.'
            );
        }
    }

    private function formatMouName(
        string $type
    ): string {

        return match ($type) {

            'peralatan' =>
                'Peralatan',

            'ht' =>
                'Handy_Talkie',

            'internal' =>
                'Internal_Rental',

            'vendor' =>
                'Vendor_Rental',

            'baju' =>
                'Baju',

            'id_card' =>
                'ID_Card',

            default =>
                'MOU',

        };
    }

    private function notifyAdmins(
        string $message
    ): void {

        $admins =
            User::where(
                'role',
                'admin'
            )->get();

        foreach (
            $admins as $admin
        ) {
            $admin->notify(
                new AdminNotification(
                    $message
                )
            );
        }
    }
}