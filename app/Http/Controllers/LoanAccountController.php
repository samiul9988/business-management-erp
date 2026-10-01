<?php

namespace App\Http\Controllers;

use App\Models\LoanAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoanAccountController extends Controller
{
    public function index(): View
    {
        return view('accounts.loan-account', ['loanAccounts' => LoanAccount::latest('id')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        LoanAccount::create($this->validated($request));

        return redirect()->route('loan-account.index')->with('success', 'Loan account added successfully.');
    }

    public function destroy(LoanAccount $loanAccount): RedirectResponse
    {
        $loanAccount->delete();

        return redirect()->route('loan-account.index')->with('success', 'Loan account deleted successfully.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'account_name' => ['required', 'string', 'max:255'],
            'account_no' => ['nullable', 'string', 'max:100'],
            'account_type' => ['nullable', 'string', 'max:100'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'branch_name' => ['nullable', 'string', 'max:255'],
            'initial_balance' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
