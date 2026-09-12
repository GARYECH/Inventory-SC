<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $categories =
            Category::withCount(
                'items'
            )
            ->latest()
            ->get();

        return view(
            'admin.categories.index',
            compact(
                'categories'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request
    ) {

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:categories,name',
            ],
        ]);


        $name =
            trim(
                $request->name
            );


        Category::create([
            'name' =>
                $name,

            'slug' =>
                Str::slug(
                    $name
                ),
        ]);


        return back()->with(
            'success',
            'Kategori baru berhasil ditambahkan ke sistem!'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Category $category
    ) {

        /*
        |--------------------------------------------------------------------------
        | PROTECT INVENTORY
        |--------------------------------------------------------------------------
        |
        | Category yang masih digunakan oleh item
        | tidak boleh dihapus.
        |
        */

        if (
            $category->items()->exists()
        ) {

            return back()->with(
                'error',
                'Kategori tidak dapat dihapus karena masih digunakan oleh barang.'
            );
        }


        $category->delete();


        return back()->with(
            'success',
            'Kategori berhasil dihapus dari sistem.'
        );
    }
}