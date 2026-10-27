<?php

namespace App\Support;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
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
            return ['changed' => false, 'archived_signed_count' => 0];
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
                'mou_number' => 'Transaksi ini sudah melewati tahap penandatanganan MoU. Nomor pada dokumen bertanda tangan tidak boleh diganti langsung; buat addendum/revisi resmi agar riwayat transaksi tetap utuh.',
            ]);
        }

        $oldDisplayNumber = $oldStoredNumber ?? (string) $order->order_number;
        $order->mou_number = $newNumber;
        $order->save();

        foreach ($signedDocuments as $document) {
            self::archiveSignedPath(
                (int) $document->id,
                (string) $document->signed_file_path,
                $oldDisplayNumber,
                $reason
            );

            DB::table('order_mou_documents')
                ->where('id', $document->id)
                ->update([
                    'signed_file_path' => null,
                    'requires_resign' => true,
                    'updated_at' => now(),
                ]);
        }

        return [
            'changed' => true,
            'archived_signed_count' => $signedDocuments->count(),
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

        DB::table('order_mou_document_signed_versions')->insert([
            'order_mou_document_id' => $documentId,
            'signed_file_path' => $signedFilePath,
            'mou_number' => $mouNumber,
            'reason' => $reason,
            'superseded_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
