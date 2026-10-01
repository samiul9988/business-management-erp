<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AreaController extends Controller
{
    public function index(): View
    {
        return view('admin.area', ['areas' => Area::latest('id')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(['name' => ['required', 'string', 'max:255', 'unique:areas,name']]);
        Area::create($validated);

        return redirect()->route('area.index')->with('success', 'Area added successfully.');
    }

    public function update(Request $request, Area $area): RedirectResponse
    {
        $validated = $request->validate(['name' => ['required', 'string', 'max:255', 'unique:areas,name,'.$area->id]]);
        $area->update($validated);

        return redirect()->route('area.index')->with('success', 'Area updated successfully.');
    }

    public function destroy(Area $area): RedirectResponse
    {
        $area->delete();

        return redirect()->route('area.index')->with('success', 'Area deleted successfully.');
    }
}
