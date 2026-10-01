@extends('adminlte::page')

@section('title', 'Serial History')

@section('content_header')
    <div class="sales-breadcrumb"><i class="bi bi-house-door-fill"></i> Home <span>›</span> Sales Module <span>›</span> Serial History</div>
@stop

@section('content')
    <section class="admin-crud-card">
        <div class="sales-card-heading"><h2><i class="bi bi-upc-scan"></i> Serial History</h2></div>
        <form method="GET" action="{{ route('sales.serial-history') }}" class="record-filter-card">
            <div class="record-filter-field record-search"><label for="serial_no">Serial / IMEI No</label><input id="serial_no" name="serial_no" value="{{ $serial }}" placeholder="Enter serial number" autofocus></div>
            <button class="btn btn-primary record-search-button" type="submit"><i class="bi bi-search"></i> Search</button>
        </form>

        @if ($serial && $events->isEmpty())
            <div class="alert alert-warning">No return history found for serial "{{ $serial }}".</div>
        @endif

        @if ($events->isNotEmpty())
            <div class="admin-crud-table-wrap">
                <table class="admin-crud-table">
                    <thead><tr><th>Date</th><th>Event</th><th>Serial No</th><th>Product</th><th>Reference</th><th>Party</th><th>Quantity</th><th>Rate</th><th>Total</th><th>Reason</th></tr></thead>
                    <tbody>
                        @foreach ($events as $event)
                            <tr>
                                <td>{{ $event['date']?->format('d/m/Y') }}</td>
                                <td><i class="bi {{ $event['icon'] }}"></i> {{ $event['type'] }}</td>
                                <td>{{ $event['serial_no'] }}</td>
                                <td>{{ $event['product_name'] }} ({{ $event['product_code'] }})</td>
                                <td>{{ $event['reference'] }}</td>
                                <td>{{ $event['party'] }}</td>
                                <td>{{ number_format((float) $event['quantity'], 2) }}</td>
                                <td>{{ number_format((float) $event['rate'], 2) }}</td>
                                <td>{{ number_format((float) $event['total'], 2) }}</td>
                                <td>{{ $event['reason'] ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@stop
