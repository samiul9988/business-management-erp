<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        return view('admin.customer', [
            'customers' => Customer::with('area')->latest('id')->get(),
            'areas' => Area::orderBy('name')->get(),
            'nextCustomerCode' => $this->nextCustomerCode(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);
        $validated['customer_code'] = $this->nextCustomerCode();
        Customer::create($validated);

        return redirect()->route('customer.index')->with('success', 'Customer added successfully.');
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $customer->update($this->validated($request));

        return redirect()->route('customer.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('customer.index')->with('success', 'Customer deleted successfully.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'mobile' => ['nullable', 'string', 'max:30'],
            'name' => ['required', 'string', 'max:255'],
            'area_id' => ['nullable', 'exists:areas,id'],
            'address' => ['nullable', 'string', 'max:500'],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'office_phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'birthday' => ['nullable', 'date'],
            'marriage_day' => ['nullable', 'date'],
            'previous_due' => ['nullable', 'numeric', 'min:0'],
            'credit_limit' => ['nullable', 'numeric', 'min:0'],
            'customer_type' => ['required', 'in:regular,wholesale'],
        ]);
    }

    private function nextCustomerCode(): string
    {
        $lastCode = Customer::query()->latest('id')->value('customer_code');
        $number = $lastCode ? ((int) $lastCode + 1) : 1001;

        return (string) $number;
    }
}
