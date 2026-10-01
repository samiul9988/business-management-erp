<?php

namespace App\Http\Controllers;

use App\Models\SerialPurchaseReturn;
use App\Models\SerialSalesReturn;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SerialHistoryController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate(['serial_no' => ['nullable', 'string', 'max:100']]);
        $serial = $validated['serial_no'] ?? null;

        $events = collect();

        if ($serial) {
            SerialSalesReturn::query()->where('serial_no', 'like', "%{$serial}%")->latest('return_date')->get()
                ->each(function (SerialSalesReturn $record) use ($events): void {
                    $events->push([
                        'type' => 'Sales Return', 'icon' => 'bi-arrow-counterclockwise', 'date' => $record->return_date,
                        'serial_no' => $record->serial_no, 'product_name' => $record->product_name, 'product_code' => $record->product_code,
                        'reference' => $record->invoice_no, 'party' => $record->customer_name, 'quantity' => $record->quantity,
                        'rate' => $record->rate, 'total' => $record->total, 'reason' => $record->reason,
                    ]);
                });

            SerialPurchaseReturn::query()->where('serial_no', 'like', "%{$serial}%")->latest('return_date')->get()
                ->each(function (SerialPurchaseReturn $record) use ($events): void {
                    $events->push([
                        'type' => 'Purchase Return', 'icon' => 'bi-upc-scan', 'date' => $record->return_date,
                        'serial_no' => $record->serial_no, 'product_name' => $record->product_name, 'product_code' => $record->product_code,
                        'reference' => $record->invoice_no, 'party' => $record->supplier_name, 'quantity' => $record->quantity,
                        'rate' => $record->rate, 'total' => $record->total, 'reason' => $record->reason,
                    ]);
                });

            $events = $events->sortByDesc('date')->values();
        }

        return view('reports.serial-history', ['events' => $events, 'serial' => $serial]);
    }
}
