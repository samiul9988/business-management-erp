<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuotationController extends Controller
{
    public function create(): View
    {
        return view('quotations.create', ['nextQuotationNo' => $this->nextQuotationNumber()]);
    }

    public function store(Request $request, DatabaseManager $database): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_mobile' => ['nullable', 'string', 'max:30'],
            'customer_address' => ['nullable', 'string', 'max:1000'],
            'quotation_date' => ['required', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:quotation_date'],
            'vat' => ['nullable', 'numeric', 'min:0'], 'discount' => ['nullable', 'numeric', 'min:0'],
            'transport_cost' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'], 'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.product_code' => ['nullable', 'string', 'max:100'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'], 'items.*.rate' => ['required', 'numeric', 'min:0'],
            'items.*.discount_percent' => ['nullable', 'numeric', 'between:0,100'],
        ]);

        $quotation = $database->transaction(function () use ($validated, $request): Quotation {
            $items = collect($validated['items'])->map(function (array $item): array {
                $subtotal = round((float) $item['quantity'] * (float) $item['rate'], 2);
                $discountAmount = round($subtotal * ((float) ($item['discount_percent'] ?? 0) / 100), 2);

                return [...$item, 'subtotal' => $subtotal, 'discount_amount' => $discountAmount, 'total' => $subtotal - $discountAmount];
            });
            $subtotal = round($items->sum('subtotal'), 2);
            $discount = round((float) ($validated['discount'] ?? 0) + $items->sum('discount_amount'), 2);
            $total = round($subtotal + (float) ($validated['vat'] ?? 0) + (float) ($validated['transport_cost'] ?? 0) - $discount, 2);
            $quotation = Quotation::create([
                ...$validated, 'user_id' => $request->user()->id, 'quotation_no' => $this->nextQuotationNumber(),
                'subtotal' => $subtotal, 'discount' => $discount, 'total' => max($total, 0),
            ]);
            $quotation->items()->createMany($items->all());

            return $quotation;
        });

        return redirect()->route('quotations.invoice.show', $quotation)->with('success', "Quotation {$quotation->quotation_no} saved successfully.");
    }

    private function nextQuotationNumber(): string
    {
        $lastNumber = Quotation::query()->latest('id')->value('quotation_no');
        $number = $lastNumber ? ((int) preg_replace('/\D/', '', $lastNumber) + 1) : 1;

        return 'QT-'.str_pad((string) $number, 6, '0', STR_PAD_LEFT);
    }
}
