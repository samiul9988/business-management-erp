<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Month;
use App\Models\Salary;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalaryGenerateController extends Controller
{
    public function index(): View
    {
        return view('hr.salary-generate', [
            'months' => Month::orderBy('name')->get(),
            'generated' => Salary::with('employee', 'month')->latest('id')->get(),
        ]);
    }

    public function store(Request $request, DatabaseManager $database): RedirectResponse
    {
        $validated = $request->validate(['month_id' => ['required', 'exists:months,id']]);

        $count = $database->transaction(function () use ($validated): int {
            $employees = Employee::query()->where('status', 'active')->get();
            $created = 0;
            foreach ($employees as $employee) {
                $exists = Salary::query()->where('employee_id', $employee->id)->where('month_id', $validated['month_id'])->exists();
                if ($exists) {
                    continue;
                }
                Salary::create([
                    'employee_id' => $employee->id,
                    'month_id' => $validated['month_id'],
                    'amount' => $employee->salary_range,
                    'paid' => 0,
                    'due' => $employee->salary_range,
                    'generated_date' => now()->toDateString(),
                ]);
                $created++;
            }

            return $created;
        });

        return redirect()->route('salary-generate.index')->with('success', "Salary generated for {$count} employee(s).");
    }
}
