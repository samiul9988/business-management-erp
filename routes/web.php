<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\BusinessMonitorController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\BankTransactionController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CashTransactionController;
use App\Http\Controllers\CashTransferController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChequeController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\CompanyProfileController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerDueReportController;
use App\Http\Controllers\CustomerPaymentController;
use App\Http\Controllers\CustomerPaymentReportController;
use App\Http\Controllers\DamageEntryController;
use App\Http\Controllers\DamageRecordController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeListController;
use App\Http\Controllers\EmployeeSalesController;
use App\Http\Controllers\InvestmentAccountController;
use App\Http\Controllers\InvestmentTransactionController;
use App\Http\Controllers\LoanAccountController;
use App\Http\Controllers\LoanTransactionController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\MonthController;
use App\Http\Controllers\CashViewController;
use App\Http\Controllers\DailyProfitLossController;
use App\Http\Controllers\PendingSalesController;
use App\Http\Controllers\PriceListController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfitLossReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseEntryController;
use App\Http\Controllers\PurchaseRecordController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\QuotationInvoiceController;
use App\Http\Controllers\QuotationRecordController;
use App\Http\Controllers\RepairCompanyController;
use App\Http\Controllers\RepairEntryController;
use App\Http\Controllers\RepairRecordController;
use App\Http\Controllers\SalaryGenerateController;
use App\Http\Controllers\SalaryPaymentController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SalesEntryController;
use App\Http\Controllers\SalesInvoiceController;
use App\Http\Controllers\SalesRecordController;
use App\Http\Controllers\SalesReturnController;
use App\Http\Controllers\SalesReturnRecordController;
use App\Http\Controllers\SerialHistoryController;
use App\Http\Controllers\SerialPurchaseReturnController;
use App\Http\Controllers\SerialSalesReturnController;
use App\Http\Controllers\ServiceEntryController;
use App\Http\Controllers\SmartSearchController;
use App\Http\Controllers\StockReportController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierDueReportController;
use App\Http\Controllers\SupplierPaymentController;
use App\Http\Controllers\SupplierPaymentReportController;
use App\Http\Controllers\SupplierPurchaseRecordController;
use App\Http\Controllers\TopCustomerController;
use App\Http\Controllers\TransactionAccountController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\WarrantyEntryController;
use App\Http\Controllers\WarrantyRecordController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

Route::get('/search', [SmartSearchController::class, 'search'])->middleware('auth')->name('search');
Route::get('/modules/{slug}', [ModuleController::class, 'show'])->middleware('auth')->name('modules.show');

Route::middleware('auth')->prefix('sales')->name('sales.')->group(function () {
    Route::get('/entry', [SalesEntryController::class, 'create'])->name('entry.create');
    Route::post('/entry', [SalesEntryController::class, 'store'])->name('entry.store');
    Route::get('/service-entry', [ServiceEntryController::class, 'create'])->name('service-entry.create');
    Route::post('/service-entry', [ServiceEntryController::class, 'store'])->name('service-entry.store');
    Route::get('/return', [SalesReturnController::class, 'create'])->name('return.create');
    Route::post('/return', [SalesReturnController::class, 'store'])->name('return.store');
    Route::get('/serial-return', [SerialSalesReturnController::class, 'create'])->name('serial-return.create');
    Route::post('/serial-return', [SerialSalesReturnController::class, 'store'])->name('serial-return.store');
    Route::get('/record', [SalesRecordController::class, 'index'])->name('record');
    Route::get('/return-record', [SalesReturnRecordController::class, 'index'])->name('return-record');
    Route::get('/stock-report', [StockReportController::class, 'index'])->name('stock-report');
    Route::get('/serial-history', [SerialHistoryController::class, 'index'])->name('serial-history');
    Route::get('/invoice', [SalesInvoiceController::class, 'index'])->name('invoice');
    Route::get('/record/{sale}/invoice', [SalesInvoiceController::class, 'show'])->name('record.invoice');
    Route::get('/top-customers', [TopCustomerController::class, 'index'])->name('top-customers');
    Route::get('/employee-sales', [EmployeeSalesController::class, 'index'])->name('employee-sales');
    Route::get('/price-list', [PriceListController::class, 'index'])->name('price-list');
    Route::get('/', [SalesController::class, 'index'])->name('index');
    Route::get('/{section}', [SalesController::class, 'index'])->name('section');
});

