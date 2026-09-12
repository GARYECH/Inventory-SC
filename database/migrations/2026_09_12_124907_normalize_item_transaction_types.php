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

        if (!Schema::hasColumn(
            'items',
            'transaction_detail'
        )) {
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
        | HANDY TALKIE
        |--------------------------------------------------------------------------
        */

        DB::table('items')
            ->whereIn(
                'transaction_type',
                [
                    'HT',
                    'Handy Talkie',
                ]
            )
            ->update([
                'transaction_type' =>
                    'Handy Talkie',
            ]);


        /*
        |--------------------------------------------------------------------------
        | LEGACY HT DETAILS
        |--------------------------------------------------------------------------
        */

        DB::table('items')
            ->where(
                'transaction_type',
                'HT'
            )
            ->update([
                'transaction_type' =>
                    'Handy Talkie',
            ]);


        DB::table('items')
            ->whereIn(
                'name',
                [
                    'HT UV-82',
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
                'name',
                [
                    'HT 888s',
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
                'name',
                [
                    'HT UV-5R',
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
        | LEGACY EQUIPMENT RENTAL
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
        | LEGACY ATK
        |--------------------------------------------------------------------------
        |
        | Default ATK lama dipindahkan ke Habis Pakai.
        |
        | Barang reusable seperti Stapler nantinya bisa diubah
        | manual melalui Edit Item menjadi Peralatan.
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

                'transaction_detail' =>
                    null,

                'requires_mou' =>
                    false,
            ]);


        /*
        |--------------------------------------------------------------------------
        | LEGACY OBAT
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

                'requires_mou' =>
                    false,
            ]);


        /*
        |--------------------------------------------------------------------------
        | LEGACY SALE
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
        | MERCHANDISE CLEANUP
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
        | HABIS PAKAI CLEANUP
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

                'requires_mou' =>
                    false,
            ]);
    }


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