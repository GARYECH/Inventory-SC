<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('ba_number')->nullable();
            $table->string('ba_date')->nullable();
            $table->string('ba_due_date')->nullable();
            $table->text('ba_description')->nullable();
            $table->integer('ba_total_fine')->nullable();
            $table->string('signed_ba_file')->nullable();
        });
    }
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['ba_number', 'ba_date', 'ba_due_date', 'ba_description', 'ba_total_fine', 'signed_ba_file']);
        });
    }
};