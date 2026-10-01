<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(): View
    {
        return view('admin.supplier', [
            'suppliers' => Supplier::latest('id')->get(),
            'nextSupplierCode' => $this->nextSupplierCode(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);
        $validated['supplier_code'] = $this->nextSupplierCode();
        Supplier::create($validated);

        return redirect()->route('supplier.index')->with('success', 'Supplier added successfully.');
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($this->validated($request));

        return redirect()->route('supplier.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $supplier->delete();

        return redirect()->route('supplier.index')->with('success', 'Supplier deleted successfully.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'mobile' => ['nullable', 'string', 'max:30'],
            'name' => ['required', 'string', 'max:255'],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'mode' => ['required', 'in:cash,credit'],
            'address' => ['nullable', 'string', 'max:500'],
            'email' => ['nullable', 'string', 'max:255'],
            'previous_due' => ['nullable', 'numeric', 'min:0'],
        ]);
    }

    private function nextSupplierCode(): string
    {
        $lastCode = Supplier::query()->latest('id')->value('supplier_code');
        $number = $lastCode ? ((int) $lastCode + 1) : 2001;

        return (string) $number;
    }
}
