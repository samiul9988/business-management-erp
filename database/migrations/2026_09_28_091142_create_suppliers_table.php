<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_code')->unique();
            $table->string('mobile')->nullable();
            $table->string('name');
            $table->string('owner_name')->nullable();
            $table->string('mode')->default('cash');
            $table->string('address')->nullable();
            $table->string('email')->nullable();
            $table->decimal('previous_due', 12, 2)->default(0);
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
