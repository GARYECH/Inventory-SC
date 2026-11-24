<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->longText('ba_html')->nullable();
            $table->string('ba_ketua_signature_path')->nullable();
            $table->string('ba_treasurer_signature_path')->nullable();
            $table->string('ba_payment_proof_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'ba_html',
                'ba_ketua_signature_path',
                'ba_treasurer_signature_path',
                'ba_payment_proof_path',
            ]);
        });
    }
};
