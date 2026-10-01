<?php

namespace Tests\Feature;

use App\Models\Sale;
use App\Models\SerialSalesReturn;
use App\Models\ServiceEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_sales_module(): void
    {
        $response = $this->actingAs(User::factory()->create())->get(route('sales.index'));

        $response->assertStatus(200);
        $response->assertSee('Sales Entry');
        $response->assertSee('Customer Due List');
    }

    public function test_authenticated_user_can_open_a_sales_section(): void
    {
        $response = $this->actingAs(User::factory()->create())->get(route('sales.section', 'entry'));

        $response->assertOk();
        $response->assertSee('Create a complete retail or wholesale invoice');
    }

    public function test_unknown_sales_section_returns_not_found(): void
    {
        $response = $this->actingAs(User::factory()->create())->get(route('sales.section', 'unknown'));

        $response->assertNotFound();
    }

    public function test_authenticated_user_can_save_a_sales_entry_with_items(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('sales.entry.store'), [
            'sale_type' => 'retail', 'customer_name' => 'Test Customer', 'sale_date' => '2026-09-24',
            'vat' => 10, 'discount' => 5, 'transport_cost' => 2, 'paid' => 50,
            'items' => [['product_name' => 'Test Laptop', 'product_code' => 'LT-01', 'quantity' => 1, 'rate' => 100, 'discount_percent' => 10]],
        ]);

        $response->assertRedirect(route('sales.entry.create'));
        $this->assertDatabaseHas('sales', ['customer_name' => 'Test Customer', 'subtotal' => 100, 'discount' => 15, 'total' => 97, 'paid' => 50, 'due' => 47]);
        $this->assertDatabaseHas('sale_items', ['product_name' => 'Test Laptop', 'total' => 90]);
        $this->assertSame(1, Sale::query()->where('customer_name', 'Test Customer')->firstOrFail()->items()->count());
    }

    public function test_sales_entry_requires_at_least_one_product(): void
    {
        $response = $this->actingAs(User::factory()->create())->post(route('sales.entry.store'), [
            'sale_type' => 'retail', 'customer_name' => 'Test Customer', 'sale_date' => '2026-09-24', 'items' => [],
        ]);

        $response->assertSessionHasErrors('items');
    }

    public function test_authenticated_user_can_save_a_service_entry_with_items(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('sales.service-entry.store'), [
            'service_type' => 'repair', 'customer_name' => 'Service Customer', 'service_date' => '2026-09-24',
            'technician' => 'Khan', 'vat' => 10, 'discount' => 5, 'paid' => 50,
            'items' => [['service_name' => 'Laptop Repair', 'device_serial' => 'SN-01', 'quantity' => 1, 'rate' => 100, 'discount_percent' => 10]],
        ]);

        $response->assertRedirect(route('sales.service-entry.create'));
        $this->assertDatabaseHas('service_entries', ['customer_name' => 'Service Customer', 'technician' => 'Khan', 'subtotal' => 100, 'discount' => 15, 'total' => 95, 'due' => 45]);
        $this->assertDatabaseHas('service_items', ['service_name' => 'Laptop Repair', 'device_serial' => 'SN-01', 'total' => 90]);
        $this->assertSame(1, ServiceEntry::query()->where('customer_name', 'Service Customer')->firstOrFail()->items()->count());
    }

    public function test_service_entry_requires_at_least_one_service(): void
    {
        $response = $this->actingAs(User::factory()->create())->post(route('sales.service-entry.store'), [
            'service_type' => 'repair', 'customer_name' => 'Service Customer', 'service_date' => '2026-09-24', 'items' => [],
        ]);

        $response->assertSessionHasErrors('items');
    }

    public function test_authenticated_user_can_save_a_sales_return_and_adjust_invoice(): void
    {
        $user = User::factory()->create();
        $sale = Sale::create(['user_id' => $user->id, 'invoice_no' => '260100900', 'sale_type' => 'retail', 'customer_name' => 'Return Customer', 'sale_date' => '2026-09-24', 'subtotal' => 100, 'total' => 100, 'paid' => 50, 'due' => 50]);

        $response = $this->actingAs($user)->post(route('sales.return.store'), [
            'invoice_no' => $sale->invoice_no, 'customer_name' => 'Return Customer', 'return_date' => '2026-09-24',
            'reason' => 'Defective product', 'items' => [['product_name' => 'Test Laptop', 'product_code' => 'LT-01', 'quantity' => 1, 'rate' => 40, 'reason' => 'Defective']],
        ]);

        $response->assertRedirect(route('sales.return.create'));
        $this->assertDatabaseHas('sales_returns', ['invoice_no' => '260100900', 'customer_name' => 'Return Customer', 'total' => 40]);
        $this->assertDatabaseHas('sales_return_items', ['product_name' => 'Test Laptop', 'subtotal' => 40]);
        $this->assertDatabaseHas('sales', ['id' => $sale->id, 'total' => 60, 'due' => 10]);
    }

    public function test_sales_return_cannot_exceed_original_invoice_total(): void
    {
        $user = User::factory()->create();
        $sale = Sale::create(['user_id' => $user->id, 'invoice_no' => '260100901', 'sale_type' => 'retail', 'customer_name' => 'Return Customer', 'sale_date' => '2026-09-24', 'subtotal' => 100, 'total' => 100, 'paid' => 100, 'due' => 0]);

        $response = $this->actingAs($user)->post(route('sales.return.store'), [
            'invoice_no' => $sale->invoice_no, 'customer_name' => 'Return Customer', 'return_date' => '2026-09-24',
            'items' => [['product_name' => 'Test Laptop', 'quantity' => 2, 'rate' => 100]],
        ]);

        $response->assertSessionHasErrors('items');
        $this->assertDatabaseCount('sales_returns', 0);
    }

    public function test_authenticated_user_can_save_a_serial_sales_return(): void
    {
        $user = User::factory()->create();
        $sale = Sale::create(['user_id' => $user->id, 'invoice_no' => '260100902', 'sale_type' => 'retail', 'customer_name' => 'Serial Customer', 'sale_date' => '2026-09-24', 'subtotal' => 200, 'total' => 200, 'paid' => 200, 'due' => 0]);

        $response = $this->actingAs($user)->post(route('sales.serial-return.store'), [
            'serial_no' => 'SN-LAPTOP-01', 'invoice_no' => $sale->invoice_no, 'customer_name' => 'Serial Customer',
            'product_name' => 'Laptop', 'product_code' => 'LP-01', 'return_date' => '2026-09-24', 'quantity' => 1, 'rate' => 150, 'reason' => 'Warranty return',
        ]);

        $response->assertRedirect(route('sales.serial-return.create'));
        $this->assertDatabaseHas('serial_sales_returns', ['serial_no' => 'SN-LAPTOP-01', 'product_name' => 'Laptop', 'total' => 150]);
        $this->assertDatabaseHas('sales', ['id' => $sale->id, 'total' => 50]);
        $this->assertSame(1, SerialSalesReturn::query()->where('serial_no', 'SN-LAPTOP-01')->count());
    }

    public function test_serial_sales_return_requires_a_serial_number(): void
    {
        $response = $this->actingAs(User::factory()->create())->post(route('sales.serial-return.store'), [
            'customer_name' => 'Serial Customer', 'product_name' => 'Laptop', 'return_date' => '2026-09-24', 'quantity' => 1, 'rate' => 10,
        ]);

        $response->assertSessionHasErrors('serial_no');
    }

    public function test_authenticated_user_can_view_filtered_sales_record(): void
    {
        $user = User::factory()->create();
        Sale::create(['user_id' => $user->id, 'invoice_no' => '260100903', 'sale_type' => 'retail', 'customer_name' => 'Record Customer', 'sale_date' => '2026-09-24', 'subtotal' => 250, 'total' => 250, 'paid' => 200, 'due' => 50]);

        $response = $this->actingAs($user)->get(route('sales.record', ['search_type' => 'customer', 'customer' => 'Record Customer', 'from' => '2026-09-01', 'to' => '2026-09-30']));

        $response->assertOk();
        $response->assertSee('Sales Record');
        $response->assertSee('260100903');
        $response->assertSee('Record Customer');
        $response->assertSee('select');
    }

    public function test_sales_record_requires_a_valid_date_range(): void
    {
        $response = $this->actingAs(User::factory()->create())->get(route('sales.record', ['from' => '2026-09-30', 'to' => '2026-09-01']));

        $response->assertSessionHasErrors('to');
    }
}
