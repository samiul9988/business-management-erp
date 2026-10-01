<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SerialSalesReturn;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SerialSalesReturnController extends Controller
{
    public function create(Request $request): View
    {
        $serialNo = trim((string) $request->query('serial_no', ''));

        return view('sales.serial-return', ['serialNo' => $serialNo, 'matchedReturn' => $serialNo !== '' ? SerialSalesReturn::query()->where('serial_no', $serialNo)->latest('id')->first() : null, 'nextReturn' => $this->nextReturnNumber()]);
    }

    public function store(Request $request, DatabaseManager $database): RedirectResponse
    {
        $validated = $request->validate([
            'serial_no' => ['required', 'string', 'max:150'], 'invoice_no' => ['nullable', 'exists:sales,invoice_no'],
            'customer_name' => ['required', 'string', 'max:255'], 'product_code' => ['nullable', 'string', 'max:100'],
            'product_name' => ['required', 'string', 'max:255'], 'return_date' => ['required', 'date'],
            'reason' => ['nullable', 'string', 'max:1000'], 'quantity' => ['required', 'numeric', 'gt:0'],
            'rate' => ['required', 'numeric', 'min:0'],
        ]);

        $serialReturn = $database->transaction(function () use ($validated, $request): SerialSalesReturn {
            $sale = ! empty($validated['invoice_no']) ? Sale::query()->where('invoice_no', $validated['invoice_no'])->first() : null;
            $total = round((float) $validated['quantity'] * (float) $validated['rate'], 2);
            $serialReturn = SerialSalesReturn::create([...$validated, 'user_id' => $request->user()->id, 'sale_id' => $sale?->id, 'return_no' => $this->nextReturnNumber(), 'total' => $total]);
            if ($sale) {
                $sale->update(['total' => max((float) $sale->total - $total, 0), 'due' => max((float) $sale->due - $total, 0)]);
            }

            return $serialReturn;
        });

        return redirect()->route('sales.serial-return.create')->with('success', "Serial return {$serialReturn->return_no} saved successfully.");
    }

    private function nextReturnNumber(): string
    {
        $lastReturn = SerialSalesReturn::query()->latest('id')->value('return_no');
        $number = $lastReturn ? ((int) $lastReturn + 1) : 260400001;

        return str_pad((string) $number, 9, '0', STR_PAD_LEFT);
    }
}
