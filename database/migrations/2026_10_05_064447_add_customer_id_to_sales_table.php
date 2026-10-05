<?php

use App\Models\Customer;
use App\Models\Sale;
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
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
        });

        // Backfill: link every existing sale to a customer record, matching
        // by mobile number when possible and creating one otherwise, so the
        // invoice always has a Customer ID to display.
        Sale::query()->whereNull('customer_id')->each(function (Sale $sale): void {
            $customer = null;

            if ($sale->customer_mobile) {
                $customer = Customer::where('mobile', $sale->customer_mobile)->first();
            }

            if (! $customer) {
                $lastCode = Customer::query()->latest('id')->value('customer_code');
                $nextCode = (string) ($lastCode ? ((int) $lastCode + 1) : 1001);

                $customer = Customer::create([
                    'customer_code' => $nextCode,
                    'mobile' => $sale->customer_mobile,
                    'name' => $sale->customer_name,
                    'address' => $sale->customer_address,
                    'previous_due' => 0,
                    'credit_limit' => 0,
                    'customer_type' => $sale->sale_type === 'wholesale' ? 'wholesale' : 'regular',
                ]);
            }

            $sale->update(['customer_id' => $customer->id]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('customer_id');
        });
    }
};
