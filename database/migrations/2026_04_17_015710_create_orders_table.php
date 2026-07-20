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
        $table->string('order_number')->unique();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        
        // Data Birokrasi
        $table->string('phone_number');
        $table->string('proker_name');
        $table->string('department');
        $table->string('treasurer_name');
        
     $table->enum('order_type', ['Internal Rental', 'Vendor Rental', 'Sale']);
        
        $table->date('start_date')->nullable(); 
        $table->date('end_date')->nullable();   
        
        // Status & Dokumen
        $table->string('status')->default('Pending SOP');
        $table->boolean('is_sop_accepted')->default(false);
        $table->string('mou_file_path')->nullable(); 
        $table->string('invoice_file_path')->nullable(); 
        $table->string('payment_proof_path')->nullable(); 
        
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
