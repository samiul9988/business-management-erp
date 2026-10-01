<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetController extends Controller
{
    public function create(): View
    {
        return view('purchase.assets', ['assets' => Asset::latest('id')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:purchase,sale'],
            'name' => ['required', 'string', 'max:255'],
            'party_name' => ['nullable', 'string', 'max:255'],
            'rate' => ['nullable', 'numeric', 'min:0'],
            'quantity' => ['nullable', 'numeric', 'gt:0'],
            'amount' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);
        $validated['user_id'] = $request->user()->id;
        Asset::create($validated);

        return redirect()->route('assets.create')->with('success', 'Assets entry saved successfully.');
    }
}
