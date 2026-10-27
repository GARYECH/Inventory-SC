<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('order_mou_document_signed_reissues')
            && !Schema::hasColumn('order_mou_document_signed_reissues', 'reissued_signed_html_path')) {
            Schema::table('order_mou_document_signed_reissues', function (Blueprint $table) {
                $table->string('reissued_signed_html_path')->nullable()->after('reissued_pdf_sha256');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('order_mou_document_signed_reissues')
            && Schema::hasColumn('order_mou_document_signed_reissues', 'reissued_signed_html_path')) {
            Schema::table('order_mou_document_signed_reissues', function (Blueprint $table) {
                $table->dropColumn('reissued_signed_html_path');
            });
        }
    }
};
