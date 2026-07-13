<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->string('order_number')->unique(); // Remove nullable, we'll generate it on store
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('item_id')->constrained()->onDelete('cascade');
    $table->integer('quantity')->default(1);
    $table->decimal('total_price', 10, 2)->default(0); 
    $table->enum('status', ['Pending', 'Approved', 'Borrowed', 'Returned', 'Cancelled'])->default('Pending');
    $table->date('start_date');
    $table->date('end_date');
    $table->text('admin_notes')->nullable();
    $table->timestamps();
});
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
