<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // Pastikan relasi category_id aman
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->text('description');
            $table->string('item_photo');
            $table->integer('stock_quantity');
            $table->integer('price')->default(0); 
            $table->string('condition_status')->default('Good');
            
            // 🌟 ATRIBUT PINTAR (Diubah jadi string agar fleksibel menampung 6 Kategori Baru) 🌟
            $table->string('transaction_type');
            $table->boolean('requires_mou')->default(true);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};