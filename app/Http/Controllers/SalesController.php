<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class SalesController extends Controller
{
    public function index(?string $section = null): View
    {
        $sections = [
            'entry' => ['title' => 'Sales Entry', 'icon' => 'bi-currency-dollar', 'description' => 'Create a new customer sale and generate an invoice.'],
            'service-entry' => ['title' => 'Service Entry', 'icon' => 'bi-tools', 'description' => 'Record a service sale or maintenance charge.'],
            'sales-return' => ['title' => 'Sales Return', 'icon' => 'bi-arrow-counterclockwise', 'description' => 'Process returned products and update stock.'],
            'serial-sales-return' => ['title' => 'Serial Sales Return', 'icon' => 'bi-arrow-counterclockwise', 'description' => 'Return a serialized product by its serial number.'],
            'record' => ['title' => 'Sales Record', 'icon' => 'bi-list-ul', 'description' => 'Review completed sales and transaction details.'],
            'branch-record' => ['title' => 'All Branch Sales Record', 'icon' => 'bi-list-columns', 'description' => 'Review sales records across every branch.'],
            'stock-report' => ['title' => 'Stock Report', 'icon' => 'bi-list-check', 'description' => 'Review current product stock and availability.'],
            'branch-stock' => ['title' => 'All Branch Stock', 'icon' => 'bi-list-check', 'description' => 'Review stock levels for all branches.'],
            'serial-history' => ['title' => 'Serial History', 'icon' => 'bi-list-check', 'description' => 'Track the complete movement history of a serial number.'],
            'branch-serial-history' => ['title' => 'All Branch Serial History', 'icon' => 'bi-list-check', 'description' => 'Track serialized items across all branches.'],
            'quotation' => ['title' => 'Quotation Entry', 'icon' => 'bi-quote', 'description' => 'Prepare and save a quotation for a customer.'],
            'invoice' => ['title' => 'Sales Invoice', 'icon' => 'bi-file-earmark-text', 'description' => 'Find, print, or download sales invoices.'],
            'return-record' => ['title' => 'Sales Return Record', 'icon' => 'bi-list-ul', 'description' => 'Review completed sales return transactions.'],
            'due-list' => ['title' => 'Customer Due List', 'icon' => 'bi-list-ul', 'description' => 'View customers with outstanding balances.'],
            'payment-report' => ['title' => 'Customer Payment Report', 'icon' => 'bi-credit-card-2-front', 'description' => 'Review customer payment activity.'],
            'payment-history' => ['title' => 'Customer Payment History', 'icon' => 'bi-credit-card-2-front', 'description' => 'View payment history by customer.'],
            'customers' => ['title' => 'Customer List', 'icon' => 'bi-person-lines-fill', 'description' => 'Manage customer details and contact information.'],
            'top-customers' => ['title' => 'Top Customer List', 'icon' => 'bi-people-fill', 'description' => 'View your highest-value customers.'],
            'employee-sales' => ['title' => 'Employee Wise Top Sales', 'icon' => 'bi-graph-up-arrow', 'description' => 'Compare sales performance by employee.'],
            'branch-employee-sales' => ['title' => 'All Branch Employee Wise Top Sales', 'icon' => 'bi-graph-up-arrow', 'description' => 'Compare employee sales across branches.'],
            'price-list' => ['title' => 'Product Price List', 'icon' => 'bi-cash-stack', 'description' => 'View and print current product prices.'],
            'quotation-invoice' => ['title' => 'Quotation Invoice', 'icon' => 'bi-file-earmark-text', 'description' => 'Convert a quotation into a printable invoice.'],
            'quotation-record' => ['title' => 'Quotation Record', 'icon' => 'bi-file-earmark', 'description' => 'Review saved customer quotations.'],
        ];

        abort_unless($section === null || array_key_exists($section, $sections), 404);

        return view('sales.index', [
            'sections' => $sections,
            'activeSection' => $section,
            'activeItem' => $section ? $sections[$section] : null,
        ]);
    }
}
