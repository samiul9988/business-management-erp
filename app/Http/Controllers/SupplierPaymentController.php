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
            $this->applyAdjustment($supplier, $validated['transaction_type'], (float) $validated['amount'] + (float) ($validated['discount'] ?? 0));
        });

        return redirect()->route('supplier-payment.index')->with('success', 'Supplier payment saved successfully.');
    }

    public function update(Request $request, SupplierPayment $supplierPayment, DatabaseManager $database): RedirectResponse
    {
        $request->merge(['supplier_id' => $supplierPayment->supplier_id]);
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

        $database->transaction(function () use ($validated, $supplierPayment): void {
            $supplier = $supplierPayment->supplier;
            $oldAdjustment = (float) $supplierPayment->amount + (float) $supplierPayment->discount;
            $this->applyAdjustment($supplier, $supplierPayment->transaction_type, $oldAdjustment, reverse: true);

            $supplierPayment->update($validated);

            $supplier->refresh();
            $this->applyAdjustment($supplier, $validated['transaction_type'], (float) $validated['amount'] + (float) ($validated['discount'] ?? 0));
        });

        return redirect()->route('supplier-payment.index')->with('success', 'Supplier payment updated successfully.');
    }

    private function applyAdjustment(Supplier $supplier, string $type, float $adjustment, bool $reverse = false): void
    {
        $effectiveType = $reverse ? ($type === 'payment' ? 'receive' : 'payment') : $type;
        $due = $effectiveType === 'payment'
            ? max((float) $supplier->previous_due - $adjustment, 0)
            : (float) $supplier->previous_due + $adjustment;
        $supplier->update(['previous_due' => $due]);
    }
}
