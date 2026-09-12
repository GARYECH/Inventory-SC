<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->time('start_time')->nullable()->after('start_date');
            $table->time('end_time')->nullable()->after('end_date');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->string('size')->nullable()->after('quantity');
            $table->text('design_link')->nullable()->after('size');
            $table->integer('size_additional_price')->default(0)->after('design_link');
        });

        Schema::create('order_mou_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('mou_type');

            $table->string('signed_file_path')->nullable();

            $table->timestamps();

            $table->unique([
                'order_id',
                'mou_type'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_mou_documents');

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'size',
                'design_link',
                'size_additional_price'
            ]);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'start_time',
                'end_time'
            ]);
        });
    }
};