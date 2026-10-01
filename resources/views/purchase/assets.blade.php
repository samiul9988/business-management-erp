@extends('adminlte::page')

@section('title', 'Assets Entry')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Purchase Module <span>›</span> Assets Entry</div>
@stop

@section('content')
    @if (session('success')) <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div> @endif
    @if ($errors->any()) <div class="alert alert-danger"><strong>Please correct the highlighted fields.</strong></div> @endif
    <div class="admin-crud-layout" style="grid-template-columns: 1fr 1fr;">
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-bag-plus"></i> Assets Purchase</h2></div>
            <form method="POST" action="{{ route('assets.store') }}" class="admin-crud-form">
                @csrf
                <input type="hidden" name="type" value="purchase">
                <div class="field"><label>Assets Name</label><input name="name" required></div>
                <div class="field"><label>Supplier Name</label><input name="party_name"></div>
                <div class="field-row">
                    <div class="field"><label>Rate</label><input name="rate" type="number" step=".01" min="0" value="0"></div>
                    <div class="field"><label>Quantity</label><input name="quantity" type="number" step=".01" min="0" value="1"></div>
                </div>
                <div class="field"><label>Amount</label><input name="amount" type="number" step=".01" min="0" required></div>
                <div class="field"><label>Note</label><textarea name="note" rows="2"></textarea></div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
            </form>
        </section>
        <section class="admin-crud-card">
            <div class="sales-card-heading"><h2><i class="bi bi-bag-check"></i> Assets Sale</h2></div>
            <form method="POST" action="{{ route('assets.store') }}" class="admin-crud-form">
                @csrf
                <input type="hidden" name="type" value="sale">
                <div class="field"><label>Assets Name</label><input name="name" required></div>
                <div class="field"><label>Customer Name</label><input name="party_name"></div>
                <div class="field-row">
                    <div class="field"><label>Valuation</label><input name="rate" type="number" step=".01" min="0" value="0"></div>
                    <div class="field"><label>S. Rate</label><input name="quantity" type="number" step=".01" min="0" value="1"></div>
                </div>
                <div class="field"><label>Amount</label><input name="amount" type="number" step=".01" min="0" required></div>
                <div class="field"><label>Note</label><textarea name="note" rows="2"></textarea></div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
            </form>
        </section>
    </div>
    <section class="admin-crud-card" style="margin-top:1rem;">
        <div class="sales-card-heading"><h2><i class="bi bi-list-ul"></i> Assets Record</h2></div>
        <div class="admin-crud-table-wrap">
            <table class="admin-crud-table">
                <thead><tr><th>Type</th><th>Name</th><th>Party</th><th>Rate</th><th>Quantity</th><th>Amount</th><th>Note</th></tr></thead>
                <tbody>
                    @forelse ($assets as $asset)
                        <tr>
                            <td>{{ ucfirst($asset->type) }}</td>
                            <td>{{ $asset->name }}</td>
                            <td>{{ $asset->party_name ?? '-' }}</td>
                            <td>{{ number_format($asset->rate, 2) }}</td>
                            <td>{{ number_format($asset->quantity, 2) }}</td>
                            <td>{{ number_format($asset->amount, 2) }}</td>
                            <td>{{ $asset->note ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7">No assets recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@stop
