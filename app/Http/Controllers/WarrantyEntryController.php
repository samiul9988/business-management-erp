<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Supplier;
use App\Models\Warranty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WarrantyEntryController extends Controller
{
    public function create(): View
    {
        return view('warranty.entry', [
            'employees' => Employee::where('status', 'active')->orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
            'nextInvoice' => $this->nextInvoiceNumber(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'warranty_date' => ['required', 'date'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_mobile' => ['nullable', 'string', 'max:30'],
            'received_by_employee_id' => ['nullable', 'exists:employees,id'],
            'warranty_status' => ['required', 'in:pending,transferred_to_supplier,received_from_supplier,delivered_from_shop,adjusted_by_supplier,delivered,warranty_void,archived'],
            'estimated_delivery_date' => ['nullable', 'date'],
            'claim_status' => ['nullable', 'string', 'max:255'],
            'product_name' => ['nullable', 'string', 'max:255'],
            'sale_out_date' => ['nullable', 'date'],
            'serial_no' => ['nullable', 'string', 'max:255'],
            'quantity' => ['nullable', 'numeric', 'min:0'],
            'problem' => ['nullable', 'string', 'max:500'],
            'condition' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:1000'],
            'warranty_validity' => ['nullable', 'boolean'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'transfer_by_employee_id' => ['nullable', 'exists:employees,id'],
            'transfer_date' => ['nullable', 'date'],
            'receive_by_sr' => ['nullable', 'string', 'max:255'],
            'return_received_by_employee_id' => ['nullable', 'exists:employees,id'],
            'return_condition' => ['nullable', 'string', 'max:255'],
            'received_date' => ['nullable', 'date'],
            'delivered_by_employee_id' => ['nullable', 'exists:employees,id'],
            'remarks' => ['nullable', 'string', 'max:1000'],
            'delivered_date' => ['nullable', 'date'],
        ]);
        $validated['user_id'] = $request->user()->id;
        $validated['invoice_no'] = $this->nextInvoiceNumber();
        $validated['warranty_validity'] = $request->boolean('warranty_validity');
        $warranty = Warranty::create($validated);

        return redirect()->route('warranty.entry.create')->with('success', "Warranty {$warranty->invoice_no} saved successfully.");
    }

    private function nextInvoiceNumber(): string
    {
        $lastInvoice = Warranty::query()->latest('id')->value('invoice_no');
        $number = $lastInvoice ? ((int) $lastInvoice + 1) : 560100001;

        return str_pad((string) $number, 9, '0', STR_PAD_LEFT);
    }
}
