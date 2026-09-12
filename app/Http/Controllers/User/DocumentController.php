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
    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD MOU
    |--------------------------------------------------------------------------
    */

    public function downloadMou(
        Order $order,
        $documentId = null
    ) {
        $this->authorizeOrder(
            $order
        );


        /*
        |--------------------------------------------------------------------------
        | LOAD ORDER DATA
        |--------------------------------------------------------------------------
        */

        $order->load([
            'orderItems.item.category',
            'mouDocuments',
        ]);


        /*
        |--------------------------------------------------------------------------
        | FIND MOU DOCUMENT
        |--------------------------------------------------------------------------
        |
        | Kalau documentId diberikan, download MoU tersebut.
        |
        | Kalau tidak diberikan, ambil MoU pertama.
        |
        */

        if ($documentId) {

            $document =
                $order->mouDocuments
                    ->where(
                        'id',
                        (int) $documentId
                    )
                    ->first();

        } else {

            $document =
                $order->mouDocuments
                    ->first();

        }


        if (!$document) {

            abort(
                404,
                'MoU untuk transaksi ini tidak ditemukan.'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | TEMPLATE ROUTING
        |--------------------------------------------------------------------------
        |
        | Setiap jenis MoU mempunyai template sendiri.
        |
        */

        $view =
            match (
                $document->mou_type
            ) {

                /*
                 * Handy Talkie
                 */
                'ht' =>
                    'admin.pdf.mou_HT',


                /*
                 * Peralatan - Internal Rental
                 */
                'internal' =>
                    'admin.pdf.mou_internal',


                /*
                 * Peralatan - Vendor Rental
                 */
                'vendor' =>
                    'admin.pdf.mou_vendor',


                /*
                 * Merchandise - Baju
                 */
                'merch_baju' =>
                    'admin.pdf.mou_merch_baju',


                /*
                 * Merchandise - ID Card
                 */
                'merch_idcard' =>
                    'admin.pdf.mou_merch_idcard',


                default =>
                    abort(
                        404,
                        'Template MoU tidak ditemukan.'
                    ),
            };


        /*
        |--------------------------------------------------------------------------
        | GENERATE PDF
        |--------------------------------------------------------------------------
        */

        $pdf =
            Pdf::loadView(
                $view,
                [
                    'order' =>
                        $order,

                    'mouDocument' =>
                        $document,
                ]
            );


        $pdf->setPaper(
            'a4',
            'portrait'
        );


        /*
        |--------------------------------------------------------------------------
        | DOWNLOAD / STREAM
        |--------------------------------------------------------------------------
        */

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


    /*
    |--------------------------------------------------------------------------
    | UPLOAD SIGNED MOU
    |--------------------------------------------------------------------------
    */

    public function uploadSignedMou(
        Request $request,
        Order $order,
        $documentId = null
    ) {
        $this->authorizeOrder(
            $order
        );


        /*
        |--------------------------------------------------------------------------
        | LOAD MOU DOCUMENTS
        |--------------------------------------------------------------------------
        */

        $order->load(
            'mouDocuments'
        );


        /*
        |--------------------------------------------------------------------------
        | FIND DOCUMENT
        |--------------------------------------------------------------------------
        */

        if ($documentId) {

            $document =
                $order->mouDocuments
                    ->where(
                        'id',
                        (int) $documentId
                    )
                    ->first();

        } else {

            /*
             * Backward compatibility:
             * old route may not send document ID.
             */
            $document =
                $order->mouDocuments
                    ->whereNull(
                        'signed_file_path'
                    )
                    ->first();

            /*
             * Kalau semua sudah punya file,
             * ambil yang pertama.
             */
            if (!$document) {

                $document =
                    $order->mouDocuments
                        ->first();

            }

        }


        if (!$document) {

            return back()->with(
                'error',
                'MoU untuk transaksi ini tidak ditemukan.'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATE FILE
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | FILE
        |--------------------------------------------------------------------------
        */

        $file =
            $request->file(
                'signed_mou'
            );


        /*
        |--------------------------------------------------------------------------
        | FILE NAME
        |--------------------------------------------------------------------------
        */

        $filename =
            'Signed_MoU_' .

            $order->order_number .

            '_' .

            $document->mou_type .

            '.' .

            $file->getClientOriginalExtension();


        /*
        |--------------------------------------------------------------------------
        | STORE FILE
        |--------------------------------------------------------------------------
        */

        $path =
            $file->storeAs(
                'signed_mous',
                $filename,
                'public'
            );


        /*
        |--------------------------------------------------------------------------
        | SAVE DOCUMENT
        |--------------------------------------------------------------------------
        */

        $document->update([
            'signed_file_path' =>
                $path,
        ]);


        /*
        |--------------------------------------------------------------------------
        | CHECK ALL MOU
        |--------------------------------------------------------------------------
        |
        | Kalau masih ada MoU yang belum diupload,
        | jangan pindah ke payment dulu.
        |
        */

        $pendingMou =
            $order->mouDocuments()
                ->whereNull(
                    'signed_file_path'
                )
                ->exists();


        if (
            $pendingMou
        ) {

            $successMessage =
                'MoU berhasil diupload. Masih ada MoU lain yang perlu diunggah.';

        } else {

            $order->update([
                'status' =>
                    'Waiting for Payment',
            ]);


            $successMessage =
                'Semua MoU sudah diupload. Silakan lanjut ke pembayaran.';

        }


        /*
        |--------------------------------------------------------------------------
        | ADMIN NOTIFICATION
        |--------------------------------------------------------------------------
        */

        $this->notifyAdmins(

            'MoU ' .

            $this->formatMouName(
                $document->mou_type
            ) .

            ' untuk ' .

            $order->order_number .

            ' berhasil diunggah.'

        );


        return back()->with(
            'success',
            $successMessage
        );
    }


    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD INVOICE
    |--------------------------------------------------------------------------
    */

    public function downloadInvoice(
        Order $order
    ) {

        $this->authorizeOrder(
            $order
        );


        $order->load([
            'orderItems.item.category',
        ]);


        $pdf =
            Pdf::loadView(
                'admin.pdf.invoice',
                compact(
                    'order'
                )
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


    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD KWITANSI
    |--------------------------------------------------------------------------
    */

    public function downloadKwitansi(
        Order $order
    ) {

        $this->authorizeOrder(
            $order
        );


        $order->load([
            'orderItems.item.category',
        ]);


        $pdf =
            Pdf::loadView(
                'admin.pdf.kwitansi',
                compact(
                    'order'
                )
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


    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD BERITA ACARA
    |--------------------------------------------------------------------------
    */

    public function downloadBeritaAcara(
        Order $order
    ) {

        $this->authorizeOrder(
            $order
        );


        $order->load([
            'orderItems.item.category',
        ]);


        $pdf =
            Pdf::loadView(
                'admin.pdf.berita_acara',
                compact(
                    'order'
                )
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


    /*
    |--------------------------------------------------------------------------
    | UPLOAD PAYMENT RECEIPT
    |--------------------------------------------------------------------------
    */

    public function uploadPaymentReceipt(
        Request $request,
        Order $order
    ) {

        $this->authorizeOrder(
            $order
        );


        $request->validate([
            'payment_receipt' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | DELETE OLD PAYMENT FILE
        |--------------------------------------------------------------------------
        */

        if (
            $order->payment_receipt &&
            Storage::disk('public')->exists(
                $order->payment_receipt
            )
        ) {

            Storage::disk('public')->delete(
                $order->payment_receipt
            );

        }


        /*
        |--------------------------------------------------------------------------
        | STORE
        |--------------------------------------------------------------------------
        */

        $path =
            $request
                ->file('payment_receipt')
                ->store(
                    'payment_receipts',
                    'public'
                );


        $order->update([
            'payment_receipt' =>
                $path,

            'status' =>
                'Pending Review Payment',
        ]);


        /*
        |--------------------------------------------------------------------------
        | NOTIFY ADMIN
        |--------------------------------------------------------------------------
        */

        $this->notifyAdmins(

            "Bukti pembayaran {$order->order_number} berhasil diunggah."

        );


        return back()->with(
            'success',
            'Bukti pembayaran berhasil diupload.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPLOAD SIGNED KWITANSI
    |--------------------------------------------------------------------------
    */

    public function uploadSignedKwitansi(
        Request $request,
        Order $order
    ) {

        $this->authorizeOrder(
            $order
        );


        $request->validate([
            'signed_kwitansi' => [
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
            $order->signed_kwitansi &&
            Storage::disk('public')->exists(
                $order->signed_kwitansi
            )
        ) {

            Storage::disk('public')->delete(
                $order->signed_kwitansi
            );

        }


        /*
        |--------------------------------------------------------------------------
        | STORE
        |--------------------------------------------------------------------------
        */

        $path =
            $request
                ->file('signed_kwitansi')
                ->store(
                    'signed_kwitansi',
                    'public'
                );


        $order->update([
            'signed_kwitansi' =>
                $path,

            'status' =>
                'Pending Review Kwitansi',
        ]);


        /*
        |--------------------------------------------------------------------------
        | NOTIFY ADMIN
        |--------------------------------------------------------------------------
        */

        $this->notifyAdmins(

            "Kwitansi {$order->order_number} berhasil diunggah."

        );


        return back()->with(
            'success',
            'Kwitansi berhasil diupload.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SUBMIT RETURN DRIVE LINK
    |--------------------------------------------------------------------------
    */

    public function submitReturnLink(
        Request $request,
        Order $order
    ) {

        $this->authorizeOrder(
            $order
        );


        $request->validate([
            'return_drive_link' => [
                'required',
                'url',
                'max:2000',
            ],
        ]);


        $order->update([
            'return_drive_link' =>
                $request->return_drive_link,

            'status' =>
                'Pending Return Review',
        ]);


        /*
        |--------------------------------------------------------------------------
        | NOTIFY ADMIN
        |--------------------------------------------------------------------------
        */

        $this->notifyAdmins(

            "Link pengembalian {$order->order_number} berhasil dikirim."

        );


        return back()->with(
            'success',
            'Link pengembalian berhasil dikirim.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPLOAD BERITA ACARA
    |--------------------------------------------------------------------------
    */

    public function uploadBeritaAcara(
        Request $request,
        Order $order
    ) {

        $this->authorizeOrder(
            $order
        );


        $request->validate([
            'signed_ba_file' => [
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
            $order->signed_ba_file &&
            Storage::disk('public')->exists(
                $order->signed_ba_file
            )
        ) {

            Storage::disk('public')->delete(
                $order->signed_ba_file
            );

        }


        /*
        |--------------------------------------------------------------------------
        | STORE
        |--------------------------------------------------------------------------
        */

        $path =
            $request
                ->file('signed_ba_file')
                ->store(
                    'signed_ba',
                    'public'
                );


        $order->update([
            'signed_ba_file' =>
                $path,

            'status' =>
                'Pending Review BA',
        ]);


        /*
        |--------------------------------------------------------------------------
        | NOTIFY ADMIN
        |--------------------------------------------------------------------------
        */

        $this->notifyAdmins(

            "Berita Acara {$order->order_number} berhasil diunggah."

        );


        return back()->with(
            'success',
            'Dokumen penyelesaian berhasil diupload.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | AUTHORIZE ORDER
    |--------------------------------------------------------------------------
    */

    private function authorizeOrder(
        Order $order
    ): void {

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


    /*
    |--------------------------------------------------------------------------
    | MOU NAME
    |--------------------------------------------------------------------------
    */

    private function formatMouName(
        string $type
    ): string {

        return match (
            $type
        ) {

            'ht' =>
                'Handy_Talkie',

            'internal' =>
                'Internal',

            'vendor' =>
                'Vendor',

            'merch_baju' =>
                'Merch_Baju',

            'merch_idcard' =>
                'Merch_ID_Card',

            default =>
                'MOU',

        };

    }


    /*
    |--------------------------------------------------------------------------
    | NOTIFY ADMINS
    |--------------------------------------------------------------------------
    */

    private function notifyAdmins(
        string $message
    ): void {

        $admins =
            User::where(
                'role',
                'admin'
            )->get();


        foreach (
            $admins
            as $admin
        ) {

            $admin->notify(
                new AdminNotification(
                    $message
                )
            );

        }

    }
}