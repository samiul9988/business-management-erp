<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\InvestmentAccount;
use App\Models\InvestmentTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvestmentTransactionController extends Controller
{
    public function index(): View
    {
        return view('accounts.investment-transaction', [
            'transactions' => InvestmentTransaction::with('investmentAccount', 'bankAccount')->latest('id')->get(),
            'investmentAccounts' => InvestmentAccount::orderBy('account_name')->get(),
            'bankAccounts' => BankAccount::orderBy('account_name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'investment_account_id' => ['required', 'exists:investment_accounts,id'],
            'transaction_type' => ['required', 'in:receive,payment'],
            'payment_type' => ['required', 'in:cash,bank'],
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id'],
            'transaction_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);
        $validated['user_id'] = $request->user()->id;
        InvestmentTransaction::create($validated);

        return redirect()->route('investment-transaction.index')->with('success', 'Investment transaction saved successfully.');
    }
}
