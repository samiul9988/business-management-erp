<?php

namespace App\Http\Controllers;

use App\Models\CompanyProfile;
use App\Models\Customer;
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

    public function show(Sale $sale): View
    {
        return $this->renderInvoice($sale);
    }

    public function public(string $token): View
    {
        $sale = Sale::where('share_token', $token)->firstOrFail();

        return $this->renderInvoice($sale);
    }

    private function renderInvoice(Sale $sale): View
    {
        $sale->load(['items', 'user']);

        $customer = $sale->customer_mobile
            ? Customer::where('mobile', $sale->customer_mobile)->first()
            : null;

        return view('sales.invoice-print', [
            'sale' => $sale,
            'customer' => $customer,
            'company' => CompanyProfile::first(),
        ]);
    }
}
