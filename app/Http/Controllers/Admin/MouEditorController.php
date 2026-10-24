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
        $savedHtmlBase64 = base64_encode((string) ($content['html'] ?? ''));

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
        $pdf = Pdf::loadHTML($this->sanitizeRenderedHtml($content['html']))->setPaper('a4', 'portrait');
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
}
