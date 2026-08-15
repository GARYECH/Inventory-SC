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

        // 2. Simpan File PDF SOP
        if ($request->hasFile('sop_pdf')) {
            $request->validate(['sop_pdf' => 'mimes:pdf|max:5120']); 
            $path = $request->file('sop_pdf')->storeAs('documents', 'SOP_Student_Council.pdf', 'public');
            Setting::updateOrCreate(['key' => 'sop_pdf_path'], ['value' => $path]);
        }

        // 🌟 3. SIMPAN LOGO SC (BARU)
        if ($request->hasFile('logo_sc')) {
            $request->validate(['logo_sc' => 'image|mimes:png,jpg,jpeg|max:2048']); // Max 2MB
            $path = $request->file('logo_sc')->storeAs('images', 'logo_sc.png', 'public');
            Setting::updateOrCreate(['key' => 'logo_sc'], ['value' => $path]);
        }

        // 🌟 4. SIMPAN TTD BENDAHARA (BARU)
        if ($request->hasFile('ttd_bendahara')) {
            $request->validate(['ttd_bendahara' => 'image|mimes:png,jpg,jpeg|max:2048']); // Max 2MB
            $path = $request->file('ttd_bendahara')->storeAs('images', 'ttd_bendahara.png', 'public');
            Setting::updateOrCreate(['key' => 'ttd_bendahara'], ['value' => $path]);
        }

        return back()->with('success', 'System Settings berhasil diperbarui!');
    }
}