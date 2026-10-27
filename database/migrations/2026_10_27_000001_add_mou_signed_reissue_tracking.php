<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('order_mou_documents') && !Schema::hasColumn('order_mou_documents', 'requires_resign')) {
            Schema::table('order_mou_documents', function (Blueprint $table) {
                $table->boolean('requires_resign')->default(false)->after('signed_file_path');
            });
        }

        if (!Schema::hasTable('order_mou_document_signed_versions')) {
            Schema::create('order_mou_document_signed_versions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_mou_document_id')
                    ->constrained('order_mou_documents')
                    ->cascadeOnDelete();
                $table->string('signed_file_path');
                $table->string('mou_number')->nullable();
                $table->string('reason', 255)->nullable();
                $table->timestamp('superseded_at')->useCurrent();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_mou_document_signed_versions');

        if (Schema::hasTable('order_mou_documents') && Schema::hasColumn('order_mou_documents', 'requires_resign')) {
            Schema::table('order_mou_documents', function (Blueprint $table) {
                $table->dropColumn('requires_resign');
            });
        }
    }
};
