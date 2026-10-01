<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_code')->unique();
            $table->string('name');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('color_id')->nullable()->constrained()->nullOnDelete();
            $table->string('model')->nullable();
            $table->string('barcode')->nullable();
            $table->string('reference')->nullable();
            $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('vat', 8, 2)->default(0);
            $table->unsignedInteger('warranty_days')->default(0);
            $table->unsignedInteger('reorder_level')->default(0);
            $table->decimal('purchase_rate', 12, 2)->default(0);
            $table->decimal('sale_rate', 12, 2)->default(0);
            $table->decimal('min_sale_rate', 12, 2)->default(0);
            $table->decimal('wholesale_rate', 12, 2)->default(0);
            $table->boolean('is_service')->default(false);
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
