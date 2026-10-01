<?php

namespace App\Http\Controllers;

use App\Models\ServiceEntry;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceEntryController extends Controller
{
    public function create(): View
    {
        return view('sales.service-entry', ['nextInvoice' => $this->nextInvoiceNumber()]);
    }

    public function store(Request $request, DatabaseManager $database): RedirectResponse
    {
        $validated = $request->validate([
            'service_type' => ['required', 'in:repair,installation,maintenance'],
            'customer_name' => ['required', 'string', 'max:255'], 'customer_mobile' => ['nullable', 'string', 'max:30'],
            'customer_address' => ['nullable', 'string', 'max:1000'], 'technician' => ['nullable', 'string', 'max:255'],
            'service_date' => ['required', 'date'], 'vat' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'], 'transport_cost' => ['nullable', 'numeric', 'min:0'],
            'paid' => ['nullable', 'numeric', 'min:0'], 'items' => ['required', 'array', 'min:1'],
            'items.*.service_name' => ['required', 'string', 'max:255'], 'items.*.service_code' => ['nullable', 'string', 'max:100'],
            'items.*.device_serial' => ['nullable', 'string', 'max:150'], 'items.*.problem_description' => ['nullable', 'string', 'max:1000'],
            'items.*.warranty_days' => ['nullable', 'integer', 'min:0'], 'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.rate' => ['required', 'numeric', 'min:0'], 'items.*.discount_percent' => ['nullable', 'numeric', 'between:0,100'],
        ]);

        $service = $database->transaction(function () use ($validated, $request): ServiceEntry {
            $items = collect($validated['items'])->map(function (array $item): array {
                $subtotal = round((float) $item['quantity'] * (float) $item['rate'], 2);
                $discountAmount = round($subtotal * ((float) ($item['discount_percent'] ?? 0) / 100), 2);

                return [...$item, 'subtotal' => $subtotal, 'discount_amount' => $discountAmount, 'total' => $subtotal - $discountAmount];
            });
            $subtotal = round($items->sum('subtotal'), 2);
            $discount = round((float) ($validated['discount'] ?? 0) + $items->sum('discount_amount'), 2);
            $total = round($subtotal + (float) ($validated['vat'] ?? 0) + (float) ($validated['transport_cost'] ?? 0) - $discount, 2);
            $paid = round((float) ($validated['paid'] ?? 0), 2);
            $service = ServiceEntry::create([...$validated, 'user_id' => $request->user()->id, 'invoice_no' => $this->nextInvoiceNumber(), 'subtotal' => $subtotal, 'discount' => $discount, 'total' => max($total, 0), 'paid' => $paid, 'due' => max($total - $paid, 0)]);
            $service->items()->createMany($items->all());

            return $service;
        });

        return redirect()->route('sales.service-entry.create')->with('success', "Service {$service->invoice_no} saved successfully.");
    }

    private function nextInvoiceNumber(): string
    {
        $lastInvoice = ServiceEntry::query()->latest('id')->value('invoice_no');
        $number = $lastInvoice ? ((int) $lastInvoice + 1) : 260200001;

        return str_pad((string) $number, 9, '0', STR_PAD_LEFT);
    }
}
