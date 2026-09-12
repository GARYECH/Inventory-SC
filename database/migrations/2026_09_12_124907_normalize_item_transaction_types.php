<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | ADD TRANSACTION DETAIL
        |--------------------------------------------------------------------------
        */

        if (
            !Schema::hasColumn(
                'items',
                'transaction_detail'
            )
        ) {
            Schema::table(
                'items',
                function (Blueprint $table) {
                    $table->string(
                        'transaction_detail'
                    )
                    ->nullable()
                    ->after('transaction_type');
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | NORMALIZE LEGACY HANDY TALKIE
        |--------------------------------------------------------------------------
        */

        DB::table('items')
            ->whereIn(
                'transaction_type',
                [
                    'HT UV-82',
                    'HT UV82',
                    'UV-82',
                    'Baofeng UV-82',
                ]
            )
            ->update([
                'transaction_type' =>
                    'Handy Talkie',

                'transaction_detail' =>
                    'HT UV-82',
            ]);


        DB::table('items')
            ->whereIn(
                'transaction_type',
                [
                    'HT 888s',
                    'HT 888S',
                    'HT 888',
                    'Baofeng 888s',
                    'Baofeng 888S',
                ]
            )
            ->update([
                'transaction_type' =>
                    'Handy Talkie',

                'transaction_detail' =>
                    'HT 888s',
            ]);


        DB::table('items')
            ->whereIn(
                'transaction_type',
                [
                    'HT UV-5R',
                    'HT UV5R',
                    'UV-5R',
                    'Baofeng UV-5R',
                ]
            )
            ->update([
                'transaction_type' =>
                    'Handy Talkie',

                'transaction_detail' =>
                    'HT UV-5R',
            ]);


        /*
        |--------------------------------------------------------------------------
        | LEGACY GENERIC HT
        |--------------------------------------------------------------------------
        |
        | Coba tentukan detail dari nama barang.
        |
        */

        DB::table('items')
            ->where(
                'transaction_type',
                'HT'
            )
            ->where(
                'name',
                'like',
                '%UV-82%'
            )
            ->update([
                'transaction_type' =>
                    'Handy Talkie',

                'transaction_detail' =>
                    'HT UV-82',
            ]);


        DB::table('items')
            ->where(
                'transaction_type',
                'HT'
            )
            ->where(
                'name',
                'like',
                '%UV82%'
            )
            ->update([
                'transaction_type' =>
                    'Handy Talkie',

                'transaction_detail' =>
                    'HT UV-82',
            ]);


        DB::table('items')
            ->where(
                'transaction_type',
                'HT'
            )
            ->where(
                'name',
                'like',
                '%888%'
            )
            ->update([
                'transaction_type' =>
                    'Handy Talkie',

                'transaction_detail' =>
                    'HT 888s',
            ]);


        DB::table('items')
            ->where(
                'transaction_type',
                'HT'
            )
            ->where(
                'name',
                'like',
                '%UV-5R%'
            )
            ->update([
                'transaction_type' =>
                    'Handy Talkie',

                'transaction_detail' =>
                    'HT UV-5R',
            ]);


        DB::table('items')
            ->where(
                'transaction_type',
                'HT'
            )
            ->where(
                'name',
                'like',
                '%UV5R%'
            )
            ->update([
                'transaction_type' =>
                    'Handy Talkie',

                'transaction_detail' =>
                    'HT UV-5R',
            ]);


        /*
        |--------------------------------------------------------------------------
        | GENERIC HT YANG TIDAK BISA DIIDENTIFIKASI
        |--------------------------------------------------------------------------
        |
        | Tidak boleh dibiarkan dengan Transaction Type lama.
        | Dipakai default UV-82 untuk menjaga data masuk ke struktur baru.
        |
        */

        DB::table('items')
            ->where(
                'transaction_type',
                'HT'
            )
            ->update([
                'transaction_type' =>
                    'Handy Talkie',

                'transaction_detail' =>
                    'HT UV-82',
            ]);


        /*
        |--------------------------------------------------------------------------
        | NORMALIZE EQUIPMENT RENTAL
        |--------------------------------------------------------------------------
        */

        DB::table('items')
            ->where(
                'transaction_type',
                'Internal Rental'
            )
            ->update([
                'transaction_type' =>
                    'Peralatan',

                'transaction_detail' =>
                    'Internal Rental',
            ]);


        DB::table('items')
            ->where(
                'transaction_type',
                'Vendor Rental'
            )
            ->update([
                'transaction_type' =>
                    'Peralatan',

                'transaction_detail' =>
                    'Vendor Rental',
            ]);


        /*
        |--------------------------------------------------------------------------
        | PERALATAN TANPA DETAIL
        |--------------------------------------------------------------------------
        |
        | Karena Peralatan wajib punya detail,
        | data lama yang kosong diberi default Internal Rental.
        |
        */

        DB::table('items')
            ->where(
                'transaction_type',
                'Peralatan'
            )
            ->where(function ($query) {
                $query
                    ->whereNull(
                        'transaction_detail'
                    )
                    ->orWhere(
                        'transaction_detail',
                        ''
                    );
            })
            ->update([
                'transaction_detail' =>
                    'Internal Rental',
            ]);


        /*
        |--------------------------------------------------------------------------
        | NORMALIZE LEGACY ATK
        |--------------------------------------------------------------------------
        */

        DB::table('items')
            ->where(
                'transaction_type',
                'ATK'
            )
            ->update([
                'transaction_type' =>
                    'Habis Pakai',

                'transaction_detail' =>
                    null,

                'subcategory' =>
                    null,

                'requires_mou' =>
                    false,
            ]);


        /*
        |--------------------------------------------------------------------------
        | NORMALIZE LEGACY OBAT
        |--------------------------------------------------------------------------
        */

        DB::table('items')
            ->where(
                'transaction_type',
                'Obat'
            )
            ->update([
                'transaction_type' =>
                    'Habis Pakai',

                'transaction_detail' =>
                    null,

                'subcategory' =>
                    null,

                'requires_mou' =>
                    false,
            ]);


        /*
        |--------------------------------------------------------------------------
        | CLEAN HABIS PAKAI
        |--------------------------------------------------------------------------
        */

        DB::table('items')
            ->where(
                'transaction_type',
                'Habis Pakai'
            )
            ->update([
                'transaction_detail' =>
                    null,

                'subcategory' =>
                    null,

                'requires_mou' =>
                    false,
            ]);


        /*
        |--------------------------------------------------------------------------
        | NORMALIZE LEGACY SALE
        |--------------------------------------------------------------------------
        */

        DB::table('items')
            ->where(
                'transaction_type',
                'Sale'
            )
            ->update([
                'transaction_type' =>
                    'Merchandise',

                'transaction_detail' =>
                    null,
            ]);


        /*
        |--------------------------------------------------------------------------
        | CLEAN MERCHANDISE
        |--------------------------------------------------------------------------
        */

        DB::table('items')
            ->where(
                'transaction_type',
                'Merchandise'
            )
            ->update([
                'transaction_detail' =>
                    null,
            ]);


        /*
        |--------------------------------------------------------------------------
        | MERCHANDISE SUBCATEGORY BY NAME
        |--------------------------------------------------------------------------
        */

        DB::table('items')
            ->where(
                'transaction_type',
                'Merchandise'
            )
            ->where(function ($query) {
                $query
                    ->whereNull(
                        'subcategory'
                    )
                    ->orWhere(
                        'subcategory',
                        ''
                    );
            })
            ->where(
                'name',
                'like',
                '%ID Card%'
            )
            ->update([
                'subcategory' =>
                    'ID Card',
            ]);


        DB::table('items')
            ->where(
                'transaction_type',
                'Merchandise'
            )
            ->where(function ($query) {
                $query
                    ->whereNull(
                        'subcategory'
                    )
                    ->orWhere(
                        'subcategory',
                        ''
                    );
            })
            ->where(function ($query) {
                $query
                    ->where(
                        'name',
                        'like',
                        '%Baju%'
                    )
                    ->orWhere(
                        'name',
                        'like',
                        '%Shirt%'
                    )
                    ->orWhere(
                        'name',
                        'like',
                        '%Kaos%'
                    )
                    ->orWhere(
                        'name',
                        'like',
                        '%T-Shirt%'
                    );
            })
            ->update([
                'subcategory' =>
                    'Baju',
            ]);


        /*
        |--------------------------------------------------------------------------
        | REMAINING MERCHANDISE
        |--------------------------------------------------------------------------
        |
        | Merchandise lama yang tidak bisa diidentifikasi
        | masuk ke Lainnya.
        |
        */

        DB::table('items')
            ->where(
                'transaction_type',
                'Merchandise'
            )
            ->where(function ($query) {
                $query
                    ->whereNull(
                        'subcategory'
                    )
                    ->orWhere(
                        'subcategory',
                        ''
                    );
            })
            ->update([
                'subcategory' =>
                    'Lainnya',

                'requires_mou' =>
                    false,
            ]);


        /*
        |--------------------------------------------------------------------------
        | MERCHANDISE LAINNYA TIDAK BUTUH MOU
        |--------------------------------------------------------------------------
        */

        DB::table('items')
            ->where(
                'transaction_type',
                'Merchandise'
            )
            ->where(
                'subcategory',
                'Lainnya'
            )
            ->update([
                'requires_mou' =>
                    false,
            ]);


        /*
        |--------------------------------------------------------------------------
        | FINAL CLEANUP
        |--------------------------------------------------------------------------
        */

        DB::table('items')
            ->where(
                'transaction_type',
                'Handy Talkie'
            )
            ->where(function ($query) {
                $query
                    ->whereNull(
                        'transaction_detail'
                    )
                    ->orWhere(
                        'transaction_detail',
                        ''
                    );
            })
            ->update([
                'transaction_detail' =>
                    'HT UV-82',
            ]);


        DB::table('items')
            ->where(
                'transaction_type',
                'Peralatan'
            )
            ->where(function ($query) {
                $query
                    ->whereNull(
                        'transaction_detail'
                    )
                    ->orWhere(
                        'transaction_detail',
                        ''
                    );
            })
            ->update([
                'transaction_detail' =>
                    'Internal Rental',
            ]);
    }


    public function down(): void
    {
        /*
        | Jangan mengembalikan transaction_type lama,
        | karena data setelah normalisasi sudah menjadi
        | bagian dari struktur transaksi baru.
        |
        | Migration ini sengaja tidak melakukan rollback data.
        */
    }
};