<?php

namespace App\Support;

use RuntimeException;

/** Inserts a visual signature only into the PIHAK KEDUA signature cell. */
final class MouSignatureHtml
{
    public static function canInsertSecondPartySignature(string $html): bool
    {
        $cellPattern = '~<td\b[^>]*>.*?</td\s*>~is';
        if (!preg_match_all($cellPattern, $html, $cells)) {
            return false;
        }

        foreach ($cells[0] as $cell) {
            if (stripos(strip_tags($cell), 'PIHAK KEDUA') === false) {
                continue;
            }
            if (preg_match('~\bclass=["\'][^"\']*\b(?:signature-space|sign-space|signature-name|signatory-name)\b[^"\']*["\']~i', $cell)) {
                return true;
            }
        }

        return false;
    }

    public static function insertSecondPartySignature(string $html, string $dataUri): string
    {
        $safeDataUri = htmlspecialchars($dataUri, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $image = '<img src="' . $safeDataUri . '" alt="Tanda tangan PIHAK KEDUA" style="display:block;margin:0 auto;max-width:175px;max-height:54px;width:auto;height:auto;object-fit:contain;">';
        $cellPattern = '~<td\b[^>]*>.*?</td\s*>~is';
        $found = false;

        $result = preg_replace_callback($cellPattern, function (array $matches) use ($image, &$found): string {
            $cell = $matches[0];
            if ($found || stripos(strip_tags($cell), 'PIHAK KEDUA') === false) {
                return $cell;
            }

            $replacements = 0;
            $spacePattern = '~(<(?:div|span)\b[^>]*\bclass=["\'][^"\']*\b(?:signature-space|sign-space)\b[^"\']*["\'][^>]*>).*?(</(?:div|span)\s*>)~is';
            $updated = preg_replace_callback(
                $spacePattern,
                static function (array $spaceMatches) use ($image): string {
                    return $spaceMatches[1] . $image . $spaceMatches[2];
                },
                $cell,
                1,
                $replacements
            );

            if ($replacements === 0) {
                $namePattern = '~(<(?:div|span)\b[^>]*\bclass=["\'][^"\']*\b(?:signature-name|signatory-name)\b[^"\']*["\'][^>]*>)~i';
                $updated = preg_replace_callback(
                    $namePattern,
                    static function (array $nameMatches) use ($image): string {
                        return '<div class="signature-space" style="height:23mm;text-align:center;">' . $image . '</div>' . $nameMatches[1];
                    },
                    $cell,
                    1,
                    $replacements
                );
            }

            if ($replacements > 0 && is_string($updated)) {
                $found = true;
                return $updated;
            }
            return $cell;
        }, $html);

        if (!$found || !is_string($result) || $result === '') {
            throw new RuntimeException('Area tanda tangan PIHAK KEDUA tidak ditemukan pada template MoU ini.');
        }

        return $result;
    }
}
