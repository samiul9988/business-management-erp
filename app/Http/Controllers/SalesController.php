<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class SalesController extends Controller
{
    public function index(?string $section = null): View
    {
        $sections = [
            'entry' => ['title' => 'Sales Entry', 'icon' => 'bi-currency-dollar', 'description' => 'Create a new customer sale and generate an invoice.', 'route' => 'sales.entry.create'],
            'service-entry' => ['title' => 'Service Entry', 'icon' => 'bi-tools', 'description' => 'Record a service sale or maintenance charge.', 'route' => 'sales.service-entry.create'],
            'sales-return' => ['title' => 'Sales Return', 'icon' => 'bi-arrow-counterclockwise', 'description' => 'Process returned products and update stock.', 'route' => 'sales.return.create'],
            'serial-sales-return' => ['title' => 'Serial Sales Return', 'icon' => 'bi-arrow-counterclockwise', 'description' => 'Return a serialized product by its serial number.', 'route' => 'sales.serial-return.create'],
            'record' => ['title' => 'Sales Record', 'icon' => 'bi-list-ul', 'description' => 'Review completed sales and transaction details.', 'route' => 'sales.record'],
            'return-record' => ['title' => 'Sales Return Record', 'icon' => 'bi-list-ul', 'description' => 'Review completed sales return transactions.', 'route' => 'sales.return-record'],
            'stock-report' => ['title' => 'Stock Report', 'icon' => 'bi-list-check', 'description' => 'Review current product stock and availability.', 'route' => 'sales.stock-report'],
            'serial-history' => ['title' => 'Serial History', 'icon' => 'bi-upc-scan', 'description' => 'Track the complete movement history of a serial number.', 'route' => 'sales.serial-history'],
            'quotation' => ['title' => 'Quotation Entry', 'icon' => 'bi-quote', 'description' => 'Prepare and save a quotation for a customer.', 'route' => 'quotations.entry.create'],
            'quotation-record' => ['title' => 'Quotation Record', 'icon' => 'bi-file-earmark', 'description' => 'Review saved customer quotations.', 'route' => 'quotations.record'],
            'quotation-invoice' => ['title' => 'Quotation Invoice', 'icon' => 'bi-file-earmark-text', 'description' => 'Find and print a saved quotation.', 'route' => 'quotations.invoice.index'],
            'invoice' => ['title' => 'Sales Invoice', 'icon' => 'bi-file-earmark-text', 'description' => 'Find, print, or download sales invoices.', 'route' => 'sales.invoice'],
            'due-list' => ['title' => 'Customer Due List', 'icon' => 'bi-list-ul', 'description' => 'View customers with outstanding balances.', 'route' => 'customer-due-report.index'],
            'payment-report' => ['title' => 'Customer Payment Report', 'icon' => 'bi-credit-card-2-front', 'description' => 'Review customer payment activity.', 'route' => 'customer-payment-report.index'],
            'customers' => ['title' => 'Customer List', 'icon' => 'bi-person-lines-fill', 'description' => 'Manage customer details and contact information.', 'route' => 'customer.index'],
            'top-customers' => ['title' => 'Top Customer List', 'icon' => 'bi-people-fill', 'description' => 'View your highest-value customers.', 'route' => 'sales.top-customers'],
            'employee-sales' => ['title' => 'Employee Wise Top Sales', 'icon' => 'bi-graph-up-arrow', 'description' => 'Compare sales performance by employee.', 'route' => 'sales.employee-sales'],
            'price-list' => ['title' => 'Product Price List', 'icon' => 'bi-cash-stack', 'description' => 'View and print current product prices.', 'route' => 'sales.price-list'],
        ];

        abort_unless($section === null || array_key_exists($section, $sections), 404);

        return view('sales.index', [
            'sections' => $sections,
            'activeSection' => $section,
            'activeItem' => $section ? $sections[$section] : null,
        ]);
    }
}
