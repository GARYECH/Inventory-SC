<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    // Tambah link desain di tabel orders
    Schema::table('orders', function (Blueprint $table) {
        $table->string('design_link')->nullable()->after('notes');
    });

    // Tambah ukuran (size) di tabel order_items
    Schema::table('order_items', function (Blueprint $table) {
        $table->string('size')->nullable()->after('quantity');
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};
