<?php

namespace App\Http\Controllers;

use App\Models\RepairCompany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RepairCompanyController extends Controller
{
    public function index(): View
    {
        return view('repair.company', ['companies' => RepairCompany::latest('id')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:repair_companies,name'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);
        RepairCompany::create($validated);

        return redirect()->route('repair-company.index')->with('success', 'Repair company added successfully.');
    }

    public function destroy(RepairCompany $repairCompany): RedirectResponse
    {
        $repairCompany->delete();

        return redirect()->route('repair-company.index')->with('success', 'Repair company deleted successfully.');
    }
}
