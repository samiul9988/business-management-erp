<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cheques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('bank_name');
            $table->string('branch_name')->nullable();
            $table->string('cheque_no');
            $table->decimal('amount', 12, 2);
            $table->string('status')->default('pending');
            $table->date('issue_date');
            $table->date('cheque_date');
            $table->date('reminder_date')->nullable();
            $table->date('submit_date')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cheques');
    }
};
