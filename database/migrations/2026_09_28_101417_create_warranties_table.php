<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warranties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('invoice_no')->unique();
            $table->date('warranty_date');
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_mobile')->nullable();
            $table->foreignId('received_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('warranty_status')->default('pending');
            $table->date('estimated_delivery_date')->nullable();
            $table->string('claim_status')->nullable();
            $table->string('product_name')->nullable();
            $table->date('sale_out_date')->nullable();
            $table->string('serial_no')->nullable();
            $table->decimal('quantity', 12, 2)->default(1);
            $table->string('problem')->nullable();
            $table->string('condition')->nullable();
            $table->text('note')->nullable();
            $table->boolean('warranty_validity')->default(true);
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('transfer_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->date('transfer_date')->nullable();
            $table->string('receive_by_sr')->nullable();
            $table->foreignId('return_received_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('return_condition')->nullable();
            $table->date('received_date')->nullable();
            $table->foreignId('delivered_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->date('delivered_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warranties');
    }
};
