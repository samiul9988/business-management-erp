<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('share_token', 16)->nullable()->unique()->after('invoice_no');
        });

        // Backfill a public share token for sales created before this column
        // existed, so every invoice (new or old) has a shareable link.
        DB::table('sales')->whereNull('share_token')->orderBy('id')->get(['id'])->each(function (object $sale): void {
            DB::table('sales')->where('id', $sale->id)->update(['share_token' => Str::random(12)]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('share_token');
        });
    }
};
