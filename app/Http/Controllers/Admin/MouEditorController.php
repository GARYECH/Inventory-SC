<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Support\MouNumberSynchronizer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    public function edit(Order $order, int $documentId)
    {
        $document = $this->findDocument($order, $documentId);
        $versions = DB::table('order_mou_document_versions')
            ->where('order_mou_document_id', $documentId)
            ->orderByDesc('version_number')
            ->get();

        $latestVersion = $versions->first();
        $content = $latestVersion ? $this->decodeContent($latestVersion->content) : ['html' => ''];
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
            'order', 'document', 'content', 'versions', 'latestVersion', 'sourceUrl', 'savedHtmlBase64'
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
