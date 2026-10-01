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
            'salesData' => $sales->mapWithKeys(function (Sale $sale): array {
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
            })->all(),
            'nextReturn' => $this->nextReturnNumber(),
        ]);
    }

    public function store(Request $request, DatabaseManager $database): RedirectResponse
    {
        $validated = $request->validate([
            'invoice_no' => ['required', 'exists:sales,invoice_no'],
            'customer_name' => ['required', 'string', 'max:255'],
            'return_date' => ['required', 'date'], 'reason' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'], 'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.product_code' => ['nullable', 'string', 'max:100'], 'items.*.serial_no' => ['nullable', 'string', 'max:150'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'], 'items.*.rate' => ['required', 'numeric', 'min:0'],
            'items.*.reason' => ['nullable', 'string', 'max:500'],
        ]);

        $return = $database->transaction(function () use ($validated, $request): SalesReturn {
            $sale = Sale::query()->where('invoice_no', $validated['invoice_no'])->firstOrFail();
            $items = collect($validated['items'])->map(function (array $item): array {
                $subtotal = round((float) $item['quantity'] * (float) $item['rate'], 2);

                return [...$item, 'subtotal' => $subtotal];
            });
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

    private function nextReturnNumber(): string
    {
        $lastReturn = SalesReturn::query()->latest('id')->value('return_no');
        $number = $lastReturn ? ((int) $lastReturn + 1) : 260300001;

        return str_pad((string) $number, 9, '0', STR_PAD_LEFT);
    }
}
