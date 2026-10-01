<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\LoanAccount;
use App\Models\LoanTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoanTransactionController extends Controller
{
    public function index(): View
    {
        return view('accounts.loan-transaction', [
            'transactions' => LoanTransaction::with('loanAccount', 'bankAccount')->latest('id')->get(),
            'loanAccounts' => LoanAccount::orderBy('account_name')->get(),
            'bankAccounts' => BankAccount::orderBy('account_name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'loan_account_id' => ['required', 'exists:loan_accounts,id'],
            'transaction_type' => ['required', 'in:receive,payment'],
            'payment_type' => ['required', 'in:cash,bank'],
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id'],
            'transaction_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);
        $validated['user_id'] = $request->user()->id;
        LoanTransaction::create($validated);

        return redirect()->route('loan-transaction.index')->with('success', 'Loan transaction saved successfully.');
    }
}
