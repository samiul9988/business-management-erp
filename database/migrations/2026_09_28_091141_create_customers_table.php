<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_code')->unique();
            $table->string('mobile')->nullable();
            $table->string('name');
            $table->foreignId('area_id')->nullable()->constrained()->nullOnDelete();
            $table->string('address')->nullable();
            $table->string('owner_name')->nullable();
            $table->string('office_phone')->nullable();
            $table->string('email')->nullable();
            $table->date('birthday')->nullable();
            $table->date('marriage_day')->nullable();
            $table->decimal('previous_due', 12, 2)->default(0);
            $table->decimal('credit_limit', 12, 2)->default(0);
            $table->string('customer_type')->default('regular');
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