Route::middleware('auth')->prefix('quotations')->name('quotations.')->group(function () {
    Route::get('/entry', [QuotationController::class, 'create'])->name('entry.create');
    Route::post('/entry', [QuotationController::class, 'store'])->name('entry.store');
    Route::get('/record', [QuotationRecordController::class, 'index'])->name('record');
    Route::get('/invoice', [QuotationInvoiceController::class, 'index'])->name('invoice.index');
    Route::get('/invoice/{quotation}', [QuotationInvoiceController::class, 'show'])->name('invoice.show');
});

Route::middleware('auth')->prefix('business-monitor')->name('business-monitor.')->group(function () {
    Route::get('/', [BusinessMonitorController::class, 'index'])->name('index');
    Route::get('/{section}', [BusinessMonitorController::class, 'index'])->name('section');
});

Route::middleware('auth')->group(function () {
    Route::get('/category', [CategoryController::class, 'index'])->name('category.index');
    Route::post('/category', [CategoryController::class, 'store'])->name('category.store');
    Route::put('/category/{category}', [CategoryController::class, 'update'])->name('category.update');
    Route::delete('/category/{category}', [CategoryController::class, 'destroy'])->name('category.destroy');

    Route::get('/brand', [BrandController::class, 'index'])->name('brand.index');
    Route::post('/brand', [BrandController::class, 'store'])->name('brand.store');
    Route::put('/brand/{brand}', [BrandController::class, 'update'])->name('brand.update');
    Route::delete('/brand/{brand}', [BrandController::class, 'destroy'])->name('brand.destroy');

    Route::get('/color', [ColorController::class, 'index'])->name('color.index');
    Route::post('/color', [ColorController::class, 'store'])->name('color.store');
    Route::put('/color/{color}', [ColorController::class, 'update'])->name('color.update');
    Route::delete('/color/{color}', [ColorController::class, 'destroy'])->name('color.destroy');

    Route::get('/unit', [UnitController::class, 'index'])->name('unit.index');
    Route::post('/unit', [UnitController::class, 'store'])->name('unit.store');
    Route::put('/unit/{unit}', [UnitController::class, 'update'])->name('unit.update');
    Route::delete('/unit/{unit}', [UnitController::class, 'destroy'])->name('unit.destroy');

    Route::get('/area', [AreaController::class, 'index'])->name('area.index');
    Route::post('/area', [AreaController::class, 'store'])->name('area.store');
    Route::put('/area/{area}', [AreaController::class, 'update'])->name('area.update');
    Route::delete('/area/{area}', [AreaController::class, 'destroy'])->name('area.destroy');

    Route::get('/product', [ProductController::class, 'index'])->name('product.index');
    Route::post('/product', [ProductController::class, 'store'])->name('product.store');
    Route::put('/product/{product}', [ProductController::class, 'update'])->name('product.update');
    Route::delete('/product/{product}', [ProductController::class, 'destroy'])->name('product.destroy');

    Route::get('/customer', [CustomerController::class, 'index'])->name('customer.index');
    Route::post('/customer', [CustomerController::class, 'store'])->name('customer.store');
    Route::put('/customer/{customer}', [CustomerController::class, 'update'])->name('customer.update');
    Route::delete('/customer/{customer}', [CustomerController::class, 'destroy'])->name('customer.destroy');

    Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier.index');
    Route::post('/supplier', [SupplierController::class, 'store'])->name('supplier.store');
    Route::put('/supplier/{supplier}', [SupplierController::class, 'update'])->name('supplier.update');
    Route::delete('/supplier/{supplier}', [SupplierController::class, 'destroy'])->name('supplier.destroy');

    Route::get('/companyProfile', [CompanyProfileController::class, 'edit'])->name('company-profile.edit');
    Route::put('/companyProfile', [CompanyProfileController::class, 'update'])->name('company-profile.update');

    Route::get('/purchase', [PurchaseEntryController::class, 'create'])->name('purchase.entry.create');
    Route::post('/purchase', [PurchaseEntryController::class, 'store'])->name('purchase.entry.store');
    Route::get('/purchaseReturns', [PurchaseReturnController::class, 'create'])->name('purchase.return.create');
    Route::post('/purchaseReturns', [PurchaseReturnController::class, 'store'])->name('purchase.return.store');
    Route::get('/serial_purchase_return', [SerialPurchaseReturnController::class, 'create'])->name('purchase.serial-return.create');
    Route::post('/serial_purchase_return', [SerialPurchaseReturnController::class, 'store'])->name('purchase.serial-return.store');
    Route::get('/purchaseRecord', [PurchaseRecordController::class, 'index'])->name('purchase.record');
    Route::get('/AssetsEntry', [AssetController::class, 'create'])->name('assets.create');
    Route::post('/AssetsEntry', [AssetController::class, 'store'])->name('assets.store');

    Route::get('/account', [TransactionAccountController::class, 'index'])->name('account.index');
    Route::post('/account', [TransactionAccountController::class, 'store'])->name('account.store');
    Route::delete('/account/{account}', [TransactionAccountController::class, 'destroy'])->name('account.destroy');

    Route::get('/bank_accounts', [BankAccountController::class, 'index'])->name('bank-account.index');
    Route::post('/bank_accounts', [BankAccountController::class, 'store'])->name('bank-account.store');
    Route::delete('/bank_accounts/{bankAccount}', [BankAccountController::class, 'destroy'])->name('bank-account.destroy');

    Route::get('/loan_accounts', [LoanAccountController::class, 'index'])->name('loan-account.index');
    Route::post('/loan_accounts', [LoanAccountController::class, 'store'])->name('loan-account.store');
    Route::delete('/loan_accounts/{loanAccount}', [LoanAccountController::class, 'destroy'])->name('loan-account.destroy');

    Route::get('/investment_account', [InvestmentAccountController::class, 'index'])->name('investment-account.index');
    Route::post('/investment_account', [InvestmentAccountController::class, 'store'])->name('investment-account.store');
    Route::delete('/investment_account/{investmentAccount}', [InvestmentAccountController::class, 'destroy'])->name('investment-account.destroy');

    Route::get('/cashTransaction', [CashTransactionController::class, 'index'])->name('cash-transaction.index');
    Route::post('/cashTransaction', [CashTransactionController::class, 'store'])->name('cash-transaction.store');
    Route::delete('/cashTransaction/{cashTransaction}', [CashTransactionController::class, 'destroy'])->name('cash-transaction.destroy');

    Route::get('/bank_transactions', [BankTransactionController::class, 'index'])->name('bank-transaction.index');
    Route::post('/bank_transactions', [BankTransactionController::class, 'store'])->name('bank-transaction.store');
    Route::delete('/bank_transactions/{bankTransaction}', [BankTransactionController::class, 'destroy'])->name('bank-transaction.destroy');

    Route::get('/customer_payment', [CustomerPaymentController::class, 'index'])->name('customer-payment.index');
    Route::post('/customer_payment', [CustomerPaymentController::class, 'store'])->name('customer-payment.store');

    Route::get('/supplier_payment', [SupplierPaymentController::class, 'index'])->name('supplier-payment.index');
    Route::post('/supplier_payment', [SupplierPaymentController::class, 'store'])->name('supplier-payment.store');

    Route::get('/cash_transfer', [CashTransferController::class, 'index'])->name('cash-transfer.index');
    Route::post('/cash_transfer', [CashTransferController::class, 'store'])->name('cash-transfer.store');

    Route::get('/loan_transactions', [LoanTransactionController::class, 'index'])->name('loan-transaction.index');
    Route::post('/loan_transactions', [LoanTransactionController::class, 'store'])->name('loan-transaction.store');

    Route::get('/investment_transactions', [InvestmentTransactionController::class, 'index'])->name('investment-transaction.index');
    Route::post('/investment_transactions', [InvestmentTransactionController::class, 'store'])->name('investment-transaction.store');

    Route::get('/check/entry', [ChequeController::class, 'create'])->name('cheque.create');
    Route::post('/check/entry', [ChequeController::class, 'store'])->name('cheque.store');
    Route::get('/check/list', [ChequeController::class, 'index'])->name('cheque.list');
    Route::get('/check/pending/list', fn () => redirect()->route('cheque.list', ['status' => 'pending']))->name('cheque.pending-list');
    Route::get('/check/paid/list', fn () => redirect()->route('cheque.list', ['status' => 'paid']))->name('cheque.paid-list');
    Route::get('/check/dis/list', fn () => redirect()->route('cheque.list', ['status' => 'dishonoured']))->name('cheque.dishonoured-list');
    Route::get('/check/reminder/list', fn () => redirect()->route('cheque.list', ['status' => 'reminder']))->name('cheque.reminder-list');

    Route::get('/designation', [DesignationController::class, 'index'])->name('designation.index');
    Route::post('/designation', [DesignationController::class, 'store'])->name('designation.store');
    Route::delete('/designation/{designation}', [DesignationController::class, 'destroy'])->name('designation.destroy');

    Route::get('/depertment', [DepartmentController::class, 'index'])->name('department.index');
    Route::post('/depertment', [DepartmentController::class, 'store'])->name('department.store');
    Route::delete('/depertment/{department}', [DepartmentController::class, 'destroy'])->name('department.destroy');

    Route::get('/month', [MonthController::class, 'index'])->name('month.index');
    Route::post('/month', [MonthController::class, 'store'])->name('month.store');
    Route::delete('/month/{month}', [MonthController::class, 'destroy'])->name('month.destroy');

    Route::get('/employee', [EmployeeController::class, 'index'])->name('employee.index');
    Route::post('/employee', [EmployeeController::class, 'store'])->name('employee.store');
    Route::delete('/employee/{employee}', [EmployeeController::class, 'destroy'])->name('employee.destroy');
    Route::get('/employee_list', [EmployeeListController::class, 'index'])->name('employee.list');
    Route::get('/emplists/{status}', [EmployeeListController::class, 'index'])->name('employee.list-by-status');

    Route::get('/salary_generate', [SalaryGenerateController::class, 'index'])->name('salary-generate.index');
    Route::post('/salary_generate', [SalaryGenerateController::class, 'store'])->name('salary-generate.store');

    Route::get('/salary_payment', [SalaryPaymentController::class, 'index'])->name('salary-payment.index');
    Route::post('/salary_payment', [SalaryPaymentController::class, 'store'])->name('salary-payment.store');

    Route::get('/repair', [RepairEntryController::class, 'create'])->name('repair.entry.create');
    Route::post('/repair', [RepairEntryController::class, 'store'])->name('repair.entry.store');
    Route::get('/repair_company', [RepairCompanyController::class, 'index'])->name('repair-company.index');
    Route::post('/repair_company', [RepairCompanyController::class, 'store'])->name('repair-company.store');
    Route::delete('/repair_company/{repairCompany}', [RepairCompanyController::class, 'destroy'])->name('repair-company.destroy');
    Route::get('/repair_record', [RepairRecordController::class, 'index'])->name('repair.record');
    Route::get('/repairStock', fn () => redirect()->route('repair.record', ['status' => 'pending']))->name('repair.in-stock');
    Route::get('/repairOutStock', fn () => redirect()->route('repair.record', ['status' => 'delivered']))->name('repair.out-stock');

    Route::get('/warranty', [WarrantyEntryController::class, 'create'])->name('warranty.entry.create');
    Route::post('/warranty', [WarrantyEntryController::class, 'store'])->name('warranty.entry.store');
    Route::get('/warrantyRecord', [WarrantyRecordController::class, 'index'])->name('warranty.record');

    Route::get('/customerDue', [CustomerDueReportController::class, 'index'])->name('customer-due-report.index');
    Route::get('/supplierDue', [SupplierDueReportController::class, 'index'])->name('supplier-due-report.index');
    Route::get('/customerPaymentReport', [CustomerPaymentReportController::class, 'index'])->name('customer-payment-report.index');
    Route::get('/supplierPaymentReport', [SupplierPaymentReportController::class, 'index'])->name('supplier-payment-report.index');
    Route::get('/supplier_report', [SupplierPurchaseRecordController::class, 'index'])->name('supplier-purchase-record.index');

    Route::get('/pending_sales', [PendingSalesController::class, 'index'])->name('pending-sales.index');
    Route::get('/damageEntry', [DamageEntryController::class, 'create'])->name('damage.entry.create');
    Route::post('/damageEntry', [DamageEntryController::class, 'store'])->name('damage.entry.store');
    Route::get('/damageList', [DamageRecordController::class, 'index'])->name('damage.record');

    Route::get('/profitLoss', [ProfitLossReportController::class, 'index'])->name('profit-loss.index');
    Route::get('/daily_profit_loss', [DailyProfitLossController::class, 'index'])->name('daily-profit-loss.index');
    Route::get('/cash_view', [CashViewController::class, 'index'])->name('cash-view.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
