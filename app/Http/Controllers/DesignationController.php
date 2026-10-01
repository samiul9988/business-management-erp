<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DesignationController extends Controller
{
    public function index(): View
    {
        return view('hr.designation', ['designations' => Designation::latest('id')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(['name' => ['required', 'string', 'max:255', 'unique:designations,name']]);
        Designation::create($validated);

        return redirect()->route('designation.index')->with('success', 'Designation added successfully.');
    }

    public function destroy(Designation $designation): RedirectResponse
    {
        $designation->delete();

        return redirect()->route('designation.index')->with('success', 'Designation deleted successfully.');
    }
}
