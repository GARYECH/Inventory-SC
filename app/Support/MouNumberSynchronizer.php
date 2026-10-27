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
    /**
     * Keep the latest official MoU number in the active signed PDF.
     * The prior PDF is retained in order_mou_document_signed_versions.
     * This is a visual reissue: it preserves the existing PDF pages/signature image
     * and overlays the number field; it does not preserve a cryptographic PDF signature.
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
        $numberChanged = $oldStoredNumber !== $newNumber;

        $documents = DB::table('order_mou_documents')
            ->where('order_id', $order->id)
            ->get();

        $hasPendingReissue = $documents->contains(
            static fn ($document): bool => (bool) ($document->requires_resign ?? false)
        );

        if (!$numberChanged && !$hasPendingReissue) {
            return [
                'changed' => false,
                'archived_signed_count' => 0,
                'updated_signed_count' => 0,
                'requires_resign_count' => 0,
            ];
        }

        $hasSignedHistory = $documents->contains(
            static fn ($document): bool => !empty($document->signed_file_path)
                || (bool) ($document->requires_resign ?? false)
        );

        $terminalStatuses = [
            'Handed Over', 'Pending Return Review', 'Returned',
            'Returned (Damaged)', 'Pending Review BA', 'Resolved (Fine Paid)',
            'Rejected', 'Cancelled',
        ];

        if ($numberChanged && $hasSignedHistory
            && in_array((string) $order->status, $terminalStatuses, true)) {
            throw ValidationException::withMessages([
                'mou_number' => 'Transaksi ini sudah melewati tahap final. Jangan mengubah nomor pada MoU signed secara langsung; buat addendum/revisi resmi agar riwayat tetap utuh.',
            ]);
        }

        $oldDisplayNumber = $oldStoredNumber ?? (string) $order->order_number;
        if ($numberChanged) {
            $order->mou_number = $newNumber;
            $order->save();
        }
        $newDisplayNumber = self::displayNumber($order);

        $updatedSignedCount = 0;
        $archivedSignedCount = 0;
        $requiresResignCount = 0;
        $createdPaths = [];

        try {
            foreach ($documents as $document) {
                $activePath = trim((string) ($document->signed_file_path ?? ''));
                $needsReissue = (bool) ($document->requires_resign ?? false);

                if (!$numberChanged && !$needsReissue) {
                    continue;
                }

                if ($activePath === '' && !$needsReissue) {
                    continue;
                }

                $sourcePath = $activePath;
                $sourceWasArchived = false;

                // Recover the older signed PDF if an earlier release already moved it to the archive.
                if ($sourcePath === '') {
                    $archivedVersion = DB::table('order_mou_document_signed_versions')
                        ->where('order_mou_document_id', $document->id)
                        ->orderByDesc('superseded_at')
                        ->orderByDesc('id')
                        ->first();

                    $archivePath = trim((string) ($archivedVersion->signed_file_path ?? ''));
                    if ($archivePath === '') {
                        $requiresResignCount++;
                        continue;
                    }

                    $sourcePath = $archivePath;
                    $sourceWasArchived = true;
                }

                if (!Storage::disk('public')->exists($sourcePath)) {
                    if (!$sourceWasArchived) {
                        DB::table('order_mou_documents')
                            ->where('id', $document->id)
                            ->update([
                                'signed_file_path' => null,
                                'requires_resign' => true,
                                'updated_at' => now(),
                            ]);
                    }
                    $requiresResignCount++;
                    continue;
                }

                // Import the existing PDF pages and overlay only the number line on page one.
                $pdfBytes = self::replaceNumberOnSignedPdf(
                    $sourcePath,
                    $newDisplayNumber,
                    (int) $document->id
                );

                $safeOrder = preg_replace('/[^A-Za-z0-9._-]/', '_', (string) $order->order_number)
                    ?: (string) $order->id;
                $safeType = preg_replace('/[^A-Za-z0-9_-]/', '_', (string) $document->mou_type)
                    ?: 'mou';
                $newPath = 'signed_mous/Signed_MoU_' . $safeOrder . '_' . $safeType
                    . '_NOMOR_' . now()->format('Ymd_His') . '_' . Str::random(6) . '.pdf';

                if (!Storage::disk('public')->put($newPath, $pdfBytes)) {
                    throw new \RuntimeException('PDF MoU dengan nomor terbaru gagal disimpan. Perubahan dibatalkan.');
                }
                $createdPaths[] = $newPath;

                if (!$sourceWasArchived && $activePath !== '') {
                    self::archiveSignedPath(
                        (int) $document->id,
                        $sourcePath,
                        $oldDisplayNumber,
                        $reason . ' — PDF lama diarsipkan; nomor pada salinan aktif diperbarui dengan overlay visual.'
                    );
                    $archivedSignedCount++;
                }

                $event = DB::table('order_mou_signature_events')
                    ->where('order_mou_document_id', $document->id)
                    ->where('signed_pdf_path', $sourcePath)
                    ->orderByDesc('signed_at')
                    ->first();

                if ($event) {
                    DB::table('order_mou_signature_events')
                        ->where('id', $event->id)
                        ->update([
                            'signed_pdf_path' => $newPath,
                            'signed_html_path' => null,
                            'signed_pdf_sha256' => hash('sha256', $pdfBytes),
                            'updated_at' => now(),
                        ]);
                }

                DB::table('order_mou_documents')
                    ->where('id', $document->id)
                    ->update([
                        'signed_file_path' => $newPath,
                        'requires_resign' => false,
                        'updated_at' => now(),
                    ]);

                $updatedSignedCount++;
            }
        } catch (\Throwable $exception) {
            foreach ($createdPaths as $createdPath) {
                Storage::disk('public')->delete($createdPath);
            }
            throw $exception;
        }

        return [
            'changed' => $numberChanged,
            'archived_signed_count' => $archivedSignedCount,
            'updated_signed_count' => $updatedSignedCount,
            'requires_resign_count' => $requiresResignCount,
        ];
    }

    /** Locate the number line from saved source HTML where possible. */
    private static function numberLineTopOnA4(int $documentId, string $sourcePath): float
    {
        $html = null;
        $event = DB::table('order_mou_signature_events')
            ->where('order_mou_document_id', $documentId)
            ->where('signed_pdf_path', $sourcePath)
            ->whereNotNull('signed_html_path')
            ->orderByDesc('signed_at')
            ->first();

        $signedHtmlPath = trim((string) ($event->signed_html_path ?? ''));
        if ($signedHtmlPath !== '' && Storage::disk('local')->exists($signedHtmlPath)) {
            $html = Storage::disk('local')->get($signedHtmlPath);
        }

        if (!is_string($html) || $html === '') {
            $version = DB::table('order_mou_document_versions')
                ->where('order_mou_document_id', $documentId)
                ->orderByDesc('version_number')
                ->first();
            if ($version) {
                $content = json_decode((string) $version->content, true);
                if (is_array($content) && !empty($content['html'])) {
                    $html = (string) $content['html'];
                }
            }
        }

        if (is_string($html)
            && preg_match('~<[^>]+\bclass\s*=\s*(?:"[^"]*\btitle-sub\b[^"]*"|\'[^\']*\btitle-sub\b[^\']*\')~i', $html) === 1) {
            return 77.5;
        }

        return 71.5;
    }

    /**
     * Preserve the whole existing signed PDF and visually replace its number line.
     * The original PDF is archived first; the active copy keeps its existing signature image.
     */
    private static function replaceNumberOnSignedPdf(string $sourcePath, string $number, int $documentId): string
    {
        if (!class_exists(\setasign\Fpdi\Fpdi::class)) {
            throw new \RuntimeException(
                'Library FPDI belum tersedia. Jalankan composer install setelah dependensi setasign/fpdf dan setasign/fpdi ditambahkan.'
            );
        }

        $absolutePath = Storage::disk('public')->path($sourcePath);
        if (!is_file($absolutePath)) {
            throw new \RuntimeException('File PDF signed lama tidak ditemukan: ' . $sourcePath);
        }

        try {
            $pdf = new \setasign\Fpdi\Fpdi('P', 'mm', 'A4');
            $pdf->SetAutoPageBreak(false);
            $pdf->SetMargins(0, 0, 0);
            $pageCount = $pdf->setSourceFile($absolutePath);

            if ($pageCount < 1) {
                throw new \RuntimeException('PDF signed lama tidak memiliki halaman.');
            }

            // Read the source HTML if available to account for template variants.
            // Legacy PDFs without any source use the position verified from the existing signed ID Card PDF.
            $numberTopOnA4 = self::numberLineTopOnA4($documentId, $sourcePath);

            for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
                $templateId = $pdf->importPage($pageNumber);
                $size = $pdf->getTemplateSize($templateId);
                $orientation = $size['orientation']
                    ?? (((float) $size['width'] > (float) $size['height']) ? 'L' : 'P');

                $pdf->AddPage($orientation, [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId, 0, 0, $size['width'], $size['height'], true);

                if ($pageNumber === 1) {
                    $pageWidth = (float) $size['width'];
                    $pageHeight = (float) $size['height'];
                    $scale = $pageHeight / 297.0;
                    $barWidth = min(176.0, max(120.0, $pageWidth - 24.0));
                    $barHeight = 8.5 * $scale;
                    $barX = ($pageWidth - $barWidth) / 2;
                    $barY = $numberTopOnA4 * $scale;

                    $pdf->SetFillColor(255, 255, 255);
                    $pdf->Rect($barX, $barY, $barWidth, $barHeight, 'F');
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->SetFont('Times', '', 11.5);
                    $pdf->SetXY($barX, $barY + (0.6 * $scale));
                    $pdf->Cell(
                        $barWidth,
                        $barHeight - (1.0 * $scale),
                        'Nomor: ' . $number,
                        0,
                        0,
                        'C',
                        false
                    );
                }
            }

            $bytes = $pdf->Output('S');
            if (!is_string($bytes) || !str_starts_with($bytes, '%PDF-')) {
                throw new \RuntimeException('Hasil pembaruan PDF tidak valid.');
            }

            return $bytes;
        } catch (\Throwable $exception) {
            throw new \RuntimeException(
                'Tidak dapat memperbarui nomor pada PDF signed. File lama tetap dipertahankan. Detail: '
                    . $exception->getMessage(),
                0,
                $exception
            );
        }
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
