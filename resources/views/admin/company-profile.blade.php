@extends('adminlte::page')

@section('title', 'Company Profile')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Administration <span>›</span> Company Profile</div>
@stop

@section('content')
    @if (session('success')) <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div> @endif
    @if ($errors->any()) <div class="alert alert-danger"><strong>Please correct the highlighted fields.</strong></div> @endif
    <section class="admin-crud-card" style="max-width:760px;">
        <div class="sales-card-heading"><h2><i class="bi bi-building"></i> Company Profile</h2></div>
        <form method="POST" action="{{ route('company-profile.update') }}" enctype="multipart/form-data" class="admin-crud-form">
            @csrf @method('PUT')
            <div class="field"><label for="logo">Company Logo</label><input id="logo" name="logo" type="file" accept="image/*"></div>
            <div class="field"><label for="name">Company Name</label><input id="name" name="name" required value="{{ old('name', $profile->name) }}"></div>
            <div class="field"><label for="description">Description</label><textarea id="description" name="description" rows="3">{{ old('description', $profile->description) }}</textarea></div>
            <div class="field-row">
                <div class="field"><label for="branch_name">Branch Name</label><input id="branch_name" name="branch_name" value="{{ old('branch_name', $profile->branch_name) }}"></div>
                <div class="field"><label for="branch_title">Branch Title</label><input id="branch_title" name="branch_title" value="{{ old('branch_title', $profile->branch_title) }}"></div>
            </div>
            <div class="field"><label>Invoice Print Type</label><div style="display:flex; gap:1rem;"><label style="font-weight:400;"><input type="radio" name="invoice_print_type" value="a4" style="width:auto;" @checked(old('invoice_print_type', $profile->invoice_print_type ?? 'a4') === 'a4')> A4</label><label style="font-weight:400;"><input type="radio" name="invoice_print_type" value="pos" style="width:auto;" @checked(old('invoice_print_type', $profile->invoice_print_type) === 'pos')> POS</label></div></div>
            <div class="field"><label for="branch_address">Branch Address</label><textarea id="branch_address" name="branch_address" rows="2">{{ old('branch_address', $profile->branch_address) }}</textarea></div>
            <div class="field-row">
                <div class="field"><label for="invoice_header">Invoice Header (1241px * 230px)</label><input id="invoice_header" name="invoice_header" type="file" accept="image/*"></div>
                <div class="field"><label for="invoice_footer">Invoice Footer (1250px * 75px)</label><input id="invoice_footer" name="invoice_footer" type="file" accept="image/*"></div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
        </form>
    </section>
@stop
