<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        // Ambil semua setting dan ubah jadi array [ 'key' => 'value' ]
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // 1. Simpan Teks MoU
        if ($request->has('mou_internal')) {
            Setting::updateOrCreate(['key' => 'mou_internal'], ['value' => $request->mou_internal]);
        }
        if ($request->has('mou_vendor')) {
            Setting::updateOrCreate(['key' => 'mou_vendor'], ['value' => $request->mou_vendor]);
        }

        // 2. Simpan File PDF SOP (Jika Admin Upload File Baru)
        if ($request->hasFile('sop_pdf')) {
            $request->validate(['sop_pdf' => 'mimes:pdf|max:5120']); // Max 5MB
            
            $file = $request->file('sop_pdf');
            $fileName = 'SOP_Student_Council.' . $file->getClientOriginalExtension();
            
            // Simpan ke folder storage/app/public/documents
            $path = $file->storeAs('documents', $fileName, 'public');
            
            Setting::updateOrCreate(['key' => 'sop_pdf_path'], ['value' => $path]);
        }

        return back()->with('success', 'System Settings berhasil diperbarui!');
    }
}