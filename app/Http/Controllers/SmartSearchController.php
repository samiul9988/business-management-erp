<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SmartSearchController extends Controller
{
    public function search(Request $request): RedirectResponse
    {
        $query = trim((string) $request->query('q', ''));

        if ($query === '') {
            return redirect()->route('dashboard');
        }

        // A Bangladeshi mobile number: 11 digits starting with 01, or the
        // same prefixed with the country code (+880/880).
        if (preg_match('/^(\+?880|0)1[3-9]\d{8}$/', $query)) {
            return redirect()->route('customer.index', ['mobile' => $query]);
        }

        // A product barcode (EAN-8/UPC-A/EAN-13 style): digits only.
        if (ctype_digit($query) && strlen($query) >= 6) {
            return redirect()->route('sales.entry.create', ['barcode' => $query]);
        }

        // Anything else (alphanumeric) is treated as a supplier serial number.
        return redirect()->route('supplier.index', ['serial' => $query]);
    }
}
