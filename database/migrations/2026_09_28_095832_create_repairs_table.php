<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repairs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('invoice_no')->unique();
            $table->date('repair_date');
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_mobile')->nullable();
            $table->text('customer_address')->nullable();
            $table->foreignId('assigned_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->date('expected_delivery_date')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('repair_company_id')->nullable()->constrained()->nullOnDelete();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_no')->nullable();
            $table->text('problem')->nullable();
            $table->boolean('warranty_period')->default(false);
            $table->boolean('extra_received')->default(false);
            $table->string('parts_name')->nullable();
            $table->foreignId('parts_supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->date('parts_purchase_date')->nullable();
            $table->decimal('parts_purchase_rate', 12, 2)->default(0);
            $table->decimal('parts_quantity', 12, 2)->default(0);
            $table->decimal('parts_purchase_amount', 12, 2)->default(0);
            $table->unsignedInteger('parts_warranty_days')->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('payment_method')->default('cash');
            $table->decimal('paid', 12, 2)->default(0);
            $table->decimal('due', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repairs');
    }
};
