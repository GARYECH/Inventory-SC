<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('orders', 'funding_source')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('funding_source', 40)
                    ->nullable()
                    ->after('order_type');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('orders', 'funding_source')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('funding_source');
            });
        }
    }
};

