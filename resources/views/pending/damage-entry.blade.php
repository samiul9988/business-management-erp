@extends('adminlte::page')

@section('title', 'Damage Entry')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Pending Module <span>›</span> Damage Entry</div>
@stop

@section('content')
    @if (session('success')) <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div> @endif
    @if ($errors->any()) <div class="alert alert-danger"><strong>Please correct the highlighted fields.</strong></div> @endif
    <section class="admin-crud-card" style="max-width:600px;">
        <div class="sales-card-heading"><h2><i class="bi bi-exclamation-triangle"></i> Damage Entry</h2></div>
        <form method="POST" action="{{ route('damage.entry.store') }}" class="admin-crud-form">
            @csrf
            <div class="field"><label>Code</label><input value="{{ $nextInvoice }}" disabled></div>
            <div class="field"><label>Date</label><input name="damage_date" type="date" value="{{ old('damage_date', now()->toDateString()) }}" required></div>
            <div class="field"><label>Product</label><select name="product_id" id="product_id" required><option value="">-- Select --</option>@foreach ($products as $product)<option value="{{ $product->id }}" data-name="{{ $product->name }}" @selected(old('product_id') == $product->id)>{{ $product->product_code }} - {{ $product->name }}</option>@endforeach</select><input type="hidden" name="product_name" id="product_name"></div>
            <div class="field-row">
                <div class="field"><label>Damage Quantity</label><input name="quantity" type="number" step=".01" min="0" required></div>
                <div class="field"><label>Damage Rate</label><input name="rate" type="number" step=".01" min="0" required></div>
            </div>
            <div class="field"><label>Description</label><textarea name="description" rows="2">{{ old('description') }}</textarea></div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
        </form>
    </section>
@stop

@push('js')
<script>
document.querySelector('#product_id').addEventListener('change', event => {
    document.querySelector('#product_name').value = event.target.selectedOptions[0]?.dataset.name || '';
});
</script>
@endpush
