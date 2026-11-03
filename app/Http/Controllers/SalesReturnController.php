<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalesReturn;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SalesReturnController extends Controller
{
    public function create(): View
    {
        $sales = Sale::query()->with('items')->latest('id')->get();

        return view('sales.return', [
            'sales' => $sales,
            'salesData' => $this->salesData($sales),
            'nextReturn' => $this->nextReturnNumber(),
            'salesReturn' => null,
        ]);
    }

    public function edit(SalesReturn $salesReturn): View
    {
        $salesReturn->load('items');
        $sales = Sale::query()->with('items')->latest('id')->get();

        return view('sales.return', [
            'sales' => $sales,
            'salesData' => $this->salesData($sales),
            'nextReturn' => $salesReturn->return_no,
            'salesReturn' => $salesReturn,
        ]);
    }

    private function salesData($sales): array
    {
        return $sales->mapWithKeys(function (Sale $sale): array {
            return [$sale->invoice_no => [
                'customer' => $sale->customer_name,
                'total' => (float) $sale->total,
                'items' => $sale->items->map(function (SaleItem $item): array {
                    return [
                        'code' => $item->product_code,
                        'name' => $item->product_name,
                        'rate' => (float) $item->rate,
                    ];
                })->values()->all(),
            ]];
        })->all();
    }

    public function store(Request $request, DatabaseManager $database): RedirectResponse
    {
        $validated = $this->validated($request);

        $return = $database->transaction(function () use ($validated, $request): SalesReturn {
            $sale = Sale::query()->where('invoice_no', $validated['invoice_no'])->firstOrFail();
            $items = $this->itemsWithTotals($validated['items']);
            $total = round($items->sum('subtotal'), 2);
            if ($total > (float) $sale->total) {
                throw ValidationException::withMessages(['items' => 'The return amount cannot exceed the original invoice total.']);
            }
            $return = SalesReturn::create([...$validated, 'user_id' => $request->user()->id, 'sale_id' => $sale->id, 'return_no' => $this->nextReturnNumber(), 'customer_name' => $validated['customer_name'], 'subtotal' => $total, 'total' => $total]);
            $return->items()->createMany($items->all());
            $sale->update(['total' => max((float) $sale->total - $total, 0), 'due' => max((float) $sale->due - $total, 0)]);

            return $return;
        });

        return redirect()->route('sales.return.create')->with('success', "Return {$return->return_no} saved successfully.");
    }

    public function update(Request $request, SalesReturn $salesReturn, DatabaseManager $database): RedirectResponse
    {
        $request->merge(['invoice_no' => $salesReturn->invoice_no]);
        $validated = $this->validated($request);

        $database->transaction(function () use ($validated, $salesReturn): void {
            $sale = $salesReturn->sale;
            $sale->update(['total' => (float) $sale->total + (float) $salesReturn->total, 'due' => (float) $sale->due + (float) $salesReturn->total]);

            $items = $this->itemsWithTotals($validated['items']);
            $total = round($items->sum('subtotal'), 2);
            if ($total > (float) $sale->total) {
                throw ValidationException::withMessages(['items' => 'The return amount cannot exceed the original invoice total.']);
            }

            $salesReturn->update(['customer_name' => $validated['customer_name'], 'return_date' => $validated['return_date'], 'reason' => $validated['reason'] ?? null, 'subtotal' => $total, 'total' => $total]);
            $salesReturn->items()->delete();
            $salesReturn->items()->createMany($items->all());
            $sale->update(['total' => max((float) $sale->total - $total, 0), 'due' => max((float) $sale->due - $total, 0)]);
        });

        return redirect()->route('sales.return-record')->with('success', "Return {$salesReturn->return_no} updated successfully.");
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'invoice_no' => ['required', 'exists:sales,invoice_no'],
            'customer_name' => ['required', 'string', 'max:255'],
            'return_date' => ['required', 'date'], 'reason' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'], 'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.product_code' => ['nullable', 'string', 'max:100'], 'items.*.serial_no' => ['nullable', 'string', 'max:150'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'], 'items.*.rate' => ['required', 'numeric', 'min:0'],
            'items.*.reason' => ['nullable', 'string', 'max:500'],
        ]);
    }

    private function itemsWithTotals(array $items)
    {
        return collect($items)->map(function (array $item): array {
            $subtotal = round((float) $item['quantity'] * (float) $item['rate'], 2);

            return [...$item, 'subtotal' => $subtotal];
        });
    }

    private function nextReturnNumber(): string
    {
        $lastReturn = SalesReturn::query()->latest('id')->value('return_no');
        $number = $lastReturn ? ((int) $lastReturn + 1) : 260300001;

        return str_pad((string) $number, 9, '0', STR_PAD_LEFT);
    }
}
