<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('order_mou_signature_events')) {
            return;
        }

        Schema::create('order_mou_signature_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('order_mou_document_id')->constrained('order_mou_documents')->cascadeOnDelete();
            $table->foreignId('source_version_id')->nullable()->constrained('order_mou_document_versions')->nullOnDelete();
            $table->unsignedInteger('source_version_number')->nullable();
            $table->foreignId('signer_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('signer_name')->nullable();
            $table->string('signature_method', 16);
            $table->string('signed_pdf_path');
            $table->char('signature_sha256', 64);
            $table->char('signed_pdf_sha256', 64);
            $table->char('ip_hash', 64)->nullable();
            $table->timestamp('signed_at');
            $table->timestamps();
            $table->index(['order_id', 'order_mou_document_id'], 'mou_signature_events_order_doc_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_mou_signature_events');
    }
};
