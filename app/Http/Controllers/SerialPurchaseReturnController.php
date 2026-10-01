<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\SerialPurchaseReturn;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SerialPurchaseReturnController extends Controller
{
    public function create(Request $request): View
    {
        $serialNo = trim((string) $request->query('serial_no', ''));

        return view('purchase.serial-return', ['serialNo' => $serialNo, 'matchedReturn' => $serialNo !== '' ? SerialPurchaseReturn::query()->where('serial_no', $serialNo)->latest('id')->first() : null, 'nextReturn' => $this->nextReturnNumber()]);
    }

    public function store(Request $request, DatabaseManager $database): RedirectResponse
    {
        $validated = $request->validate([
            'serial_no' => ['required', 'string', 'max:150'], 'invoice_no' => ['nullable', 'exists:purchases,invoice_no'],
            'supplier_name' => ['required', 'string', 'max:255'], 'product_code' => ['nullable', 'string', 'max:100'],
            'product_name' => ['required', 'string', 'max:255'], 'return_date' => ['required', 'date'],
            'reason' => ['nullable', 'string', 'max:1000'], 'quantity' => ['required', 'numeric', 'gt:0'],
            'rate' => ['required', 'numeric', 'min:0'],
        ]);

        $serialReturn = $database->transaction(function () use ($validated, $request): SerialPurchaseReturn {
            $purchase = ! empty($validated['invoice_no']) ? Purchase::query()->where('invoice_no', $validated['invoice_no'])->first() : null;
            $total = round((float) $validated['quantity'] * (float) $validated['rate'], 2);
            $serialReturn = SerialPurchaseReturn::create([...$validated, 'user_id' => $request->user()->id, 'purchase_id' => $purchase?->id, 'return_no' => $this->nextReturnNumber(), 'total' => $total]);
            if ($purchase) {
                $purchase->update(['total' => max((float) $purchase->total - $total, 0), 'due' => max((float) $purchase->due - $total, 0)]);
            }

            return $serialReturn;
        });

        return redirect()->route('purchase.serial-return.create')->with('success', "Serial return {$serialReturn->return_no} saved successfully.");
    }

    private function nextReturnNumber(): string
    {
        $lastReturn = SerialPurchaseReturn::query()->latest('id')->value('return_no');
        $number = $lastReturn ? ((int) $lastReturn + 1) : 360400001;

        return str_pad((string) $number, 9, '0', STR_PAD_LEFT);
    }
}
