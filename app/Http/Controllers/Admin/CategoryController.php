<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // 🌟 WAJIB DITAMBAH: Untuk membuat slug otomatis

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('items')->latest()->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name'
        ]);

        // 🌟 UPDATE: Masukkan name dan buat slug secara otomatis
        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) 
        ]);

        return back()->with('success', 'Kategori baru berhasil ditambahkan ke sistem!');
    }

    public function destroy(Category $category)
    {
        if ($category->items()->count() > 0) {
            return back()->with('error', 'Gagal! Kategori masih digunakan oleh beberapa aset.');
        }

        $category->delete();
        return back()->with('success', 'Kategori berhasil dihapus dari sistem.');
    }
}