<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Support\MouNumberSynchronizer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MouEditorController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('order_mou_documents as d')
            ->join('orders as o', 'o.id', '=', 'd.order_id')
            ->select(
                'd.id as document_id',
                'd.order_id',
                'd.mou_type',
                'd.signed_file_path',
                DB::raw('(SELECT COUNT(*) FROM order_mou_document_signed_versions sv WHERE sv.order_mou_document_id = d.id) as signed_archive_count'),
                'd.updated_at as document_updated_at',
                'o.order_number',
                'o.proker_name',
                'o.department',
                'o.order_type'
            );

        if ($request->filled('order_id')) {
            $query->where('d.order_id', (int) $request->query('order_id'));
        }

        $documents = $query->orderByDesc('d.id')->paginate(20)->withQueryString();

        return view('admin.mou-editor.index', compact('documents'));
    }

    public function edit(Request $request, Order $order, int $documentId)
    {
        $document = $this->findDocument($order, $documentId);
        $versions = DB::table('order_mou_document_versions')
            ->where('order_mou_document_id', $documentId)
            ->orderByDesc('version_number')
            ->get();

        $latestVersion = $versions->first();
        $content = $latestVersion ? $this->decodeContent($latestVersion->content) : ['html' => ''];

        $signedHistory = DB::table('order_mou_document_signed_versions')
            ->where('order_mou_document_id', $documentId)
            ->orderByDesc('superseded_at')
            ->orderByDesc('id')
            ->get();
        $isSignedEdit = $request->query('mode') === 'signed';
        $signedHtmlAvailable = false;
        $signedPdfSourcePath = '';
        $signedPdfSourceUrl = null;
        if ($isSignedEdit) {
            [$signedPdfSourcePath] = $this->signedPdfSource($document);
            if ($signedPdfSourcePath !== '') {
                $signedPdfSourceUrl = asset('storage/' . ltrim($signedPdfSourcePath, '/'));
            }

            $signedHtmlPath = $this->latestSignedHtmlPath($documentId, $signedPdfSourcePath);
            if ($signedHtmlPath !== null) {
                $signedHtml = Storage::disk('local')->get($signedHtmlPath);
                if (is_string($signedHtml) && $signedHtml !== '') {
                    $content = ['html' => $signedHtml];
                    $signedHtmlAvailable = true;
                }
            }
        }

        // Never present the ordinary MoU template as though it were a signed PDF.
        // Documents without a signing-time HTML snapshot use a dedicated PDF viewer/upload page.
        if ($isSignedEdit && !$signedHtmlAvailable) {
            return view('admin.mou-editor.signed-upload', compact(
                'order', 'document', 'signedHistory', 'signedPdfSourcePath', 'signedPdfSourceUrl'
            ));
        }

        $sourceUrl = route('admin.orders.document.mou', [
            'order' => $order->id,
            'documentId' => $documentId,
        ]) . '?mou_editor_source=1';
        $editorHtml = $this->inlineMouEditorStorageImages((string) ($content['html'] ?? ''));
        $editorHtml = MouNumberSynchronizer::replaceNumberInHtml(
            $editorHtml,
            MouNumberSynchronizer::displayNumber($order)
        );
        $savedHtmlBase64 = base64_encode($editorHtml);

        return view('admin.mou-editor.edit', compact(
            'order', 'document', 'content', 'versions', 'latestVersion', 'sourceUrl', 'savedHtmlBase64', 'isSignedEdit', 'signedHtmlAvailable', 'signedHistory'
        ));
    }

    public function save(Request $request, Order $order, int $documentId)
    {
        $document = $this->findDocument($order, $documentId);
        $validated = $request->validate([
            'rendered_html' => ['required', 'string', 'max:1500000'],
            'change_note' => ['nullable', 'string', 'max:500'],
        ]);

        $html = $this->sanitizeRenderedHtml($validated['rendered_html']);
        $submittedMouNumber = MouNumberSynchronizer::extractNumber($html);
        $numberChanged = $submittedMouNumber !== null
            && $submittedMouNumber !== MouNumberSynchronizer::displayNumber($order);
        $numberChange = ['changed' => false, 'archived_signed_count' => 0, 'updated_signed_count' => 0, 'requires_resign_count' => 0];
        $content = [
            'format_version' => 2,
            'render_mode' => 'legacy-html',
            'html' => $html,
            'transaction_snapshot' => [
                'order_id' => (int) $order->id,
                'order_number' => (string) $order->order_number,
                'mou_type' => (string) $document->mou_type,
            ],
        ];
        $changeNote = trim((string) ($validated['change_note'] ?? '')) ?: 'Edit isi MoU';

        $versionNumber = DB::transaction(function () use ($documentId, $content, $changeNote, $order, $submittedMouNumber, $numberChanged, &$numberChange) {
            if ($numberChanged) {
                $numberChange = MouNumberSynchronizer::updateOrderNumber(
                    $order,
                    $submittedMouNumber,
                    'Nomor MoU diperbarui melalui MoU Editor'
                );
            }

            $latest = DB::table('order_mou_document_versions')
                ->where('order_mou_document_id', $documentId)
                ->orderByDesc('version_number')
                ->lockForUpdate()
                ->first();

            $nextVersion = $latest ? ((int) $latest->version_number + 1) : 1;
            DB::table('order_mou_document_versions')->insert([
                'order_mou_document_id' => $documentId,
                'version_number' => $nextVersion,
                'content' => json_encode($content, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                'status' => 'draft',
                'change_note' => $changeNote,
                'created_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return $nextVersion;
        });

        $successMessage = "MoU berhasil disimpan sebagai versi {$versionNumber}. Nomor MoU disinkronkan ke data pesanan.";
        if (($numberChange['updated_signed_count'] ?? 0) > 0) {
            $successMessage .= ' ' . $numberChange['updated_signed_count']
                . ' PDF MoU signed diperbarui dengan nomor terbaru; salinan lama tetap diarsipkan.';
        }
        if (($numberChange['requires_resign_count'] ?? 0) > 0) {
            $successMessage .= ' ' . $numberChange['requires_resign_count']
                . ' dokumen lama tidak memiliki sumber HTML/tanda tangan untuk dibuat ulang dan perlu ditandatangani ulang satu kali.';
        }

        return redirect()
            ->route('admin.mou-editor.edit', [$order->id, $document->id])
            ->with('success', $successMessage);
    }

    /**
     * Publish a corrected copy from the website-signed HTML snapshot.
     * The prior PDF and original signing event/hash are preserved in history.
     */
    public function reissueSigned(Request $request, Order $order, int $documentId)
    {
        $document = $this->findDocument($order, $documentId);
        $validated = $request->validate([
            'rendered_html' => ['required', 'string', 'max:1500000'],
            'change_note' => ['required', 'string', 'max:500'],
            'confirm_visual_reissue' => ['required', 'accepted'],
        ]);

        $currentActivePath = trim((string) ($document->signed_file_path ?? ''));
        [$sourcePath, $sourceIsArchived] = $this->signedPdfSource($document);
        abort_unless($sourcePath !== '', 422, 'PDF signed aktif maupun PDF arsip tidak ditemukan.');

        abort_unless(
            Schema::hasTable('order_mou_document_signed_reissues')
                && Schema::hasColumn('order_mou_document_signed_reissues', 'reissued_signed_html_path'),
            500,
            'Migrasi arsip reissue MoU belum dijalankan.'
        );

        $sourceHtmlPath = $this->latestSignedHtmlPath($documentId, $sourcePath);
        abort_unless($sourceHtmlPath !== null, 422,
            'PDF ini tidak memiliki snapshot HTML signed yang tersimpan. Gunakan formulir unggah PDF revisi di bawah, atau minta penandatangan menandatangani ulang revisi isi.'
        );
        $sourceHtml = Storage::disk('local')->get($sourceHtmlPath);
        abort_unless(is_string($sourceHtml) && $sourceHtml !== '', 422, 'Snapshot HTML signed tidak dapat dibaca.');

        $sourceSignatureUri = $this->secondPartySignatureDataUri($sourceHtml);
        abort_unless($sourceSignatureUri !== null, 422,
            'Gambar tanda tangan PIHAK KEDUA tidak dapat diverifikasi dari snapshot. Gunakan PDF revisi yang diunggah atau minta tanda tangan ulang.'
        );

        $html = $this->prepareHtmlForDomPdf($validated['rendered_html']);
        $html = MouNumberSynchronizer::replaceNumberInHtml(
            $html,
            MouNumberSynchronizer::displayNumber($order)
        );
        $decodedHtml = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        abort_unless(str_contains($decodedHtml, $sourceSignatureUri), 422,
            'Gambar tanda tangan pada snapshot asli tidak ditemukan di hasil edit. Batalkan pengeditan tanda tangan dan coba lagi.'
        );

        $pdfBytes = Pdf::loadHTML($html)->setPaper('a4', 'portrait')->output();
        abort_unless(is_string($pdfBytes) && str_starts_with($pdfBytes, '%PDF-'), 500, 'PDF hasil edit tidak valid. PDF lama tetap menjadi dokumen aktif.');

        $safeOrder = preg_replace('/[^A-Za-z0-9._-]/', '_', (string) $order->order_number) ?: (string) $order->id;
        $safeType = preg_replace('/[^A-Za-z0-9_-]/', '_', (string) $document->mou_type) ?: 'mou';
        $token = now()->format('Ymd_His') . '_' . Str::random(6);
        $newPdfPath = 'signed_mous/Reissued_Signed_MoU_' . $safeOrder . '_' . $safeType . '_' . $token . '.pdf';
        $newHtmlPath = 'signed_mou_html/Reissued_Signed_MoU_' . $safeOrder . '_' . $safeType . '_' . $token . '.html';
        $reason = trim((string) $validated['change_note']);

        if (!Storage::disk('local')->put($newHtmlPath, $html)) {
            throw new \RuntimeException('Snapshot HTML revisi gagal disimpan. PDF aktif tidak diubah.');
        }
        if (!Storage::disk('public')->put($newPdfPath, $pdfBytes)) {
            Storage::disk('local')->delete($newHtmlPath);
            throw new \RuntimeException('PDF revisi gagal disimpan. PDF aktif tidak diubah.');
        }

        try {
            DB::transaction(function () use (
                $documentId, $order, $currentActivePath, $sourcePath, $sourceIsArchived, $newPdfPath, $newHtmlPath, $pdfBytes, $reason
            ) {
                $lockedDocument = DB::table('order_mou_documents')
                    ->where('id', $documentId)
                    ->where('order_id', $order->id)
                    ->lockForUpdate()
                    ->first();
                if (!$lockedDocument || trim((string) ($lockedDocument->signed_file_path ?? '')) !== $currentActivePath) {
                    throw new \RuntimeException('PDF aktif berubah saat proses edit. Tidak ada versi yang ditimpa; muat ulang halaman.');
                }

                $sourceHash = hash('sha256', Storage::disk('public')->get($sourcePath));
                if (!$sourceIsArchived) {
                    MouNumberSynchronizer::archiveSignedPath(
                        $documentId,
                        $sourcePath,
                        MouNumberSynchronizer::displayNumber($order),
                        'Admin menerbitkan ulang View Signed: ' . $reason
                    );
                }

                DB::table('order_mou_document_signed_reissues')->insert([
                    'order_mou_document_id' => $documentId,
                    'source_signed_file_path' => $sourcePath,
                    'source_signed_pdf_sha256' => $sourceHash,
                    'reissued_file_path' => $newPdfPath,
                    'reissued_pdf_sha256' => hash('sha256', $pdfBytes),
                    'reissued_signed_html_path' => $newHtmlPath,
                    'mou_number' => MouNumberSynchronizer::displayNumber($order),
                    'reason' => mb_substr($reason, 0, 255),
                    'issued_by' => Auth::id(),
                    'issued_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('order_mou_documents')->where('id', $documentId)->update([
                    'signed_file_path' => $newPdfPath,
                    'requires_resign' => false,
                    'updated_at' => now(),
                ]);
            });
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($newPdfPath);
            Storage::disk('local')->delete($newHtmlPath);
            throw $exception;
        }

        return redirect()
            ->route('admin.mou-editor.edit', ['order' => $order->id, 'documentId' => $documentId, 'mode' => 'signed'])
            ->with('success', 'View Signed berhasil diperbarui. PDF sebelumnya tetap tersimpan di arsip. Ini adalah salinan visual revisi; perubahan isi substantif tetap perlu persetujuan/tanda tangan pihak terkait.');
    }

    /** Replace the active signed PDF with a corrected PDF prepared by an admin externally. */
    public function reissueSignedUpload(Request $request, Order $order, int $documentId)
    {
        $document = $this->findDocument($order, $documentId);
        $validated = $request->validate([
            'corrected_pdf' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'change_note_upload' => ['required', 'string', 'max:500'],
            'confirm_visual_reissue_upload' => ['required', 'accepted'],
        ]);

        $currentActivePath = trim((string) ($document->signed_file_path ?? ''));
        [$sourcePath, $sourceIsArchived] = $this->signedPdfSource($document);
        abort_unless($sourcePath !== '', 422, 'PDF signed aktif maupun PDF arsip tidak ditemukan.');
        abort_unless(
            Schema::hasTable('order_mou_document_signed_reissues')
                && Schema::hasColumn('order_mou_document_signed_reissues', 'reissued_signed_html_path'),
            500,
            'Migrasi arsip reissue MoU belum dijalankan.'
        );

        $file = $request->file('corrected_pdf');
        $pdfBytes = file_get_contents($file->getRealPath());
        abort_unless(is_string($pdfBytes) && str_starts_with($pdfBytes, '%PDF-'), 422, 'File harus merupakan PDF yang valid.');

        $safeOrder = preg_replace('/[^A-Za-z0-9._-]/', '_', (string) $order->order_number) ?: (string) $order->id;
        $safeType = preg_replace('/[^A-Za-z0-9_-]/', '_', (string) $document->mou_type) ?: 'mou';
        $filename = 'Reissued_Signed_MoU_' . $safeOrder . '_' . $safeType . '_' . now()->format('Ymd_His') . '_' . Str::random(6) . '.pdf';
        $newPdfPath = $file->storeAs('signed_mous', $filename, 'public');
        if (!$newPdfPath) {
            throw new \RuntimeException('PDF revisi gagal disimpan. PDF lama tetap menjadi dokumen aktif.');
        }
        $reason = trim((string) $validated['change_note_upload']);

        try {
            DB::transaction(function () use ($documentId, $order, $currentActivePath, $sourcePath, $sourceIsArchived, $newPdfPath, $reason) {
                $lockedDocument = DB::table('order_mou_documents')
                    ->where('id', $documentId)
                    ->where('order_id', $order->id)
                    ->lockForUpdate()
                    ->first();
                if (!$lockedDocument || trim((string) ($lockedDocument->signed_file_path ?? '')) !== $currentActivePath) {
                    throw new \RuntimeException('PDF aktif berubah saat proses edit. Tidak ada versi yang ditimpa; muat ulang halaman.');
                }

                $sourceHash = hash('sha256', Storage::disk('public')->get($sourcePath));
                $newBytes = Storage::disk('public')->get($newPdfPath);
                if (!$sourceIsArchived) {
                    MouNumberSynchronizer::archiveSignedPath(
                        $documentId,
                        $sourcePath,
                        MouNumberSynchronizer::displayNumber($order),
                        'Admin mengunggah PDF koreksi: ' . $reason
                    );
                }

                DB::table('order_mou_document_signed_reissues')->insert([
                    'order_mou_document_id' => $documentId,
                    'source_signed_file_path' => $sourcePath,
                    'source_signed_pdf_sha256' => $sourceHash,
                    'reissued_file_path' => $newPdfPath,
                    'reissued_pdf_sha256' => hash('sha256', $newBytes),
                    'reissued_signed_html_path' => null,
                    'mou_number' => MouNumberSynchronizer::displayNumber($order),
                    'reason' => mb_substr('Unggah PDF eksternal: ' . $reason, 0, 255),
                    'issued_by' => Auth::id(),
                    'issued_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('order_mou_documents')->where('id', $documentId)->update([
                    'signed_file_path' => $newPdfPath,
                    'requires_resign' => false,
                    'updated_at' => now(),
                ]);
            });
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($newPdfPath);
            throw $exception;
        }

        return redirect()
            ->route('admin.mou-editor.edit', ['order' => $order->id, 'documentId' => $documentId, 'mode' => 'signed'])
            ->with('success', 'PDF koreksi berhasil menjadi View Signed terbaru. PDF sebelumnya tetap tersimpan di arsip. Pastikan perubahan isi substantif telah disetujui dan ditandatangani pihak terkait.');
    }


    public function preview(Request $request, Order $order, int $documentId)
    {
        $document = $this->findDocument($order, $documentId);
        $validated = $request->validate([
            'rendered_html' => ['required', 'string', 'max:1500000'],
        ]);
        $html = $this->prepareHtmlForDomPdf($validated['rendered_html']);

        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'portrait');
        return $pdf->stream('MoU_Preview_' . $order->order_number . '_' . $document->mou_type . '.pdf');
    }

    public function versionPdf(Order $order, int $documentId, int $versionId)
    {
        $this->findDocument($order, $documentId);
        $version = DB::table('order_mou_document_versions')
            ->where('id', $versionId)
            ->where('order_mou_document_id', $documentId)
            ->first();
        abort_unless($version, 404);

        $content = $this->decodeContent($version->content);
        abort_unless(!empty($content['html']), 422, 'Versi ini tidak berisi HTML MoU.');
        $versionHtml = $this->prepareHtmlForDomPdf($content['html']);
        $pdf = Pdf::loadHTML($versionHtml)->setPaper('a4', 'portrait');
        return $pdf->stream('MoU_' . $order->order_number . '_V' . $version->version_number . '.pdf');
    }

    public function restore(Order $order, int $documentId, int $versionId)
    {
        $this->findDocument($order, $documentId);
        $sourceVersion = DB::table('order_mou_document_versions')
            ->where('id', $versionId)
            ->where('order_mou_document_id', $documentId)
            ->first();
        abort_unless($sourceVersion, 404);
        $sourceContent = $this->decodeContent($sourceVersion->content);
        abort_unless(!empty($sourceContent['html']), 422, 'Versi ini tidak berisi HTML MoU.');

        $newVersion = DB::transaction(function () use ($documentId, $sourceVersion, $sourceContent) {
            $latest = DB::table('order_mou_document_versions')
                ->where('order_mou_document_id', $documentId)
                ->orderByDesc('version_number')
                ->lockForUpdate()
                ->first();
            $nextVersion = $latest ? ((int) $latest->version_number + 1) : 1;

            DB::table('order_mou_document_versions')->insert([
                'order_mou_document_id' => $documentId,
                'version_number' => $nextVersion,
                'content' => json_encode($sourceContent, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                'status' => 'draft',
                'change_note' => 'Pemulihan dari versi ' . $sourceVersion->version_number,
                'created_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return $nextVersion;
        });

        return redirect()
            ->route('admin.mou-editor.edit', [$order->id, $documentId])
            ->with('success', "Isi versi lama disalin ke draft baru versi {$newVersion}. Versi lama tetap tersimpan.");
    }

    /** Return the active PDF or the newest archived PDF still present in storage. */
    private function signedPdfSource(object $document): array
    {
        $activePath = trim((string) ($document->signed_file_path ?? ''));
        if ($activePath !== '' && Storage::disk('public')->exists($activePath)) {
            return [$activePath, false];
        }

        $versions = DB::table('order_mou_document_signed_versions')
            ->where('order_mou_document_id', $document->id)
            ->orderByDesc('superseded_at')
            ->orderByDesc('id')
            ->get();
        foreach ($versions as $version) {
            $path = trim((string) ($version->signed_file_path ?? ''));
            if ($path !== '' && Storage::disk('public')->exists($path)) {
                return [$path, true];
            }
        }

        return ['', false];
    }

    private function latestSignedHtmlPath(int $documentId, string $activePath): ?string
    {
        if ($activePath !== '' && Schema::hasTable('order_mou_document_signed_reissues')
            && Schema::hasColumn('order_mou_document_signed_reissues', 'reissued_signed_html_path')) {
            $activeReissue = DB::table('order_mou_document_signed_reissues')
                ->where('order_mou_document_id', $documentId)
                ->where('reissued_file_path', $activePath)
                ->orderByDesc('id')
                ->first();
            if ($activeReissue) {
                $path = trim((string) ($activeReissue->reissued_signed_html_path ?? ''));
                if ($path !== '' && Storage::disk('local')->exists($path)) {
                    return $path;
                }
                $reissueReason = mb_strtolower(trim((string) ($activeReissue->reason ?? '')));
                if (str_starts_with($reissueReason, 'unggah pdf eksternal:')
                    || (!str_contains($reissueReason, 'nomor') && !str_contains($reissueReason, 'number'))) {
                    return null;
                }
                // A legacy number-only PDF overlay may still use the signed HTML snapshot from its archived source.
            }
        }

        $eventQuery = DB::table('order_mou_signature_events')
            ->where('order_mou_document_id', $documentId)
            ->whereNotNull('signed_html_path');
        $event = $activePath !== '' ? (clone $eventQuery)->where('signed_pdf_path', $activePath)->orderByDesc('signed_at')->first() : null;
        if (!$event) {
            $event = $eventQuery
                ->whereIn('signed_pdf_path', function ($query) use ($documentId) {
                    $query->select('signed_file_path')->from('order_mou_document_signed_versions')
                        ->where('order_mou_document_id', $documentId);
                })
                ->orderByDesc('signed_at')->first();
        }
        $path = is_object($event) ? trim((string) ($event->signed_html_path ?? '')) : '';
        return $path !== '' && Storage::disk('local')->exists($path) ? $path : null;
    }

    private function secondPartySignatureDataUri(string $html): ?string
    {
        if (!preg_match('~<img\\b(?=[^>]*\\balt="Tanda tangan PIHAK KEDUA")[^>]*>~is', $html, $image)) {
            return null;
        }
        if (!preg_match('~\\bsrc="([^"]+)"~is', $image[0], $source)) {
            return null;
        }
        $dataUri = html_entity_decode($source[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return preg_match('~^data:image/(?:png|jpe?g);base64,[A-Za-z0-9+/=\\r\\n]+$~i', $dataUri) ? $dataUri : null;
    }

    private function findDocument(Order $order, int $documentId): object
    {
        $document = DB::table('order_mou_documents')
            ->where('id', $documentId)
            ->where('order_id', $order->id)
            ->first();
        abort_unless($document, 404);
        return $document;
    }

    private function decodeContent(string $content): array
    {
        $decoded = json_decode($content, true);
        if (!is_array($decoded)) {
            throw ValidationException::withMessages([
                'document' => 'Isi versi MoU tidak dapat dibaca. Hubungi administrator.',
            ]);
        }
        return $decoded;
    }

    /** Strip executable markup while keeping the original template's CSS and page structure. */
    private function sanitizeRenderedHtml(string $html): string
    {
        if (!preg_match('/<html\b/i', $html) || !preg_match('/<body\b/i', $html)) {
            throw ValidationException::withMessages([
                'rendered_html' => 'HTML MoU tidak utuh. Muat ulang template legacy sebelum menyimpan.',
            ]);
        }

        $html = preg_replace('/<script\b[^>]*>.*?<\/script\s*>/is', '', $html) ?? $html;
        $html = preg_replace('/\s+on[a-z0-9_-]+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? $html;
        $html = preg_replace('/((?:href|src)\s*=\s*)(["\'])\s*javascript\s*:.*?\2/i', '$1"#"', $html) ?? $html;
        return $html;
    }


    /**
     * Prepare saved/editor HTML for DomPDF without redesigning the document.
     * Preserve the original template CSS and intentional page-break elements.
     */
    private function prepareHtmlForDomPdf(string $html): string
    {
        $html = $this->sanitizeRenderedHtml($html);

        // Remove the A4 simulation and visual selection CSS used only by the editor.
        $html = preg_replace(
            '~<style\b(?=[^>]*\bid\s*=\s*(["\'])mou-editor-a4-preview-style\1)[^>]*>.*?</style\s*>~is',
            '',
            $html
        ) ?? $html;

        // Remove the active-page marker without changing other class names.
        $html = preg_replace_callback(
            '~\sclass\s*=\s*(["\'])(.*?)\1~is',
            static function (array $matches): string {
                $quote = $matches[1];
                $classes = preg_split('/\s+/', trim($matches[2])) ?: [];
                $classes = array_values(array_filter(
                    $classes,
                    static fn (string $class): bool =>
                        $class !== '' && $class !== 'mou-editor-active-page'
                ));

                return $classes
                    ? ' class=' . $quote . implode(' ', $classes) . $quote
                    : '';
            },
            $html
        ) ?? $html;

        // Remove editor-only controls, but retain explicit page-break markers.
        $html = preg_replace(
            '~<([a-z][a-z0-9]*)\b(?=[^>]*\bdata-mou-editor-ui(?:\s|=|>))[^>]*>.*?</\1\s*>~is',
            '',
            $html
        ) ?? $html;

        return $this->inlineMouEditorStorageImages($html);
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
