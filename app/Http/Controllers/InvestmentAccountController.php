<?php

namespace App\Http\Controllers;

use App\Models\InvestmentAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvestmentAccountController extends Controller
{
    public function index(): View
    {
        return view('accounts.investment-account', ['investmentAccounts' => InvestmentAccount::latest('id')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        InvestmentAccount::create($this->validated($request));

        return redirect()->route('investment-account.index')->with('success', 'Investment account added successfully.');
    }

    public function destroy(InvestmentAccount $investmentAccount): RedirectResponse
    {
        $investmentAccount->delete();

        return redirect()->route('investment-account.index')->with('success', 'Investment account deleted successfully.');
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
