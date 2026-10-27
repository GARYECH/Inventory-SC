<?php

namespace App\Support;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MouNumberSynchronizer
{
    public static function displayNumber(Order $order): string
    {
        $number = trim((string) ($order->mou_number ?? ''));

        return $number !== '' ? $number : (string) $order->order_number;
    }

    /** Read the text shown in the main MoU title number field. */
    public static function extractNumber(string $html): ?string
    {
        $blockPattern = '~<(?P<tag>[a-z][a-z0-9]*)\b[^>]*\bclass\s*=\s*(?:"[^"]*\btitle-number\b[^"]*"|\'[^\']*\btitle-number\b[^\']*\')[^>]*>.*?</(?P=tag)\s*>~is';

        if (!preg_match($blockPattern, $html, $matches)) {
            return null;
        }

        $text = html_entity_decode(
            strip_tags($matches[0]),
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );
        $text = preg_replace('/^\s*Nomor\s*:\s*/iu', '', trim($text)) ?? trim($text);
        $text = preg_replace('/\s+/u', ' ', trim($text)) ?? trim($text);

        if ($text === '') {
            return null;
        }

        return $text;
    }

    /** Change only the number span in the MoU title; leave the rest of the HTML untouched. */
    public static function replaceNumberInHtml(string $html, string $number): string
    {
        $number = trim($number);
        $blockPattern = '~<(?P<tag>[a-z][a-z0-9]*)\b[^>]*\bclass\s*=\s*(?:"[^"]*\btitle-number\b[^"]*"|\'[^\']*\btitle-number\b[^\']*\')[^>]*>.*?</(?P=tag)\s*>~is';

        $updated = preg_replace_callback(
            $blockPattern,
            static function (array $blockMatches) use ($number): string {
                $block = $blockMatches[0];
                $count = 0;
                $changed = preg_replace_callback(
                    '~<span\b([^>]*)>.*?</span>~is',
                    static function (array $spanMatches) use ($number): string {
                        return '<span' . $spanMatches[1] . '>'
                            . htmlspecialchars($number, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
                            . '</span>';
                    },
                    $block,
                    1,
                    $count
                );

                return $count === 1 && is_string($changed) ? $changed : $block;
            },
            $html,
            1
        );

        return is_string($updated) ? $updated : $html;
    }

    /**
     * Update orders.mou_number as the single source of truth.
     * Any previously signed PDF is preserved and archived, and its document is
     * flagged for a new signature. Call this inside a DB transaction.
     */
    /**
     * Update the canonical MoU number. If the website has a stored signed-HTML
     * snapshot, rebuild the current PDF with the same visual signature and
     * archive the prior PDF. Older/uploaded PDFs without a source snapshot are
     * archived and flagged for one re-sign rather than silently edited.
     * Call this method inside a DB transaction.
     */
    public static function updateOrderNumber(Order $order, ?string $requestedNumber, string $reason): array
    {
        $newNumber = trim((string) ($requestedNumber ?? ''));
        $newNumber = $newNumber === '' ? null : $newNumber;

        if ($newNumber !== null && mb_strlen($newNumber) > 255) {
            throw ValidationException::withMessages([
                'mou_number' => 'Nomor MoU maksimal 255 karakter.',
            ]);
        }

        $oldStoredNumber = trim((string) ($order->mou_number ?? ''));
        $oldStoredNumber = $oldStoredNumber === '' ? null : $oldStoredNumber;
        if ($oldStoredNumber === $newNumber) {
            return [
                'changed' => false,
                'archived_signed_count' => 0,
                'updated_signed_count' => 0,
                'requires_resign_count' => 0,
            ];
        }

        $signedDocuments = DB::table('order_mou_documents')
            ->where('order_id', $order->id)
            ->whereNotNull('signed_file_path')
            ->get();

        $terminalStatuses = [
            'Handed Over', 'Pending Return Review', 'Returned',
            'Returned (Damaged)', 'Pending Review BA', 'Resolved (Fine Paid)',
            'Rejected', 'Cancelled',
        ];

        if ($signedDocuments->isNotEmpty() && in_array((string) $order->status, $terminalStatuses, true)) {
            throw ValidationException::withMessages([
                'mou_number' => 'Transaksi ini sudah melewati tahap final. Jangan mengganti nomor pada MoU signed secara langsung; buat addendum/revisi resmi agar riwayat tetap utuh.',
            ]);
        }

        $oldDisplayNumber = $oldStoredNumber ?? (string) $order->order_number;
        $order->mou_number = $newNumber;
        $order->save();
        $newDisplayNumber = self::displayNumber($order);

        $updatedSignedCount = 0;
        $requiresResignCount = 0;

        foreach ($signedDocuments as $document) {
            $oldPath = (string) $document->signed_file_path;
            $event = DB::table('order_mou_signature_events')
                ->where('order_mou_document_id', $document->id)
                ->where('signed_pdf_path', $oldPath)
                ->whereNotNull('signed_html_path')
                ->orderByDesc('signed_at')
                ->first();

            $signedHtmlPath = $event ? trim((string) ($event->signed_html_path ?? '')) : '';
            $canRebuild = $signedHtmlPath !== ''
                && Storage::disk('local')->exists($signedHtmlPath);

            if ($canRebuild) {
                $signedHtml = Storage::disk('local')->get($signedHtmlPath);
                $updatedHtml = self::replaceNumberInHtml($signedHtml, $newDisplayNumber);
                $numberAfterUpdate = self::extractNumber($updatedHtml);

                // Only rebuild if the number was found and the saved visual signature remains embedded.
                $hasEmbeddedSignatureImage = preg_match('~<img\b[^>]*src=["\']data:image/[^;]+;base64,~i', $updatedHtml) === 1;

                if ($numberAfterUpdate === $newDisplayNumber && $hasEmbeddedSignatureImage) {
                    $pdfBytes = Pdf::loadHTML($updatedHtml)
                        ->setPaper('a4', 'portrait')
                        ->output();

                    $safeOrder = preg_replace('/[^A-Za-z0-9._-]/', '_', (string) $order->order_number) ?: (string) $order->id;
                    $safeType = preg_replace('/[^A-Za-z0-9_-]/', '_', (string) $document->mou_type) ?: 'mou';
                    $newPath = 'signed_mous/Signed_MoU_' . $safeOrder . '_' . $safeType
                        . '_RENOMOR_' . now()->format('Ymd_His') . '_' . Str::random(6) . '.pdf';

                    if (!Storage::disk('public')->put($newPath, $pdfBytes)) {
                        throw new \RuntimeException('PDF MoU signed dengan nomor baru gagal disimpan. Nomor belum dapat disinkronkan.');
                    }

                    self::archiveSignedPath(
                        (int) $document->id,
                        $oldPath,
                        $oldDisplayNumber,
                        $reason . ' — PDF diperbarui dengan nomor terbaru; tanda tangan visual dipertahankan.'
                    );

                    // Save a new private snapshot; keep the previous snapshot for rollback/audit.
                    $updatedHtmlPath = 'signed_mou_html/reissued_' . $safeOrder . '_'
                        . $safeType . '_' . now()->format('Ymd_His') . '_' . Str::random(6) . '.html';
                    if (!Storage::disk('local')->put($updatedHtmlPath, $updatedHtml)) {
                        Storage::disk('public')->delete($newPath);
                        throw new \RuntimeException('Snapshot HTML MoU signed gagal disimpan ulang. Nomor belum dapat disinkronkan.');
                    }

                    DB::table('order_mou_signature_events')
                        ->where('id', $event->id)
                        ->update([
                            'signed_pdf_path' => $newPath,
                            'signed_html_path' => $updatedHtmlPath,
                            'signed_pdf_sha256' => hash('sha256', $pdfBytes),
                            'updated_at' => now(),
                        ]);

                    DB::table('order_mou_documents')
                        ->where('id', $document->id)
                        ->update([
                            'signed_file_path' => $newPath,
                            'requires_resign' => false,
                            'updated_at' => now(),
                        ]);

                    $updatedSignedCount++;
                    continue;
                }
            }

            // Legacy and manually uploaded PDFs do not have signed HTML + signature source to rebuild safely.
            self::archiveSignedPath(
                (int) $document->id,
                $oldPath,
                $oldDisplayNumber,
                $reason . ' — sumber HTML bertanda tangan tidak tersedia; perlu tanda tangan ulang.'
            );

            DB::table('order_mou_documents')
                ->where('id', $document->id)
                ->update([
                    'signed_file_path' => null,
                    'requires_resign' => true,
                    'updated_at' => now(),
                ]);

            $requiresResignCount++;
        }

        return [
            'changed' => true,
            'archived_signed_count' => $signedDocuments->count(),
            'updated_signed_count' => $updatedSignedCount,
            'requires_resign_count' => $requiresResignCount,
        ];
    }

    public static function archiveSignedPath(
        int $documentId,
        string $signedFilePath,
        ?string $mouNumber,
        string $reason
    ): void {
        if (trim($signedFilePath) === '') {
            return;
        }

        $signedPdfHash = null;
        if (Storage::disk('public')->exists($signedFilePath)) {
            $signedPdfHash = hash('sha256', Storage::disk('public')->get($signedFilePath));
        }

        DB::table('order_mou_document_signed_versions')->insert([
            'order_mou_document_id' => $documentId,
            'signed_file_path' => $signedFilePath,
            'signed_pdf_sha256' => $signedPdfHash,
            'mou_number' => $mouNumber,
            'reason' => $reason,
            'superseded_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
