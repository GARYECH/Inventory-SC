<?php

namespace App\Support;

use App\Models\Order;
use Illuminate\Support\Facades\Storage;

class BaDocumentHtml
{
    /** Build the current Berita Acara HTML and resolve signature/attachment slots. */
    public static function forOrder(Order $order, int $rentalDays, bool $reset = false, bool $editorPreview = false): string
    {
        $html = !$reset && is_string($order->ba_html) && trim($order->ba_html) !== ''
            ? $order->ba_html
            : view('admin.pdf.berita_acara', [
                'order' => $order,
                'rentalDays' => $rentalDays,
            ])->render();

        $html = self::resolveSlots($html, $order, $editorPreview);

        return self::inlineStorageImages($html);
    }

    /** Restore dynamic asset placeholders before persisting the editable HTML. */
    public static function normaliseEditorHtml(string $html): string
    {
        $html = self::replaceSlotContents($html, 'event-treasurer', '<span style="display:block;height:64px;"></span>');
        $html = self::replaceSlotContents($html, 'event-chair', '<span style="display:block;height:64px;"></span>');
        $html = self::replaceSlotContents($html, 'payment-proof', '<p style="color:#777;font-style:italic;text-align:center;">Bukti transfer belum diunggah.</p>');

        return self::sanitize($html);
    }

    private static function resolveSlots(string $html, Order $order, bool $editorPreview = false): string
    {
        $treasurerMarkup = self::imageMarkup(
            $order->ba_treasurer_signature_path,
            'Tanda tangan Bendahara Proker',
            'max-width:180px;max-height:64px;object-fit:contain;',
            $editorPreview
        ) ?? '<span style="display:block;height:64px;"></span>';

        $chairMarkup = self::imageMarkup(
            $order->ba_ketua_signature_path,
            'Tanda tangan Ketua Acara',
            'max-width:180px;max-height:64px;object-fit:contain;',
            $editorPreview
        ) ?? '<span style="display:block;height:64px;"></span>';

        $proofMarkup = self::attachmentMarkup($order->ba_payment_proof_path, $editorPreview);

        $html = self::replaceSlotContents($html, 'event-treasurer', $treasurerMarkup);
        $html = self::replaceSlotContents($html, 'event-chair', $chairMarkup);
        $html = self::replaceSlotContents($html, 'payment-proof', $proofMarkup);

        return $html;
    }

    private static function replaceSlotContents(string $html, string $slot, string $replacement): string
    {
        $quotedSlot = preg_quote($slot, '~');
        $pattern = '~(<div\b(?=[^>]*\bdata-ba-slot\s*=\s*(["\'])' . $quotedSlot . '\2)[^>]*>).*?(</div>)~is';

        $updated = preg_replace_callback(
            $pattern,
            static fn (array $matches): string => $matches[1] . $replacement . $matches[3],
            $html
        );

        return is_string($updated) ? $updated : $html;
    }

    private static function imageMarkup(?string $path, string $alt, string $style, bool $editorPreview = false): ?string
    {
        if ($editorPreview && is_string($path) && $path !== '' && Storage::disk('public')->exists($path)) {
            $absolutePath = Storage::disk('public')->path($path);
            if (@getimagesize($absolutePath) !== false) {
                $url = asset('storage/' . ltrim($path, '/'));
                return '<img src="' . e($url) . '" alt="' . e($alt) . '" style="display:inline-block;' . e($style) . '">';
            }
        }

        $dataUri = self::publicImageDataUri($path);
        if ($dataUri === null) {
            return null;
        }

        return '<img src="' . e($dataUri) . '" alt="' . e($alt) . '" style="display:inline-block;' . e($style) . '">';
    }

