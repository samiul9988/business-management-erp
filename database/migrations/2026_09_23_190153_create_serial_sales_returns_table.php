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
        Schema::create('serial_sales_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained()->nullOnDelete();
            $table->string('return_no')->unique();
            $table->string('serial_no')->index();
            $table->string('invoice_no')->nullable();
            $table->string('customer_name')->default('Cash Customer');
            $table->string('product_code')->nullable();
            $table->string('product_name');
            $table->text('reason')->nullable();
            $table->date('return_date');
            $table->decimal('quantity', 12, 2)->default(1);
            $table->decimal('rate', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('serial_sales_returns');
    }
};
