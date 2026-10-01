<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Employee;
use App\Models\Month;
use App\Models\Salary;
use App\Models\SalaryPayment;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalaryPaymentController extends Controller
{
    public function index(): View
    {
        $salaries = Salary::with('employee', 'month')->where('due', '>', 0)->latest('id')->get();

        return view('hr.salary-payment', [
            'employees' => Employee::orderBy('name')->get(),
            'months' => Month::orderBy('name')->get(),
            'bankAccounts' => BankAccount::orderBy('account_name')->get(),
            'salaries' => $salaries,
            'salariesData' => $salaries->mapWithKeys(fn (Salary $salary): array => ["{$salary->employee_id}-{$salary->month_id}" => ['salary_id' => $salary->id, 'amount' => (float) $salary->amount, 'due' => (float) $salary->due]])->all(),
            'payments' => SalaryPayment::with('salary.employee', 'salary.month')->latest('id')->get(),
        ]);
    }

    public function store(Request $request, DatabaseManager $database): RedirectResponse
    {
        $validated = $request->validate([
            'salary_id' => ['required', 'exists:salaries,id'],
            'payment_method' => ['required', 'in:cash,bank'],
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id'],
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);
        $validated['user_id'] = $request->user()->id;

        $database->transaction(function () use ($validated): void {
            $salary = Salary::query()->findOrFail($validated['salary_id']);
            SalaryPayment::create($validated);
            $salary->update([
                'paid' => (float) $salary->paid + (float) $validated['amount'],
                'due' => max((float) $salary->due - (float) $validated['amount'], 0),
            ]);
        });

        return redirect()->route('salary-payment.index')->with('success', 'Salary payment saved successfully.');
    }
}
