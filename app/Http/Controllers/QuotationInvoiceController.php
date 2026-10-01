<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuotationInvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate(['quotation_no' => ['nullable', 'string', 'max:50']]);
        $quotation = ! empty($validated['quotation_no'])
            ? Quotation::with('items')->where('quotation_no', 'like', "%{$validated['quotation_no']}%")->latest('id')->first()
            : null;

        return view('quotations.invoice', ['quotation' => $quotation, 'searched' => $validated['quotation_no'] ?? null]);
    }

    public function show(Quotation $quotation): View
    {
        $quotation->load('items');

        return view('quotations.invoice', ['quotation' => $quotation, 'searched' => $quotation->quotation_no]);
    }
}
