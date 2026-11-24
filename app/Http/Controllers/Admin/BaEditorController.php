<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Support\BaDocumentHtml;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BaEditorController extends Controller
{
    public function edit(Request $request, Order $order)
    {
        $order->load(['orderItems.item.category', 'orderItems.sizeBreakdowns']);
        $rentalDays = $this->getRentalDays($order->start_date, $order->end_date);
        $editorHtml = BaDocumentHtml::forOrder($order, $rentalDays, $request->boolean('reset'), true);
        $editorHtmlBase64 = base64_encode($editorHtml);

        return view('admin.ba-editor.edit', compact('order', 'editorHtmlBase64'));
    }

    public function save(Request $request, Order $order)
    {
        $validated = $request->validate([
            'rendered_html' => ['required', 'string', 'max:1500000'],
            'ba_ketua_signature' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'ba_treasurer_signature' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'ba_payment_proof' => ['nullable', 'file', 'mimes:png,jpg,jpeg,pdf', 'max:10240'],
        ], [
            'ba_ketua_signature.image' => 'Tanda tangan Ketua Acara harus berupa gambar PNG/JPG.',
            'ba_treasurer_signature.image' => 'Tanda tangan Bendahara Proker harus berupa gambar PNG/JPG.',
            'ba_payment_proof.mimes' => 'Bukti transfer harus berupa PNG, JPG, atau PDF.',
            'ba_payment_proof.max' => 'Ukuran bukti transfer maksimal 10 MB.',
        ]);

        $html = BaDocumentHtml::normaliseEditorHtml($validated['rendered_html']);
        $updates = ['ba_html' => $html];

        $uploads = [
            'ba_ketua_signature' => ['column' => 'ba_ketua_signature_path', 'folder' => 'ba_signatures'],
            'ba_treasurer_signature' => ['column' => 'ba_treasurer_signature_path', 'folder' => 'ba_signatures'],
            'ba_payment_proof' => ['column' => 'ba_payment_proof_path', 'folder' => 'ba_attachments'],
        ];

        $obsoleteFiles = [];

        foreach ($uploads as $field => $configuration) {
            if (!$request->hasFile($field)) {
                continue;
            }

            $file = $request->file($field);
            $extension = strtolower($file->getClientOriginalExtension());
            $safeOrderNumber = preg_replace('/[^A-Za-z0-9._-]/', '_', (string) $order->order_number) ?: (string) $order->id;
            $filename = 'BA_' . $safeOrderNumber . '_' . $field . '_' . now()->format('Ymd_His') . '_' . Str::random(5) . '.' . $extension;
            $newPath = $file->storeAs($configuration['folder'], $filename, 'public');

            if (!$newPath) {
                throw new \RuntimeException('Gagal menyimpan file ' . $field . '.');
            }

            $oldPath = $order->{$configuration['column']};
            $updates[$configuration['column']] = $newPath;

            if (is_string($oldPath) && $oldPath !== '') {
                $obsoleteFiles[] = $oldPath;
            }
        }

        $order->update($updates);

        foreach ($obsoleteFiles as $oldPath) {
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        return redirect()
            ->route('admin.ba-editor.edit', $order->id)
            ->with('success', 'Berita Acara berhasil disimpan. Periksa kembali preview PDF sebelum digunakan.');
    }

    private function getRentalDays($startDate, $endDate): int
    {
        if (empty($startDate) || empty($endDate)) {
            return 1;
        }

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->startOfDay();

        if ($end->lessThan($start)) {
            return 1;
        }

        return max(1, $start->diffInDays($end) - 1);
    }
}
