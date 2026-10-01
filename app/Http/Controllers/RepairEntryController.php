<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Repair;
use App\Models\RepairCompany;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RepairEntryController extends Controller
{
    public function create(): View
    {
        return view('repair.entry', [
            'employees' => Employee::where('status', 'active')->orderBy('name')->get(),
            'repairCompanies' => RepairCompany::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
            'nextInvoice' => $this->nextInvoiceNumber(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'repair_date' => ['required', 'date'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_mobile' => ['nullable', 'string', 'max:30'],
            'customer_address' => ['nullable', 'string', 'max:1000'],
            'assigned_employee_id' => ['nullable', 'exists:employees,id'],
            'expected_delivery_date' => ['nullable', 'date'],
            'status' => ['required', 'in:pending,completed,non_completed,delivered,transfer,received'],
            'repair_company_id' => ['nullable', 'exists:repair_companies,id'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'serial_no' => ['nullable', 'string', 'max:255'],
            'problem' => ['nullable', 'string', 'max:1000'],
            'warranty_period' => ['nullable', 'boolean'],
            'extra_received' => ['nullable', 'boolean'],
            'parts_name' => ['nullable', 'string', 'max:255'],
            'parts_supplier_id' => ['nullable', 'exists:suppliers,id'],
            'parts_purchase_date' => ['nullable', 'date'],
            'parts_purchase_rate' => ['nullable', 'numeric', 'min:0'],
            'parts_quantity' => ['nullable', 'numeric', 'min:0'],
            'parts_warranty_days' => ['nullable', 'integer', 'min:0'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,mfs'],
            'paid' => ['nullable', 'numeric', 'min:0'],
        ]);

        $partsAmount = round((float) ($validated['parts_purchase_rate'] ?? 0) * (float) ($validated['parts_quantity'] ?? 0), 2);
        $total = max(round((float) $validated['subtotal'] - (float) ($validated['discount'] ?? 0), 2), 0);
        $paid = round((float) ($validated['paid'] ?? 0), 2);

        $repair = Repair::create([
            ...$validated,
            'user_id' => $request->user()->id,
            'invoice_no' => $this->nextInvoiceNumber(),
            'warranty_period' => $request->boolean('warranty_period'),
            'extra_received' => $request->boolean('extra_received'),
            'parts_purchase_amount' => $partsAmount,
            'total' => $total,
            'paid' => $paid,
            'due' => max($total - $paid, 0),
        ]);

        return redirect()->route('repair.entry.create')->with('success', "Repair {$repair->invoice_no} saved successfully.");
    }

    private function nextInvoiceNumber(): string
    {
        $lastInvoice = Repair::query()->latest('id')->value('invoice_no');
        $number = $lastInvoice ? ((int) $lastInvoice + 1) : 460100001;

        return str_pad((string) $number, 9, '0', STR_PAD_LEFT);
    }
}
