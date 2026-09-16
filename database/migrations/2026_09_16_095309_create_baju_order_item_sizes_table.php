<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_item_sizes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_item_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('size', 20);

            $table->string('division', 255);

            $table->integer('quantity');

            $table->integer('unit_price')->default(0);

            $table->integer('size_additional_price')->default(0);

            $table->integer('subtotal_price')->default(0);

            $table->timestamps();

            $table->index([
                'order_item_id',
                'size',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item_sizes');
    }
};