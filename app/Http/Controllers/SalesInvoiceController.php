<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesInvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate(['invoice_no' => ['nullable', 'string', 'max:50']]);
        $sale = ! empty($validated['invoice_no'])
            ? Sale::with('items')->where('invoice_no', 'like', "%{$validated['invoice_no']}%")->latest('id')->first()
            : null;

        return view('sales.invoice', ['sale' => $sale, 'searched' => $validated['invoice_no'] ?? null]);
    }
}
