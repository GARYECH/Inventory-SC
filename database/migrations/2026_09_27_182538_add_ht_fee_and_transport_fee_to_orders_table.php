<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('orders', 'ht_fee')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->unsignedBigInteger('ht_fee')
                    ->default(0)
                    ->after('end_time');
            });
        }

        if (!Schema::hasColumn('orders', 'transport_fee')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->unsignedBigInteger('transport_fee')
                    ->default(0)
                    ->after('ht_fee');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('orders', 'ht_fee')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('ht_fee');
            });
        }

        if (Schema::hasColumn('orders', 'transport_fee')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('transport_fee');
            });
        }
    }
};