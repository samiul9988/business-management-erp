<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PurchaseEntryController extends Controller
{
    public function create(): View
    {
        return view('purchase.entry', ['suppliers' => Supplier::orderBy('name')->get(), 'nextInvoice' => $this->nextInvoiceNumber(), 'purchase' => null]);
    }

    public function edit(Purchase $purchase): View
    {
        $purchase->load('items.serials');

        return view('purchase.entry', ['suppliers' => Supplier::orderBy('name')->get(), 'nextInvoice' => $purchase->invoice_no, 'purchase' => $purchase]);
    }

    public function store(Request $request, DatabaseManager $database): RedirectResponse
    {
        [$validated, $serialsByItem] = $this->validateAndParse($request);

        $purchase = $database->transaction(function () use ($validated, $request, $serialsByItem): Purchase {
            $items = $this->itemsWithTotals($validated['items']);
            $subtotal = round($items->sum('total'), 2);
            $discount = round((float) ($validated['discount'] ?? 0), 2);
            $total = round($subtotal + (float) ($validated['vat'] ?? 0) + (float) ($validated['transport_cost'] ?? 0) - $discount, 2);
            $paid = round((float) ($validated['paid'] ?? 0), 2);
            $purchase = Purchase::create([...$validated, 'user_id' => $request->user()->id, 'invoice_no' => $this->nextInvoiceNumber(), 'subtotal' => $subtotal, 'discount' => $discount, 'total' => max($total, 0), 'paid' => $paid, 'due' => max($total - $paid, 0)]);
            $this->saveItemsAndSerials($purchase, $items, $serialsByItem);

            return $purchase;
        });

        return redirect()->route('purchase.entry.create')->with('success', "Purchase {$purchase->invoice_no} saved successfully.");
    }

    public function update(Request $request, Purchase $purchase, DatabaseManager $database): RedirectResponse
    {
        [$validated, $serialsByItem] = $this->validateAndParse($request, $purchase);

        if (ProductSerial::where('purchase_id', $purchase->id)->where('status', '!=', 'in_stock')->exists()) {
            throw ValidationException::withMessages(['items' => 'This purchase has serials that are already sold, so its items can no longer be edited.']);
        }

        $database->transaction(function () use ($validated, $purchase, $serialsByItem): void {
            $items = $this->itemsWithTotals($validated['items']);
            $subtotal = round($items->sum('total'), 2);
            $discount = round((float) ($validated['discount'] ?? 0), 2);
            $total = round($subtotal + (float) ($validated['vat'] ?? 0) + (float) ($validated['transport_cost'] ?? 0) - $discount, 2);
            $paid = round((float) ($validated['paid'] ?? 0), 2);

            $purchase->update([...$validated, 'subtotal' => $subtotal, 'discount' => $discount, 'total' => max($total, 0), 'paid' => $paid, 'due' => max($total - $paid, 0)]);
            ProductSerial::where('purchase_id', $purchase->id)->delete();
            $purchase->items()->delete();
            $this->saveItemsAndSerials($purchase, $items, $serialsByItem);
        });

        return redirect()->route('purchase.record')->with('success', "Purchase {$purchase->invoice_no} updated successfully.");
    }

    private function validateAndParse(Request $request, ?Purchase $existing = null): array
    {
        $validated = $request->validate([
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'supplier_name' => ['required', 'string', 'max:255'],
            'supplier_mobile' => ['nullable', 'string', 'max:30'],
            'supplier_address' => ['nullable', 'string', 'max:1000'],
            'purchase_date' => ['required', 'date'],
            'vat' => ['nullable', 'numeric', 'min:0'], 'discount' => ['nullable', 'numeric', 'min:0'],
            'transport_cost' => ['nullable', 'numeric', 'min:0'], 'paid' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'], 'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.product_code' => ['nullable', 'string', 'max:100'], 'items.*.warranty_days' => ['nullable', 'integer', 'min:0'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'], 'items.*.rate' => ['required', 'numeric', 'min:0'],
            'items.*.serials' => ['nullable', 'string'],
        ]);

        $serialsByItem = collect($validated['items'])->map(fn (array $item) => $this->parseSerials($item['serials'] ?? ''));

        $duplicates = $serialsByItem->flatten()->duplicates();
        if ($duplicates->isNotEmpty()) {
            throw ValidationException::withMessages(['items' => 'Duplicate serial number: '.$duplicates->first()]);
        }

        $allSerials = $serialsByItem->flatten();
        if ($allSerials->isNotEmpty()) {
            $query = ProductSerial::whereIn('serial_no', $allSerials);
            if ($existing) {
                $query->where('purchase_id', '!=', $existing->id);
            }
            $existingSerial = $query->value('serial_no');
            if ($existingSerial) {
                throw ValidationException::withMessages(['items' => "Serial number already recorded: {$existingSerial}"]);
            }
        }

        foreach ($validated['items'] as $index => $item) {
            $serials = $serialsByItem[$index];
            if ($serials->isNotEmpty() && $serials->count() !== (int) round((float) $item['quantity'])) {
                throw ValidationException::withMessages(['items' => "Serial count for \"{$item['product_name']}\" does not match quantity."]);
            }
        }

        return [$validated, $serialsByItem];
    }

    private function itemsWithTotals(array $items)
    {
        return collect($items)->map(function (array $item): array {
            return [...$item, 'total' => round((float) $item['quantity'] * (float) $item['rate'], 2)];
        })->map(fn (array $item) => collect($item)->except('serials')->all());
    }

    private function saveItemsAndSerials(Purchase $purchase, $items, $serialsByItem): void
    {
        $createdItems = $purchase->items()->createMany($items->all());
        $createdItems->each(fn ($purchaseItem) => $this->ensureProductExists($purchaseItem));

        $createdItems->each(function ($purchaseItem, $index) use ($purchase, $serialsByItem): void {
            foreach ($serialsByItem[$index] as $serialNo) {
                ProductSerial::create([
                    'product_code' => $purchaseItem->product_code,
                    'serial_no' => $serialNo,
                    'status' => 'in_stock',
                    'purchase_id' => $purchase->id,
                    'purchase_item_id' => $purchaseItem->id,
                ]);
            }
        });
    }

    private function ensureProductExists($purchaseItem): void
    {
        if (empty($purchaseItem->product_code) || Product::where('product_code', $purchaseItem->product_code)->exists()) {
            return;
        }

        Product::create([
            'product_code' => $purchaseItem->product_code,
            'name' => $purchaseItem->product_name,
            'warranty_days' => $purchaseItem->warranty_days ?? 0,
            'purchase_rate' => $purchaseItem->rate,
            'sale_rate' => $purchaseItem->rate,
            'wholesale_rate' => $purchaseItem->rate,
            'status' => 'active',
        ]);
    }

    private function parseSerials(string $raw)
    {
        return collect(preg_split('/[\r\n,]+/', $raw))->map(fn ($s) => trim($s))->filter()->values();
    }

    private function nextInvoiceNumber(): string
    {
        $lastInvoice = Purchase::query()->latest('id')->value('invoice_no');
        $number = $lastInvoice ? ((int) $lastInvoice + 1) : 360100001;

        return str_pad((string) $number, 9, '0', STR_PAD_LEFT);
    }
}
