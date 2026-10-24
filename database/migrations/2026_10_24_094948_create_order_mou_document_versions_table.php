<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_mou_document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_mou_document_id')
                ->constrained('order_mou_documents')
                ->cascadeOnDelete();
            $table->unsignedInteger('version_number');
            $table->json('content');
            $table->string('status', 30)->default('draft');
            $table->text('change_note')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->unique(['order_mou_document_id', 'version_number'], 'order_mou_doc_version_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_mou_document_versions');
    }
};
