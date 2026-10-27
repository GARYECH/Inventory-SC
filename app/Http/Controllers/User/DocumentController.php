<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use App\Support\MouNumberSynchronizer;
use App\Notifications\AdminNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD GUIDEBOOK
    |--------------------------------------------------------------------------
    */

    public function downloadGuidebook()
    {
        $guidebookPath =
            Setting::where(
                'key',
                'guidebook_pdf_path'
            )->value(
                'value'
            );

        if (
            empty($guidebookPath)
        ) {
            return back()->with(
                'error',
                'Guidebook belum tersedia.'
            );
        }

        if (
            !Storage::disk('public')->exists(
                $guidebookPath
            )
        ) {
            return back()->with(
                'error',
                'File Guidebook tidak ditemukan.'
            );
        }

        return response()->file(
            Storage::disk('public')->path(
                $guidebookPath
            )
        );
    }


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
            'orderItems.sizeBreakdowns',
            'mouDocuments',
        ]);

        /*
        |--------------------------------------------------------------------------
        | RENTAL DAYS
        |--------------------------------------------------------------------------
        |
        | Perhitungan:
        |
        | Minggu → Senin  = 0 hari
        | Minggu → Selasa = 1 hari
        | Minggu → Rabu   = 2 hari
        |
        */

        $rentalDays =
            $this->getRentalDays(
                $order->start_date,
                $order->end_date
            );

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

        if (
            $documentId
        ) {

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

        if (
            !$document
        ) {
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

        if (request()->boolean('mou_editor_source')) {
            abort_unless(
                auth()->check()
                && auth()->user()->role === 'admin',
                403
            );

            $mouEditorRenderedHtml = view($view,
                [
                    'order' =>
                        $order,

                    'mouDocument' =>
                        $document,

                    'rentalDays' =>
                        $rentalDays,
                ])->render();
$mouEditorRenderedHtml = $this->inlineMouEditorStorageImages($mouEditorRenderedHtml);

return response(
                $mouEditorRenderedHtml,
                200,
                [
                    'Content-Type' => 'text/html; charset=UTF-8',
                    'Cache-Control' => 'no-store, private',
                ]
            );
        }
        /* INVENTORY_SC_MOU_EDITOR_GENERATED_PDF: use saved edited HTML when available. */
        $mouEditorSavedHtml = null;
        $mouEditorDocumentId = (isset($documentId) && $documentId !== null && (int) $documentId > 0) ? (int) $documentId : null;
        if ($mouEditorDocumentId === null) {
            // If this order has exactly one MoU record, it is safe to infer it. Do not guess when multiple MoUs exist.
            $mouEditorDocumentCandidates = \Illuminate\Support\Facades\DB::table('order_mou_documents')
                ->where('order_id', $order->id)
                ->orderBy('id')
                ->limit(2)
                ->pluck('id');
            if ($mouEditorDocumentCandidates->count() === 1) {
                $mouEditorDocumentId = (int) $mouEditorDocumentCandidates->first();
            }
        }
        if ($mouEditorDocumentId !== null) {
            $mouEditorVersionRow = \Illuminate\Support\Facades\DB::table('order_mou_documents as mou_document')
                ->join('order_mou_document_versions as mou_version', 'mou_version.order_mou_document_id', '=', 'mou_document.id')
                ->where('mou_document.id', $mouEditorDocumentId)
                ->where('mou_document.order_id', $order->id)
                ->orderByDesc('mou_version.version_number')
                ->select('mou_version.content')
                ->first();

            if ($mouEditorVersionRow) {
                $mouEditorVersionContent = json_decode($mouEditorVersionRow->content, true);
                if (
                    is_array($mouEditorVersionContent)
                    && ($mouEditorVersionContent['render_mode'] ?? null) === 'legacy-html'
                    && !empty($mouEditorVersionContent['html'])
                ) {
                    $mouEditorSavedHtml = $mouEditorVersionContent['html'];
                }
            }
        }

        if (is_string($mouEditorSavedHtml) && $mouEditorSavedHtml !== '') {
            $mouEditorSavedHtml = MouNumberSynchronizer::replaceNumberInHtml(
                $mouEditorSavedHtml,
                MouNumberSynchronizer::displayNumber($order)
            );
            $mouEditorSavedHtml = $this->inlineMouEditorStorageImages($mouEditorSavedHtml);
        $pdf = Pdf::loadHTML($mouEditorSavedHtml);
        } else {
            $pdf =
                Pdf::loadView(
                    $view,
                    [
                        'order' =>
                            $order,
    
                        'mouDocument' =>
                            $document,
    
                        'rentalDays' =>
                            $rentalDays,
                    ]
                );
        }


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
        | STATUS CHECK
        |--------------------------------------------------------------------------
        */

        $isStandardMouUpload = $order->status === 'Waiting for MoU';


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

        if (
            $documentId
        ) {

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

            if (
                !$document
            ) {

                $document =
                    $order->mouDocuments
                        ->first();

            }
        }


        if (
            !$document
        ) {
            return back()->with(
                'error',
                'MoU untuk transaksi ini tidak ditemukan.'
            );
        }

        if (!$isStandardMouUpload && !(bool) ($document->requires_resign ?? false)) {
            return back()->with(
                'error',
                'MoU hanya dapat diunggah pada tahap Waiting for MoU atau saat revisi nomor memerlukan tanda tangan ulang.'
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

        if ($document->signed_file_path) {
            MouNumberSynchronizer::archiveSignedPath(
                (int) $document->id,
                (string) $document->signed_file_path,
                MouNumberSynchronizer::displayNumber($order),
                'File MoU digantikan oleh unggahan bertanda tangan yang lebih baru'
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
            '_' . now()->format('Ymd_His') . '_' . \Illuminate\Support\Str::random(6) .
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
            'signed_file_path' => $path,
            'requires_resign' => false,
        ]);


        /*
        |--------------------------------------------------------------------------
        | CHECK ALL MOU
        |--------------------------------------------------------------------------
        |
        | Kalau masih ada MoU yang belum diupload,
        | jangan pindah ke tahap review.
        |
        */

        $pendingMou =
            $order->mouDocuments()
                ->whereNull(
                    'signed_file_path'
                )
                ->exists();


        if (!$isStandardMouUpload) {
            $successMessage = 'MoU revisi berhasil diunggah. Status transaksi tetap ' . $order->status . '.';
        } elseif ($pendingMou) {
            $successMessage = 'MoU berhasil diupload. Masih ada MoU lain yang perlu diunggah.';
        } else {
            $order->update(['status' => 'Pending Review MoU']);
            $successMessage = 'Semua MoU sudah diupload. Menunggu pemeriksaan admin sebelum melanjutkan ke pembayaran.';
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
    | PIHAK KEDUA: PREVIEW / SIGN MOU WITH DRAWN OR UPLOADED SIGNATURE
    |--------------------------------------------------------------------------
    */

    public function previewMouSignature(Request $request, Order $order, $documentId)
    {
        [$payload, $pdfBytes] = $this->makeSecondPartySignedMouPreview($request, $order, $documentId);
        $request->session()->put($this->mouSignaturePreviewSessionKey($order, $payload['document']->id), [
            'signature_sha256' => hash('sha256', $payload['signature_bytes']),
            'source_html_sha256' => $payload['source_html_sha256'],
            'method' => $payload['method'],
            'created_at' => now()->timestamp,
        ]);

        $filename = 'MoU_Preview_TTD_' . $order->order_number . '.pdf';
        return response($pdfBytes, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . preg_replace('/[^A-Za-z0-9._-]/', '_', $filename) . '"',
            'Cache-Control' => 'no-store, private',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function signMouWithSignature(Request $request, Order $order, $documentId)
    {
        $request->validate([
            'consent' => ['required', 'accepted'],
            'preview_confirmed' => ['required', 'accepted'],
        ]);

        [$payload, $pdfBytes] = $this->makeSecondPartySignedMouPreview($request, $order, $documentId);
        $document = $payload['document'];
        $orderStatusBeforeSign = (string) $order->status;
        $signatureBytes = $payload['signature_bytes'];
        $method = $payload['method'];
        $sourceVersion = $payload['source_version'];
        $signedHtml = (string) $payload['signed_html'];

        $previewKey = $this->mouSignaturePreviewSessionKey($order, $document->id);
        $previewRecord = $request->session()->get($previewKey);
        $previewTimestamp = is_array($previewRecord) ? (int) ($previewRecord['created_at'] ?? 0) : 0;
        $previewAge = now()->timestamp - $previewTimestamp;
        $validPreview = is_array($previewRecord)
            && $previewTimestamp > 0
            && $previewAge >= 0
            && $previewAge <= 1800
            && ($previewRecord['method'] ?? null) === $method
            && hash_equals((string) ($previewRecord['signature_sha256'] ?? ''), hash('sha256', $signatureBytes))
            && hash_equals((string) ($previewRecord['source_html_sha256'] ?? ''), (string) $payload['source_html_sha256']);

        if (!$validPreview) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'preview_confirmed' => 'Silakan buka ulang Preview PDF TTD untuk tanda tangan dan versi MoU terbaru sebelum mengirim.',
            ]);
        }

        $safeOrderNumber = preg_replace('/[^A-Za-z0-9._-]/', '_', (string) $order->order_number) ?: (string) $order->id;
        $filename = 'Signed_MoU_' . $safeOrderNumber . '_' . preg_replace('/[^A-Za-z0-9_-]/', '_', (string) $document->mou_type)
            . '_' . now()->format('Ymd_His') . '_' . \Illuminate\Support\Str::random(6) . '.pdf';
        $path = 'signed_mous/' . $filename;

        $signedHtmlPath = 'signed_mou_html/' . $safeOrderNumber . '_'
            . preg_replace('/[^A-Za-z0-9_-]/', '_', (string) $document->mou_type)
            . '_' . now()->format('Ymd_His') . '_' . \Illuminate\Support\Str::random(6) . '.html';

        if (!Storage::disk('local')->put($signedHtmlPath, $signedHtml)) {
            abort(500, 'Snapshot isi MoU bertanda tangan gagal disimpan.');
        }

        if (!Storage::disk('public')->put($path, $pdfBytes)) {
            Storage::disk('local')->delete($signedHtmlPath);
            abort(500, 'PDF bertanda tangan gagal disimpan.');
        }

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use (
                $request, $order, $document, $path, $pdfBytes, $signatureBytes, $method, $sourceVersion, $orderStatusBeforeSign, $signedHtmlPath
            ) {
                if (!empty($document->signed_file_path)) {
                    MouNumberSynchronizer::archiveSignedPath(
                        (int) $document->id,
                        (string) $document->signed_file_path,
                        MouNumberSynchronizer::displayNumber($order),
                        'Digantikan oleh tanda tangan MoU yang lebih baru'
                    );
                }

                \Illuminate\Support\Facades\DB::table('order_mou_documents')
                    ->where('id', $document->id)
                    ->where('order_id', $order->id)
                    ->update([
                        'signed_file_path' => $path,
                        'requires_resign' => false,
                        'updated_at' => now(),
                    ]);

                \Illuminate\Support\Facades\DB::table('order_mou_signature_events')->insert([
                    'order_id' => $order->id,
                    'order_mou_document_id' => $document->id,
                    'source_version_id' => $sourceVersion ? $sourceVersion->id : null,
                    'source_version_number' => $sourceVersion ? (int) $sourceVersion->version_number : null,
                    'signer_user_id' => auth()->id(),
                    'signer_name' => auth()->user()->name ?? null,
                    'signature_method' => $method,
                    'signed_pdf_path' => $path,
                    'signed_html_path' => $signedHtmlPath,
                    'signature_sha256' => hash('sha256', $signatureBytes),
                    'signed_pdf_sha256' => hash('sha256', $pdfBytes),
                    'ip_hash' => $request->ip() ? hash_hmac('sha256', (string) $request->ip(), (string) config('app.key')) : null,
                    'signed_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $pendingMou = \Illuminate\Support\Facades\DB::table('order_mou_documents')
                    ->where('order_id', $order->id)
                    ->whereNull('signed_file_path')
                    ->exists();

                if (!$pendingMou && $orderStatusBeforeSign === 'Waiting for MoU') {
                    $order->update(['status' => 'Pending Review MoU']);
                }
            });
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($path);
            Storage::disk('local')->delete($signedHtmlPath);
            throw $exception;
        }

        $request->session()->forget($previewKey);

        $pendingMou = $order->mouDocuments()
            ->whereNull('signed_file_path')
            ->exists();

        $this->notifyAdmins(
            'MoU ' . $this->formatMouName((string) $document->mou_type)
            . ' untuk ' . $order->order_number . ' telah ditandatangani PIHAK KEDUA melalui website.'
        );

        $successMessage = $orderStatusBeforeSign !== 'Waiting for MoU'
            ? 'Tanda tangan ulang MoU berhasil disimpan. Status transaksi tetap ' . $orderStatusBeforeSign . '.'
            : ($pendingMou
                ? 'Tanda tangan berhasil disimpan. Masih ada MoU lain yang perlu ditandatangani.'
                : 'MoU bertanda tangan berhasil dikirim. Menunggu pemeriksaan admin sebelum melanjutkan ke pembayaran.');

        return back()->with('success', $successMessage);
    }

    /** Validate the input, render the exact latest saved MoU when available, then add the signature to PIHAK KEDUA. */
    private function makeSecondPartySignedMouPreview(Request $request, Order $order, $documentId): array
    {
        $this->authorizeOrder($order);

        $validatedMethod = $request->validate([
            'signature_method' => ['required', 'in:draw,upload'],
        ]);
        $method = $validatedMethod['signature_method'];

        if ($method === 'draw') {
            $validated = $request->validate([
                'signature_data' => ['required', 'string', 'max:2500000'],
            ]);
            $data = (string) $validated['signature_data'];
            if (!preg_match('/^data:image\/png;base64,([A-Za-z0-9+\/=\r\n]+)$/D', $data, $matches)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'signature_data' => 'Tanda tangan gambar tidak valid. Silakan gambar ulang.',
                ]);
            }
            $signatureBytes = base64_decode($matches[1], true);
            $drawInfo = is_string($signatureBytes) ? @getimagesizefromstring($signatureBytes) : false;
            if (!is_string($signatureBytes)
                || strlen($signatureBytes) > 1500000
                || substr($signatureBytes, 0, 8) !== "\x89PNG\r\n\x1a\n"
                || !is_array($drawInfo)
                || ($drawInfo['mime'] ?? '') !== 'image/png'
                || ($drawInfo[0] ?? 0) > 1200
                || ($drawInfo[1] ?? 0) > 400) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'signature_data' => 'File tanda tangan PNG tidak valid atau terlalu besar.',
                ]);
            }
            $mime = 'image/png';
        } else {
            $validated = $request->validate([
                'signature_file' => ['required', 'file', 'mimes:png,jpg,jpeg', 'max:2048'],
            ]);
            $file = $request->file('signature_file');
            $signatureBytes = file_get_contents($file->getRealPath());
            if (!is_string($signatureBytes) || $signatureBytes === '') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'signature_file' => 'Gambar tanda tangan tidak dapat dibaca.',
                ]);
            }
            $imageInfo = @getimagesizefromstring($signatureBytes);
            $mime = is_array($imageInfo) ? ($imageInfo['mime'] ?? '') : '';
            if (!in_array($mime, ['image/png', 'image/jpeg'], true)
                || strlen($signatureBytes) > 2097152
                || (is_array($imageInfo) && (($imageInfo[0] ?? 0) > 4000 || ($imageInfo[1] ?? 0) > 2000))) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'signature_file' => 'Gunakan gambar PNG/JPG yang valid, maksimal 2 MB dan dimensi 4000 × 2000 piksel.',
                ]);
            }
        }

        $document = \App\Models\OrderMouDocument::query()
            ->where('id', (int) $documentId)
            ->where('order_id', $order->id)
            ->first();
        abort_unless($document, 404, 'Dokumen MoU tidak ditemukan untuk pesanan ini.');
        abort_unless(
            $order->status === 'Waiting for MoU' || (bool) ($document->requires_resign ?? false),
            422,
            'MoU hanya dapat ditandatangani saat Waiting for MoU atau ketika revisi nomor memerlukan tanda tangan ulang.'
        );

        $order->loadMissing(['orderItems.item.category', 'orderItems.sizeBreakdowns', 'mouDocuments']);
        $rentalDays = $this->getRentalDays($order->start_date, $order->end_date);
        $view = match ((string) $document->mou_type) {
            'ht' => 'admin.pdf.mou_HT',
            'internal' => 'admin.pdf.mou_internal',
            'vendor' => 'admin.pdf.mou_vendor',
            'merch_baju' => 'admin.pdf.mou_merch_baju',
            'merch_idcard' => 'admin.pdf.mou_merch_idcard',
            default => abort(404, 'Template MoU tidak ditemukan.'),
        };

        $sourceVersion = \Illuminate\Support\Facades\DB::table('order_mou_document_versions as v')
            ->join('order_mou_documents as d', 'd.id', '=', 'v.order_mou_document_id')
            ->where('d.id', $document->id)
            ->where('d.order_id', $order->id)
            ->orderByDesc('v.version_number')
            ->select('v.id', 'v.version_number', 'v.content')
            ->first();

        $html = null;
        if ($sourceVersion) {
            $content = json_decode((string) $sourceVersion->content, true);
            if (is_array($content)
                && ($content['render_mode'] ?? null) === 'legacy-html'
                && !empty($content['html'])) {
                $html = (string) $content['html'];
            }
        }

        if (!is_string($html) || $html === '') {
            $html = view($view, [
                'order' => $order,
                'mouDocument' => $document,
                'rentalDays' => $rentalDays,
            ])->render();
            $sourceVersion = null;
        }

        $html = MouNumberSynchronizer::replaceNumberInHtml(
            $html,
            MouNumberSynchronizer::displayNumber($order)
        );
        $html = $this->inlineMouEditorStorageImages($html);
        $sourceHtmlSha256 = hash('sha256', $html);
        if (!\App\Support\MouSignatureHtml::canInsertSecondPartySignature($html)) {
            abort(422, 'Area tanda tangan PIHAK KEDUA tidak ditemukan pada versi MoU ini. Periksa kembali template atau simpan ulang MoU dari template asli.');
        }
        $dataUri = 'data:' . $mime . ';base64,' . base64_encode($signatureBytes);
        $signedHtml = \App\Support\MouSignatureHtml::insertSecondPartySignature($html, $dataUri);
        $pdfBytes = Pdf::loadHTML($signedHtml)->setPaper('a4', 'portrait')->output();

        return [[
            'document' => $document,
            'signature_bytes' => $signatureBytes,
            'method' => $method,
            'source_version' => $sourceVersion,
            'source_html_sha256' => $sourceHtmlSha256,
            'signed_html' => $signedHtml,
        ], $pdfBytes];
    }


    private function mouSignaturePreviewSessionKey(Order $order, $documentId): string
    {
        return 'mou_signature_preview.' . (int) $order->id . '.' . (int) $documentId;
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


        /*
        |--------------------------------------------------------------------------
        | LOAD ORDER DATA
        |--------------------------------------------------------------------------
        */

        $order->load([
            'orderItems.item.category',
            'orderItems.sizeBreakdowns',
        ]);


        /*
        |--------------------------------------------------------------------------
        | RENTAL DAYS
        |--------------------------------------------------------------------------
        */

        $rentalDays =
            $this->getRentalDays(
                $order->start_date,
                $order->end_date
            );


        /*
        |--------------------------------------------------------------------------
        | GENERATE PDF
        |--------------------------------------------------------------------------
        */

        $pdf =
            Pdf::loadView(
                'admin.pdf.invoice',
                [
                    'order' =>
                        $order,

                    'rentalDays' =>
                        $rentalDays,
                ]
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


        /*
        |--------------------------------------------------------------------------
        | LOAD ORDER DATA
        |--------------------------------------------------------------------------
        */

        $order->load([
            'orderItems.item.category',
            'orderItems.sizeBreakdowns',
        ]);


        /*
        |--------------------------------------------------------------------------
        | RENTAL DAYS
        |--------------------------------------------------------------------------
        */

        $rentalDays =
            $this->getRentalDays(
                $order->start_date,
                $order->end_date
            );


        /*
        |--------------------------------------------------------------------------
        | GENERATE PDF
        |--------------------------------------------------------------------------
        */

        $pdf =
            Pdf::loadView(
                'admin.pdf.kwitansi',
                [
                    'order' =>
                        $order,

                    'rentalDays' =>
                        $rentalDays,
                ]
            );


        $pdf->setPaper(
            'a4',
            'portrait'
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


        /*
        |--------------------------------------------------------------------------
        | LOAD ORDER DATA
        |--------------------------------------------------------------------------
        */

        $order->load([
            'orderItems.item.category',
            'orderItems.sizeBreakdowns',
        ]);


        /*
        |--------------------------------------------------------------------------
        | RENTAL DAYS
        |--------------------------------------------------------------------------
        */

        $rentalDays =
            $this->getRentalDays(
                $order->start_date,
                $order->end_date
            );


        /*
        |--------------------------------------------------------------------------
        | GENERATE PDF
        |--------------------------------------------------------------------------
        */

        $pdf =
            Pdf::loadView(
                'admin.pdf.berita_acara',
                [
                    'order' =>
                        $order,

                    'rentalDays' =>
                        $rentalDays,
                ]
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


        /*
        |--------------------------------------------------------------------------
        | STATUS CHECK
        |--------------------------------------------------------------------------
        */

        if (
            $order->status !==
            'Waiting for Payment'
        ) {
            return back()->with(
                'error',
                'Bukti pembayaran belum dapat diunggah pada tahap ini.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATE FILE
        |--------------------------------------------------------------------------
        */

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
                ->file(
                    'payment_receipt'
                )
                ->store(
                    'payment_receipts',
                    'public'
                );


        /*
        |--------------------------------------------------------------------------
        | UPDATE ORDER
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | STATUS CHECK
        |--------------------------------------------------------------------------
        */

        if (
            $order->status !==
            'Waiting for Kwitansi'
        ) {
            return back()->with(
                'error',
                'Kwitansi belum dapat diunggah pada tahap ini.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATE FILE
        |--------------------------------------------------------------------------
        */

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
                ->file(
                    'signed_kwitansi'
                )
                ->store(
                    'signed_kwitansi',
                    'public'
                );


        /*
        |--------------------------------------------------------------------------
        | UPDATE ORDER
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | STATUS CHECK
        |--------------------------------------------------------------------------
        */

        if (
            $order->status !==
            'Handed Over'
        ) {
            return back()->with(
                'error',
                'Pengembalian belum dapat dikirim pada tahap ini.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATE LINK
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'return_drive_link' => [
                'required',
                'url',
                'max:2000',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | UPDATE ORDER
        |--------------------------------------------------------------------------
        */

        $order->update([
            'return_drive_link' =>
                $request->return_drive_link,

            'return_submitted_at' =>
                now(),

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


        /*
        |--------------------------------------------------------------------------
        | STATUS CHECK
        |--------------------------------------------------------------------------
        */

        if (
            !in_array(
                $order->status,
                [
                    'Returned',
                    'Returned (Damaged)',
                ],
                true
            )
        ) {
            return back()->with(
                'error',
                'Berita Acara belum dapat diunggah pada tahap ini.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATE FILE
        |--------------------------------------------------------------------------
        */

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
                ->file(
                    'signed_ba_file'
                )
                ->store(
                    'signed_ba',
                    'public'
                );


        /*
        |--------------------------------------------------------------------------
        | UPDATE ORDER
        |--------------------------------------------------------------------------
        */

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
    | GET RENTAL DAYS
    |--------------------------------------------------------------------------
    |
    | Perhitungan jumlah hari rental:
    |
    | Start → End
    |
    | Minggu → Senin  = 1 hari (minimum)
    | Minggu → Selasa = 1 hari (minimum)
    | Minggu → Rabu   = 2 hari
    | Senin  → Rabu   = 1 hari (minimum)
    |
    */

    private function getRentalDays(
        $startDate,
        $endDate
    ): int {
        if (
            empty($startDate) ||
            empty($endDate)
        ) {
            return 1;
        }


        $start =
            Carbon::parse(
                $startDate
            )->startOfDay();


        $end =
            Carbon::parse(
                $endDate
            )->startOfDay();


        if (
            $end->lessThan(
                $start
            )
        ) {
            return 1;
        }


        $difference =
            $start->diffInDays(
                $end
            );


        return max(
            1,
            $difference - 1
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


    /**
     * Convert server-local image paths from the Settings-managed public disk into
     * data URIs. Browser iframes cannot read server filesystem paths such as
     * /.../storage/app/public/logo.png, but DomPDF's old Blade templates can.
     * The source file itself is read only from storage/app/public.
     */
    private function inlineMouEditorStorageImages(string $html): string
    {
        $storageRoot = realpath(storage_path('app/public'));
        if ($storageRoot === false) {
            return $html;
        }

        $storageRootPrefix = rtrim($storageRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $updatedHtml = preg_replace_callback(
            '~(?P<before><img\\b[^>]*?\\bsrc\\s*=\\s*)(?P<quote>["\'])(?P<src>.*?)\\k<quote>(?P<after>[^>]*>)~is',
            function (array $matches) use ($storageRootPrefix): string {
                $source = html_entity_decode(
                    $matches['src'],
                    ENT_QUOTES | ENT_HTML5,
                    'UTF-8'
                );

                if (
                    $source === ''
                    || preg_match('~^(?:https?:|data:|//)~i', $source)
                ) {
                    return $matches[0];
                }

                $source = rawurldecode($source);
                $realPath = realpath($source);

                if (
                    $realPath === false
                    || !str_starts_with($realPath, $storageRootPrefix)
                    || !is_file($realPath)
                ) {
                    return $matches[0];
                }

                $mimeType = function_exists('mime_content_type')
                    ? mime_content_type($realPath)
                    : false;

                if (!is_string($mimeType) || !str_starts_with(strtolower($mimeType), 'image/')) {
                    return $matches[0];
                }

                $imageBytes = @file_get_contents($realPath);
                if ($imageBytes === false) {
                    return $matches[0];
                }

                $dataUri = 'data:' . $mimeType . ';base64,' . base64_encode($imageBytes);

                return $matches['before']
                    . $matches['quote']
                    . $dataUri
                    . $matches['quote']
                    . $matches['after'];
            },
            $html
        );

        return is_string($updatedHtml) ? $updatedHtml : $html;
    }
}