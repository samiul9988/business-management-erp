<?php

namespace App\Http\Controllers;

use App\Models\Month;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonthController extends Controller
{
    public function index(): View
    {
        return view('hr.month', ['months' => Month::latest('id')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(['name' => ['required', 'string', 'max:7', 'unique:months,name']]);
        Month::create($validated);

        return redirect()->route('month.index')->with('success', 'Month added successfully.');
    }

    public function destroy(Month $month): RedirectResponse
    {
        $month->delete();

        return redirect()->route('month.index')->with('success', 'Month deleted successfully.');
    }
}
