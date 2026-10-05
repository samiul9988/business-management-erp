<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesEntryController extends Controller
{
    public function create(Request $request): View
    {
        return view('sales.entry', [
            'nextInvoice' => $this->nextInvoiceNumber(),
            'searchedBarcode' => trim((string) $request->query('barcode', '')),
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
        ]);

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
            $sale->items()->createMany($items->all());

            return $sale;
        });

        return redirect()->route('sales.entry.create')->with('success', "Sale {$sale->invoice_no} saved successfully.");
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
