<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The earlier migration may already have created this table.
        if (!Schema::hasTable('order_mou_documents')) {
            Schema::create('order_mou_documents', function (Blueprint $table) {
                $table->id();

                $table->foreignId('order_id')
                    ->constrained('orders')
                    ->cascadeOnDelete();

                $table->string('mou_type');

                $table->string('signed_file_path')
                    ->nullable();

                $table->timestamps();

                $table->unique([
                    'order_id',
                    'mou_type',
                ]);
            });
        }
    }

    public function down(): void
    {
        // The earlier upgrade migration manages this table.
        // Its down() method handles removing the table.
    }
};