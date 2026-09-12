<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | ADD TRANSACTION DETAIL
        |--------------------------------------------------------------------------
        |
        | transaction_type sekarang hanya boleh mempunyai 4 nilai:
        |
        | - Peralatan
        | - Handy Talkie
        | - Habis Pakai
        | - Merchandise
        |
        | transaction_detail digunakan untuk detail tambahan seperti:
        |
        | Peralatan
        |   -> Internal Rental
        |   -> Vendor Rental
        |
        | Handy Talkie
        |   -> HT UV-82
        |   -> HT 888s
        |   -> HT UV-5R
        |
        */

        if (!Schema::hasColumn('items', 'transaction_detail')) {

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
        | NORMALIZE OLD HANDY TALKIE DATA
        |--------------------------------------------------------------------------
        */

        DB::table('items')
            ->whereIn(
                'transaction_type',
                [
                    'HT UV-82',
                    'HT 888s',
                    'HT UV-5R',
                ]
            )
            ->get()
            ->each(
                function ($item) {

                    DB::table('items')
                        ->where(
                            'id',
                            $item->id
                        )
                        ->update([
                            'transaction_type' =>
                                'Handy Talkie',

                            'transaction_detail' =>
                                $item->transaction_type,
                        ]);

                }
            );


        /*
        |--------------------------------------------------------------------------
        | NORMALIZE OLD RENTAL DATA
        |--------------------------------------------------------------------------
        */

        DB::table('items')
            ->whereIn(
                'transaction_type',
                [
                    'Internal Rental',
                    'Vendor Rental',
                ]
            )
            ->get()
            ->each(
                function ($item) {

                    DB::table('items')
                        ->where(
                            'id',
                            $item->id
                        )
                        ->update([
                            'transaction_type' =>
                                'Peralatan',

                            'transaction_detail' =>
                                $item->transaction_type,
                        ]);

                }
            );


        /*
        |--------------------------------------------------------------------------
        | OLD PERALATAN
        |--------------------------------------------------------------------------
        */

        DB::table('items')
            ->where(
                'transaction_type',
                'Peralatan'
            )
            ->update([
                'transaction_type' =>
                    'Peralatan',
            ]);


        /*
        |--------------------------------------------------------------------------
        | OLD ATK
        |--------------------------------------------------------------------------
        |
        | Database lama tidak menyimpan apakah ATK tersebut:
        |
        | - Peralatan
        | - Habis Pakai
        |
        | Karena itu kita gunakan Habis Pakai sebagai default sementara.
        |
        | Setelah migration:
        |
        | contoh:
        | Stapler -> ubah manual menjadi Peralatan
        | Kertas   -> tetap Habis Pakai
        |
        */

        DB::table('items')
            ->where(
                'transaction_type',
                'ATK'
            )
            ->update([
                'transaction_type' =>
                    'Habis Pakai',
            ]);


        /*
        |--------------------------------------------------------------------------
        | OLD OBAT
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
            ]);


        /*
        |--------------------------------------------------------------------------
        | OLD SALE
        |--------------------------------------------------------------------------
        |
        | Sale sudah tidak digunakan.
        | Semua Sale lama dipindahkan ke Merchandise.
        |
        */

        DB::table('items')
            ->where(
                'transaction_type',
                'Sale'
            )
            ->update([
                'transaction_type' =>
                    'Merchandise',
            ]);
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (
            Schema::hasColumn(
                'items',
                'transaction_detail'
            )
        ) {

            Schema::table(
                'items',
                function (Blueprint $table) {

                    $table->dropColumn(
                        'transaction_detail'
                    );

                }
            );

        }
    }
};