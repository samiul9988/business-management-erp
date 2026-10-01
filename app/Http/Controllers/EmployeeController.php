<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(): View
    {
        return view('hr.employee', [
            'employees' => Employee::with('designation', 'department')->latest('id')->get(),
            'designations' => Designation::orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
            'nextEmployeeCode' => $this->nextEmployeeCode(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'designation_id' => ['nullable', 'exists:designations,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'join_date' => ['nullable', 'date'],
            'salary_range' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,deactive'],
            'present_address' => ['nullable', 'string', 'max:255'],
            'permanent_address' => ['nullable', 'string', 'max:255'],
            'contact_no' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'image' => ['nullable', 'image', 'max:2048'],
            'reference' => ['nullable', 'string', 'max:1000'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'in:male,female'],
            'date_of_birth' => ['nullable', 'date'],
            'marital_status' => ['nullable', 'in:married,unmarried'],
        ]);
        $validated['employee_code'] = $this->nextEmployeeCode();
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('employees', 'public');
        }
        Employee::create($validated);

        return redirect()->route('employee.index')->with('success', 'Employee added successfully.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $employee->delete();

        return redirect()->route('employee.index')->with('success', 'Employee deleted successfully.');
    }

    private function nextEmployeeCode(): string
    {
        $lastCode = Employee::query()->latest('id')->value('employee_code');
        $number = $lastCode ? ((int) $lastCode + 1) : 5001;

        return (string) $number;
    }
}
