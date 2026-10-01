@extends('adminlte::page')

@section('title', 'Customer')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Administration <span>›</span> Customer</div>
@stop

@section('content')
    @if (session('success')) <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div> @endif
    @if ($errors->any()) <div class="alert alert-danger"><strong>Please correct the highlighted fields.</strong></div> @endif
    <div class="admin-crud-layout">
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-person-vcard"></i> Add Customer</h2></div>
            <form method="POST" action="{{ route('customer.store') }}" class="admin-crud-form">
                @csrf
                <div class="field"><label>Customer Id</label><input value="{{ $nextCustomerCode }}" disabled></div>
                <div class="field-row">
                    <div class="field"><label for="mobile">Customer Mobile</label><input id="mobile" name="mobile" value="{{ old('mobile') }}"></div>
                    <div class="field"><label for="name">Customer Name</label><input id="name" name="name" required value="{{ old('name') }}"></div>
                </div>
                <div class="field-row">
                    <div class="field"><label for="area_id">Area</label><select id="area_id" name="area_id"><option value="">-- Select --</option>@foreach ($areas as $area)<option value="{{ $area->id }}" @selected(old('area_id') == $area->id)>{{ $area->name }}</option>@endforeach</select></div>
                    <div class="field"><label for="owner_name">Owner Name</label><input id="owner_name" name="owner_name" value="{{ old('owner_name') }}"></div>
                </div>
                <div class="field"><label for="address">Address</label><input id="address" name="address" value="{{ old('address') }}"></div>
                <div class="field-row">
                    <div class="field"><label for="office_phone">Office Phone</label><input id="office_phone" name="office_phone" value="{{ old('office_phone') }}"></div>
                    <div class="field"><label for="email">Customer Email</label><input id="email" name="email" type="email" value="{{ old('email') }}"></div>
                </div>
                <div class="field-row">
                    <div class="field"><label for="birthday">Birthday</label><input id="birthday" name="birthday" type="date" value="{{ old('birthday') }}"></div>
                    <div class="field"><label for="marriage_day">Marriage Day</label><input id="marriage_day" name="marriage_day" type="date" value="{{ old('marriage_day') }}"></div>
                </div>
                <div class="field-row">
                    <div class="field"><label for="previous_due">Previous Due</label><input id="previous_due" name="previous_due" type="number" step=".01" min="0" value="{{ old('previous_due', 0) }}"></div>
                    <div class="field"><label for="credit_limit">Credit Limit</label><input id="credit_limit" name="credit_limit" type="number" step=".01" min="0" value="{{ old('credit_limit', 0) }}"></div>
                </div>
                <div class="field"><label>Customer Type</label><div style="display:flex; gap:1rem;"><label style="font-weight:400;"><input type="radio" name="customer_type" value="regular" style="width:auto;" @checked(old('customer_type', 'regular') === 'regular')> Regular</label><label style="font-weight:400;"><input type="radio" name="customer_type" value="wholesale" style="width:auto;" @checked(old('customer_type') === 'wholesale')> Wholesale</label></div></div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
            </form>
        </section>
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-list-ul"></i> Customer List</h2></div>
            <div class="admin-crud-table-wrap">
                <table class="admin-crud-table">
                    <thead><tr><th>Id</th><th>Name</th><th>Mobile</th><th>Area</th><th>Due</th><th>Action</th></tr></thead>
                    <tbody>
                        @forelse ($customers as $customer)
                            <tr>
                                <td>{{ $customer->customer_code }}</td>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->mobile ?? '-' }}</td>
                                <td>{{ $customer->area?->name ?? '-' }}</td>
                                <td>{{ number_format($customer->previous_due, 2) }}</td>
                                <td class="row-actions">
                                    <form method="POST" action="{{ route('customer.destroy', $customer) }}" onsubmit="return confirm('Delete this customer?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-delete"><i class="bi bi-trash3"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6">No customers added yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@stop
