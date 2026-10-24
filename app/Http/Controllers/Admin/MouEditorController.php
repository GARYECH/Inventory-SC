<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
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

        $versionNumber = DB::transaction(function () use ($documentId, $content, $changeNote) {
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

        return redirect()
            ->route('admin.mou-editor.edit', [$order->id, $document->id])
            ->with('success', "MoU berhasil disimpan sebagai versi {$versionNumber}. Template legacy tidak ditimpa.");
    }

    public function preview(Request $request, Order $order, int $documentId)
    {
        $document = $this->findDocument($order, $documentId);
        $validated = $request->validate([
            'rendered_html' => ['required', 'string', 'max:1500000'],
        ]);
        $html = $this->sanitizeRenderedHtml($validated['rendered_html']);
        $html = $this->inlineMouEditorStorageImages($html);

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
        $versionHtml = $this->sanitizeRenderedHtml($content['html']);
$versionHtml = $this->inlineMouEditorStorageImages($versionHtml);
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