    private static function attachmentMarkup(?string $path, bool $editorPreview = false): string
    {
        if (!is_string($path) || trim($path) === '' || !Storage::disk('public')->exists($path)) {
            return '<p style="color:#777;font-style:italic;text-align:center;">Bukti transfer belum diunggah.</p>';
        }

        $dataUri = self::publicImageDataUri($path);
        if ($dataUri !== null) {
            $source = $dataUri;
            if ($editorPreview) {
                $source = asset('storage/' . ltrim($path, '/'));
            }
            return '<img src="' . e($source) . '" alt="Bukti Transfer Denda" style="display:block;margin:0 auto;max-width:100%;max-height:650px;object-fit:contain;">';
        }

        $url = asset('storage/' . ltrim($path, '/'));
        $fileName = basename($path);

        return '<p style="text-align:center;padding:30px 12px;"><strong>Bukti transfer dilampirkan dalam format PDF:</strong><br><a href="' . e($url) . '" style="color:#1d4ed8;text-decoration:underline;">' . e($fileName) . '</a></p>';
    }

    private static function publicImageDataUri(?string $path): ?string
    {
        if (!is_string($path) || trim($path) === '' || !Storage::disk('public')->exists($path)) {
            return null;
        }

        $absolutePath = Storage::disk('public')->path($path);
        $imageInfo = @getimagesize($absolutePath);
        if (!is_array($imageInfo) || empty($imageInfo['mime']) || !str_starts_with((string) $imageInfo['mime'], 'image/')) {
            return null;
        }

        $bytes = @file_get_contents($absolutePath);
        if (!is_string($bytes) || $bytes === '') {
            return null;
        }

        return 'data:' . $imageInfo['mime'] . ';base64,' . base64_encode($bytes);
    }

    /** Inline local storage images so the browser editor and DomPDF can both display them. */
    private static function inlineStorageImages(string $html): string
    {
        $storageRoot = realpath(storage_path('app/public'));
        if ($storageRoot === false) {
            return $html;
        }

        $storageRootPrefix = rtrim($storageRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $updatedHtml = preg_replace_callback(
            '~(?P<before><img\b[^>]*?\bsrc\s*=\s*)(?P<quote>["\'])(?P<src>.*?)\k<quote>(?P<after>[^>]*>)~is',
            static function (array $matches) use ($storageRootPrefix): string {
                $source = html_entity_decode($matches['src'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                if ($source === '' || preg_match('~^(?:https?:|data:|//)~i', $source)) {
                    return $matches[0];
                }

                $source = rawurldecode($source);
                $realPath = realpath($source);
                if ($realPath === false
                    || !str_starts_with($realPath, $storageRootPrefix)
                    || !is_file($realPath)) {
                    return $matches[0];
                }

                $mimeType = function_exists('mime_content_type') ? mime_content_type($realPath) : false;
                if (!is_string($mimeType) || !str_starts_with(strtolower($mimeType), 'image/')) {
                    return $matches[0];
                }

                $imageBytes = @file_get_contents($realPath);
                if ($imageBytes === false) {
                    return $matches[0];
                }

                $dataUri = 'data:' . $mimeType . ';base64,' . base64_encode($imageBytes);

                return $matches['before'] . $matches['quote'] . $dataUri . $matches['quote'] . $matches['after'];
            },
            $html
        );

        return is_string($updatedHtml) ? $updatedHtml : $html;
    }

    private static function sanitize(string $html): string
    {
        // Keep the rich HTML/CSS document, while removing executable or embedded active content.
        $html = preg_replace('~<(script|iframe|object|embed|form|button)\b[^>]*>.*?</\1\s*>~is', '', $html) ?? $html;
        $html = preg_replace('~<(script|iframe|object|embed|form|input|button)\b[^>]*/?>~is', '', $html) ?? $html;
        $html = preg_replace('/\s+on[a-z0-9_-]+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? $html;
        $html = preg_replace('/(href|src)\s*=\s*(["\'])\s*javascript:[^"\']*\2/i', '$1="#"', $html) ?? $html;

        return $html;
    }
}
