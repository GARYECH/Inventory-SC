<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('return_drive_link')->nullable()->after('signed_kwitansi');
        });
    }
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('return_drive_link');
        });
    }
};