<?php

namespace App\Http\Controllers;

use App\Models\Damage;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DamageEntryController extends Controller
{
    public function create(): View
    {
        return view('pending.damage-entry', [
            'products' => Product::orderBy('name')->get(),
            'nextInvoice' => $this->nextInvoiceNumber(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'damage_date' => ['required', 'date'],
            'product_id' => ['required', 'exists:products,id'],
            'product_name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'rate' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);
        $validated['user_id'] = $request->user()->id;
        $validated['invoice_no'] = $this->nextInvoiceNumber();
        $validated['amount'] = round((float) $validated['quantity'] * (float) $validated['rate'], 2);
        Damage::create($validated);

        return redirect()->route('damage.entry.create')->with('success', 'Damage entry saved successfully.');
    }

    private function nextInvoiceNumber(): string
    {
        $lastInvoice = Damage::query()->latest('id')->value('invoice_no');
        $number = $lastInvoice ? ((int) $lastInvoice + 1) : 660100001;

        return str_pad((string) $number, 9, '0', STR_PAD_LEFT);
    }
}
