<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SearchDemoSeeder extends Seeder
{
    /**
     * Seed sample records that exercise every branch of the dashboard
     * smart search: a customer mobile number, a product barcode, and a
     * supplier serial number.
     */
    public function run(): void
    {
        $area = Area::firstOrCreate(['name' => 'Dhanmondi']);

        Customer::updateOrCreate(
            ['customer_code' => 'DEMO-CUST-1'],
            [
                'mobile' => '01712345678',
                'name' => 'Rahim Uddin',
                'area_id' => $area->id,
                'address' => 'House 12, Road 5, Dhanmondi, Dhaka',
                'owner_name' => 'Rahim Uddin',
                'previous_due' => 0,
                'credit_limit' => 0,
                'customer_type' => 'regular',
            ]
        );

        Supplier::updateOrCreate(
            ['supplier_code' => 'DEMO-SUP-1'],
            [
                'mobile' => '01898765432',
                'serial_number' => 'SN-2024-00123',
                'name' => 'Karim Electronics',
                'owner_name' => 'Abdul Karim',
                'mode' => 'credit',
                'address' => 'Gulshan-1, Dhaka',
                'previous_due' => 0,
            ]
        );

        Product::updateOrCreate(
            ['product_code' => '8901030895566'],
            [
                'name' => 'Logitech Wireless Mouse M185',
                'barcode' => '8901030895566',
                'sale_rate' => 850,
                'purchase_rate' => 650,
                'min_sale_rate' => 700,
                'wholesale_rate' => 780,
                'warranty_days' => 365,
                'status' => 'active',
            ]
        );
    }
}
