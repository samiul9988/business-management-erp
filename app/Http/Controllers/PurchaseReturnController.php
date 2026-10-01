<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchaseReturn;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PurchaseReturnController extends Controller
{
    public function create(): View
    {
        $purchases = Purchase::query()->with('items')->latest('id')->get();

        return view('purchase.return', [
            'purchases' => $purchases,
            'purchasesData' => $purchases->mapWithKeys(function (Purchase $purchase): array {
                return [$purchase->invoice_no => [
                    'supplier' => $purchase->supplier_name,
                    'total' => (float) $purchase->total,
                    'items' => $purchase->items->map(function (PurchaseItem $item): array {
                        return ['code' => $item->product_code, 'name' => $item->product_name, 'rate' => (float) $item->rate];
                    })->values()->all(),
                ]];
            })->all(),
            'nextReturn' => $this->nextReturnNumber(),
        ]);
    }

    public function store(Request $request, DatabaseManager $database): RedirectResponse
    {
        $validated = $request->validate([
            'invoice_no' => ['required', 'exists:purchases,invoice_no'],
            'supplier_name' => ['required', 'string', 'max:255'],
            'return_date' => ['required', 'date'], 'reason' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'], 'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.product_code' => ['nullable', 'string', 'max:100'], 'items.*.serial_no' => ['nullable', 'string', 'max:150'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'], 'items.*.rate' => ['required', 'numeric', 'min:0'],
            'items.*.reason' => ['nullable', 'string', 'max:500'],
        ]);

        $return = $database->transaction(function () use ($validated, $request): PurchaseReturn {
            $purchase = Purchase::query()->where('invoice_no', $validated['invoice_no'])->firstOrFail();
            $items = collect($validated['items'])->map(function (array $item): array {
                return [...$item, 'subtotal' => round((float) $item['quantity'] * (float) $item['rate'], 2)];
            });
            $total = round($items->sum('subtotal'), 2);
            if ($total > (float) $purchase->total) {
                throw ValidationException::withMessages(['items' => 'The return amount cannot exceed the original invoice total.']);
            }
            $return = PurchaseReturn::create([...$validated, 'user_id' => $request->user()->id, 'purchase_id' => $purchase->id, 'return_no' => $this->nextReturnNumber(), 'subtotal' => $total, 'total' => $total]);
            $return->items()->createMany($items->all());
            $purchase->update(['total' => max((float) $purchase->total - $total, 0), 'due' => max((float) $purchase->due - $total, 0)]);

            return $return;
        });

        return redirect()->route('purchase.return.create')->with('success', "Return {$return->return_no} saved successfully.");
    }

    private function nextReturnNumber(): string
    {
        $lastReturn = PurchaseReturn::query()->latest('id')->value('return_no');
        $number = $lastReturn ? ((int) $lastReturn + 1) : 360300001;

        return str_pad((string) $number, 9, '0', STR_PAD_LEFT);
    }
}
