<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | ORDER ITEMS -> ITEMS
        |--------------------------------------------------------------------------
        |
        | Item tidak boleh dihapus kalau sudah digunakan
        | oleh transaction history.
        |
        */

        Schema::table(
            'order_items',
            function (Blueprint $table) {

                $table->dropForeign([
                    'item_id'
                ]);

                $table->foreign(
                    'item_id'
                )
                ->references('id')
                ->on('items')
                ->restrictOnDelete();
            }
        );


        /*
        |--------------------------------------------------------------------------
        | ITEMS -> CATEGORIES
        |--------------------------------------------------------------------------
        |
        | Category tidak boleh dihapus kalau masih
        | digunakan oleh item.
        |
        */

        Schema::table(
            'items',
            function (Blueprint $table) {

                $table->dropForeign([
                    'category_id'
                ]);

                $table->foreign(
                    'category_id'
                )
                ->references('id')
                ->on('categories')
                ->restrictOnDelete();
            }
        );
    }


    public function down(): void
    {
        Schema::table(
            'order_items',
            function (Blueprint $table) {

                $table->dropForeign([
                    'item_id'
                ]);

                $table->foreign(
                    'item_id'
                )
                ->references('id')
                ->on('items')
                ->cascadeOnDelete();
            }
        );


        Schema::table(
            'items',
            function (Blueprint $table) {

                $table->dropForeign([
                    'category_id'
                ]);

                $table->foreign(
                    'category_id'
                )
                ->references('id')
                ->on('categories')
                ->cascadeOnDelete();
            }
        );
    }
};