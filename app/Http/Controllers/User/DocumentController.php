<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Notifications\AdminNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function downloadMou(
        Order $order
    ) {
        $this->authorizeOrder(
            $order
        );

        $order->load([
            'orderItems.item',
            'mouDocuments',
        ]);


        /*
        |--------------------------------------------------------------------------
        | ONE MOU PER ORDER
        |--------------------------------------------------------------------------
        */

        $document =
            $order->mouDocuments->first();


        if (!$document) {

            abort(
                404,
                'MoU tidak diperlukan untuk transaksi ini.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | TEMPLATE ROUTING
        |--------------------------------------------------------------------------
        */

        $view = match (
            $document->mou_type
        ) {

            'ht' =>
                'admin.pdf.mou_HT',

            'internal' =>
                'admin.pdf.mou_internal',

            'vendor' =>
                'admin.pdf.mou_vendor',

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


        return $pdf->stream(
            'MoU_' .
            $this->formatMouName(
                $document->mou_type
            ) .
            '_' .
            $order->order_number .
            '.pdf'
        );
    }


    public function uploadSignedMou(
        Request $request,
        Order $order
    ) {
        $this->authorizeOrder(
            $order
        );


        $order->load(
            'mouDocuments'
        );


        $document =
            $order->mouDocuments->first();


        if (!$document) {

            return back()->with(
                'error',
                'MoU untuk transaksi ini tidak ditemukan.'
            );
        }


        $request->validate([
            'signed_mou' => [
                'required',
                'file',
                'mimes:pdf',
                'max:5120',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | DELETE OLD FILE
        |--------------------------------------------------------------------------
        */

        if (
            $document->signed_file_path &&
            Storage::disk('public')->exists(
                $document->signed_file_path
            )
        ) {

            Storage::disk('public')->delete(
                $document->signed_file_path
            );
        }


        $file =
            $request->file(
                'signed_mou'
            );


        $filename =
            'Signed_MoU_' .
            $order->order_number .
            '_' .
            $document->mou_type .
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


        /*
        |--------------------------------------------------------------------------
        | MOVE TO PAYMENT
        |--------------------------------------------------------------------------
        */

        $order->update([
            'status' =>
                'Waiting for Payment',
        ]);


        /*
        |--------------------------------------------------------------------------
        | ADMIN NOTIFICATION
        |--------------------------------------------------------------------------
        */

        $this->notifyAdmins(
            "MoU {$this->formatMouName($document->mou_type)} untuk {$order->order_number} berhasil diunggah."
        );


        return back()->with(
            'success',
            'MoU berhasil diupload.'
        );
    }


    public function downloadInvoice(
        Order $order
    ) {
        $this->authorizeOrder(
            $order
        );


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
        $this->authorizeOrder(
            $order
        );


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
        $this->authorizeOrder(
            $order
        );


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

            'ht' =>
                'Handy_Talkie',

            'internal' =>
                'Internal',

            'vendor' =>
                'Vendor',

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