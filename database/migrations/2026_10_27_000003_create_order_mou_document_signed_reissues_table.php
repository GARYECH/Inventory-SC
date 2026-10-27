<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('order_mou_document_signed_reissues')) {
            Schema::create('order_mou_document_signed_reissues', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_mou_document_id')
                    ->constrained('order_mou_documents')
                    ->cascadeOnDelete();
                $table->string('source_signed_file_path');
                $table->char('source_signed_pdf_sha256', 64)->nullable();
                $table->string('reissued_file_path');
                $table->char('reissued_pdf_sha256', 64);
                $table->string('mou_number')->nullable();
                $table->string('reason', 255)->nullable();
                $table->unsignedBigInteger('issued_by')->nullable();
                $table->timestamp('issued_at')->useCurrent();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_mou_document_signed_reissues');
    }
};
