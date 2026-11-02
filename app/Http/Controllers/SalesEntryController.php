<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\Sale;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SalesEntryController extends Controller
{
    public function create(Request $request): View
    {
        return view('sales.entry', [
            'nextInvoice' => $this->nextInvoiceNumber(),
            'searchedBarcode' => trim((string) $request->query('barcode', '')),
            'sale' => null,
            'customers' => $this->customerOptions(),
            'products' => $this->productOptions(),
            'serialCatalog' => $this->serialCatalog(),
        ]);
    }

    public function edit(Sale $sale): View
    {
        $sale->load('items');

        return view('sales.entry', [
            'nextInvoice' => $sale->invoice_no,
            'searchedBarcode' => '',
            'sale' => $sale,
            'customers' => $this->customerOptions(),
            'products' => $this->productOptions(),
            'serialCatalog' => $this->serialCatalog($sale),
        ]);
    }

    private function serialCatalog(?Sale $sale = null): array
    {
        $query = ProductSerial::where('status', 'in_stock');

        if ($sale) {
            $query->orWhere('sale_id', $sale->id);
        }

        $serials = $query->get(['product_code', 'serial_no']);
        $productsByCode = Product::whereIn('product_code', $serials->pluck('product_code')->unique())->get()->keyBy('product_code');

        return $serials->map(function ($s) use ($productsByCode) {
            $p = $productsByCode->get($s->product_code);

            return [
                'serial' => $s->serial_no, 'code' => $s->product_code, 'name' => $p?->name ?? $s->product_code,
                'rate' => (float) ($p?->sale_rate ?? 0), 'wholesale_rate' => (float) ($p?->wholesale_rate ?? 0),
                'warranty_days' => $p?->warranty_days ?? 0,
            ];
        })->values()->all();
    }

    private function customerOptions()
    {
        return Customer::query()->orderBy('name')->get(['id', 'customer_code', 'name', 'mobile', 'address', 'previous_due'])
            ->map(fn (Customer $c) => [
                'id' => $c->id, 'code' => $c->customer_code, 'name' => $c->name,
                'mobile' => $c->mobile, 'address' => $c->address, 'due' => (float) $c->previous_due,
            ]);
    }

    private function productOptions()
    {
        return Product::query()->with('color')->where('status', 'active')->orderBy('name')->get()
            ->map(fn (Product $p) => [
                'code' => $p->product_code, 'name' => $p->name, 'color' => $p->color?->name ?? 'N/A',
                'rate' => (float) $p->sale_rate, 'wholesale_rate' => (float) $p->wholesale_rate,
                'warranty_days' => $p->warranty_days,
            ]);
    }

    public function store(Request $request, DatabaseManager $database): RedirectResponse
    {
        $validated = $request->validate([
            'sale_type' => ['required', 'in:retail,wholesale'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_mobile' => ['nullable', 'string', 'max:30'],
            'customer_address' => ['nullable', 'string', 'max:1000'],
            'sale_date' => ['required', 'date'],
            'vat' => ['nullable', 'numeric', 'min:0'], 'discount' => ['nullable', 'numeric', 'min:0'],
            'transport_cost' => ['nullable', 'numeric', 'min:0'], 'paid' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'], 'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.product_code' => ['nullable', 'string', 'max:100'], 'items.*.warranty_days' => ['nullable', 'integer', 'min:0'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'], 'items.*.rate' => ['required', 'numeric', 'min:0'],
            'items.*.discount_percent' => ['nullable', 'numeric', 'between:0,100'],
            'items.*.serial_no' => ['nullable', 'string', 'max:100'],
        ]);

        $this->assertSerialQuantities($validated['items']);

        $sale = $database->transaction(function () use ($validated, $request): Sale {
            $items = collect($validated['items'])->map(function (array $item): array {
                $subtotal = round((float) $item['quantity'] * (float) $item['rate'], 2);
                $discountAmount = round($subtotal * ((float) ($item['discount_percent'] ?? 0) / 100), 2);

                return [...$item, 'subtotal' => $subtotal, 'discount_amount' => $discountAmount, 'total' => $subtotal - $discountAmount];
            });
            $subtotal = round($items->sum('subtotal'), 2);
            $discount = round((float) ($validated['discount'] ?? 0) + $items->sum('discount_amount'), 2);
            $total = round($subtotal + (float) ($validated['vat'] ?? 0) + (float) ($validated['transport_cost'] ?? 0) - $discount, 2);
            $paid = round((float) ($validated['paid'] ?? 0), 2);
            $customerId = $this->findOrCreateCustomer($validated)?->id;
            $sale = Sale::create([...$validated, 'user_id' => $request->user()->id, 'customer_id' => $customerId, 'invoice_no' => $this->nextInvoiceNumber(), 'subtotal' => $subtotal, 'discount' => $discount, 'total' => max($total, 0), 'paid' => $paid, 'due' => max($total - $paid, 0)]);
            $createdItems = $sale->items()->createMany($items->all());
            $this->reserveSerials($createdItems, $sale);

            return $sale;
        });

        return redirect()->route('sales.entry.create')->with('success', "Sale {$sale->invoice_no} saved successfully.");
    }

    private function assertSerialQuantities(array $items): void
    {
        foreach ($items as $item) {
            if (! empty($item['serial_no']) && (float) $item['quantity'] !== 1.0) {
                throw ValidationException::withMessages(['items' => "Quantity must be 1 for a serial-tracked item: {$item['product_name']}"]);
            }
        }
    }

    private function reserveSerials($createdItems, Sale $sale): void
    {
        $createdItems->each(function ($saleItem) use ($sale): void {
            if (! $saleItem->serial_no) {
                return;
            }

            $serial = ProductSerial::where('serial_no', $saleItem->serial_no)->where('status', 'in_stock')->first();

            if (! $serial) {
                throw ValidationException::withMessages(['items' => "Serial {$saleItem->serial_no} is no longer available."]);
            }

            $serial->update(['status' => 'sold', 'sale_id' => $sale->id, 'sale_item_id' => $saleItem->id]);
        });
    }

    public function update(Request $request, Sale $sale, DatabaseManager $database): RedirectResponse
    {
        $validated = $request->validate([
            'sale_type' => ['required', 'in:retail,wholesale'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_mobile' => ['nullable', 'string', 'max:30'],
            'customer_address' => ['nullable', 'string', 'max:1000'],
            'sale_date' => ['required', 'date'],
            'vat' => ['nullable', 'numeric', 'min:0'], 'discount' => ['nullable', 'numeric', 'min:0'],
            'transport_cost' => ['nullable', 'numeric', 'min:0'], 'paid' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'], 'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.product_code' => ['nullable', 'string', 'max:100'], 'items.*.warranty_days' => ['nullable', 'integer', 'min:0'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'], 'items.*.rate' => ['required', 'numeric', 'min:0'],
            'items.*.discount_percent' => ['nullable', 'numeric', 'between:0,100'],
            'items.*.serial_no' => ['nullable', 'string', 'max:100'],
        ]);

        $this->assertSerialQuantities($validated['items']);

        $database->transaction(function () use ($validated, $sale): void {
            $items = collect($validated['items'])->map(function (array $item): array {
                $subtotal = round((float) $item['quantity'] * (float) $item['rate'], 2);
                $discountAmount = round($subtotal * ((float) ($item['discount_percent'] ?? 0) / 100), 2);

                return [...$item, 'subtotal' => $subtotal, 'discount_amount' => $discountAmount, 'total' => $subtotal - $discountAmount];
            });
            $subtotal = round($items->sum('subtotal'), 2);
            $discount = round((float) ($validated['discount'] ?? 0) + $items->sum('discount_amount'), 2);
            $total = round($subtotal + (float) ($validated['vat'] ?? 0) + (float) ($validated['transport_cost'] ?? 0) - $discount, 2);
            $paid = round((float) ($validated['paid'] ?? 0), 2);
            $customerId = $this->findOrCreateCustomer($validated)?->id;

            $sale->update([...$validated, 'customer_id' => $customerId, 'subtotal' => $subtotal, 'discount' => $discount, 'total' => max($total, 0), 'paid' => $paid, 'due' => max($total - $paid, 0)]);
            ProductSerial::where('sale_id', $sale->id)->update(['status' => 'in_stock', 'sale_id' => null, 'sale_item_id' => null]);
            $sale->items()->delete();
            $createdItems = $sale->items()->createMany($items->all());
            $this->reserveSerials($createdItems, $sale);
        });

        return redirect()->route('sales.record')->with('success', "Sale {$sale->invoice_no} updated successfully.");
    }

    private function findOrCreateCustomer(array $validated): ?Customer
    {
        if (! empty($validated['customer_mobile'])) {
            $customer = Customer::where('mobile', $validated['customer_mobile'])->first();

            if ($customer) {
                return $customer;
            }
        }

        if (empty($validated['customer_name']) || $validated['customer_name'] === 'Cash Customer') {
            return null;
        }

        $lastCode = Customer::query()->latest('id')->value('customer_code');
        $nextCode = (string) ($lastCode ? ((int) $lastCode + 1) : 1001);

        return Customer::create([
            'customer_code' => $nextCode,
            'mobile' => $validated['customer_mobile'] ?? null,
            'name' => $validated['customer_name'],
            'address' => $validated['customer_address'] ?? null,
            'previous_due' => 0,
            'credit_limit' => 0,
            'customer_type' => $validated['sale_type'] === 'wholesale' ? 'wholesale' : 'regular',
        ]);
    }

    private function nextInvoiceNumber(): string
    {
        $lastInvoice = Sale::query()->latest('id')->value('invoice_no');
        $number = $lastInvoice ? ((int) $lastInvoice + 1) : 260100001;

        return str_pad((string) $number, 9, '0', STR_PAD_LEFT);
    }
}
