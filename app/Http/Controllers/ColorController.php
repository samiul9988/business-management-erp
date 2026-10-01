<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ColorController extends Controller
{
    public function index(): View
    {
        return view('admin.color', ['colors' => Color::latest('id')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(['name' => ['required', 'string', 'max:255', 'unique:colors,name']]);
        Color::create($validated);

        return redirect()->route('color.index')->with('success', 'Color added successfully.');
    }

    public function update(Request $request, Color $color): RedirectResponse
    {
        $validated = $request->validate(['name' => ['required', 'string', 'max:255', 'unique:colors,name,'.$color->id]]);
        $color->update($validated);

        return redirect()->route('color.index')->with('success', 'Color updated successfully.');
    }

    public function destroy(Color $color): RedirectResponse
    {
        $color->delete();

        return redirect()->route('color.index')->with('success', 'Color deleted successfully.');
    }
}
