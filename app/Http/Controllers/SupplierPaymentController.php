<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierPaymentController extends Controller
{
    public function index(): View
    {
        return view('accounts.supplier-payment', [
            'payments' => SupplierPayment::with('supplier', 'bankAccount')->latest('id')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
            'bankAccounts' => BankAccount::orderBy('account_name')->get(),
        ]);
    }

    public function store(Request $request, DatabaseManager $database): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'transaction_type' => ['required', 'in:receive,payment'],
            'payment_type' => ['required', 'in:cash,bank'],
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id'],
            'payment_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'discount_percent' => ['nullable', 'numeric', 'between:0,100'],
        ]);
        $validated['user_id'] = $request->user()->id;

        $database->transaction(function () use ($validated): void {
            $supplier = Supplier::query()->findOrFail($validated['supplier_id']);
            SupplierPayment::create($validated);
            $adjustment = (float) $validated['amount'] + (float) ($validated['discount'] ?? 0);
            $due = $validated['transaction_type'] === 'payment'
                ? max((float) $supplier->previous_due - $adjustment, 0)
                : (float) $supplier->previous_due + $adjustment;
            $supplier->update(['previous_due' => $due]);
        });

        return redirect()->route('supplier-payment.index')->with('success', 'Supplier payment saved successfully.');
    }
}
