<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('order_mou_signature_events')
            && !Schema::hasColumn('order_mou_signature_events', 'signed_html_path')) {
            Schema::table('order_mou_signature_events', function (Blueprint $table) {
                $table->string('signed_html_path')->nullable()->after('signed_pdf_path');
            });
        }

        if (Schema::hasTable('order_mou_document_signed_versions')
            && !Schema::hasColumn('order_mou_document_signed_versions', 'signed_pdf_sha256')) {
            Schema::table('order_mou_document_signed_versions', function (Blueprint $table) {
                $table->char('signed_pdf_sha256', 64)->nullable()->after('signed_file_path');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('order_mou_document_signed_versions')
            && Schema::hasColumn('order_mou_document_signed_versions', 'signed_pdf_sha256')) {
            Schema::table('order_mou_document_signed_versions', function (Blueprint $table) {
                $table->dropColumn('signed_pdf_sha256');
            });
        }

        if (Schema::hasTable('order_mou_signature_events')
            && Schema::hasColumn('order_mou_signature_events', 'signed_html_path')) {
            Schema::table('order_mou_signature_events', function (Blueprint $table) {
                $table->dropColumn('signed_html_path');
            });
        }
    }
};
