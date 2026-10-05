<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Asset;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Brand;
use App\Models\CashTransaction;
use App\Models\CashTransfer;
use App\Models\Category;
use App\Models\Cheque;
use App\Models\Color;
use App\Models\CompanyProfile;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\Damage;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\InvestmentAccount;
use App\Models\InvestmentTransaction;
use App\Models\LoanAccount;
use App\Models\LoanTransaction;
use App\Models\Month;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Quotation;
use App\Models\Repair;
use App\Models\RepairCompany;
use App\Models\Sale;
use App\Models\SalesReturn;
use App\Models\Salary;
use App\Models\SalaryPayment;
use App\Models\SerialPurchaseReturn;
use App\Models\SerialSalesReturn;
use App\Models\ServiceEntry;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\TransactionAccount;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warranty;
use Illuminate\Database\Seeder;

/**
 * Seeds ~10 realistic records per feature for a computer, CCTV, laptop sales
 * and service center, so every module (sales, purchase, repair, warranty,
 * accounts, HR, reports) has data to browse and the dashboard search can be
 * exercised end to end.
 */
class BusinessDemoSeeder extends Seeder
{
    private const COUNT = 10;

    /**
     * Most "Record"/report pages default their date filter to the current
     * month, so primary transaction dates must land inside it or the seeded
     * rows are invisible until the filter is widened.
     */
    private function thisMonthDate(): string
    {
        return fake()->dateTimeBetween(now()->startOfMonth(), now())->format('Y-m-d');
    }

    public function run(): void
    {
        $user = User::first() ?? User::factory()->create(['name' => 'Test User', 'email' => 'test@example.com']);

        $areas = $this->seedAreas();
        $categories = $this->seedCategories();
        $brands = $this->seedBrands();
        $colors = $this->seedColors();
        $units = $this->seedUnits();
        $products = $this->seedProducts($categories, $brands, $colors, $units);
        $customers = $this->seedCustomers($areas);
        $suppliers = $this->seedSuppliers();
        $this->seedCompanyProfile();

        $transactionAccounts = $this->seedTransactionAccounts();
        $bankAccounts = $this->seedBankAccounts();
        $loanAccounts = $this->seedLoanAccounts();
        $investmentAccounts = $this->seedInvestmentAccounts();

        $designations = $this->seedDesignations();
        $departments = $this->seedDepartments();
        $months = $this->seedMonths();
        $employees = $this->seedEmployees($designations, $departments);
        $repairCompanies = $this->seedRepairCompanies();

        $sales = $this->seedSales($user, $products);
        $purchases = $this->seedPurchases($user, $suppliers, $products);
        $this->seedServiceEntries($user, $products);
        $this->seedSalesReturns($user, $sales);
        $this->seedPurchaseReturns($user, $purchases);
        $this->seedSerialSalesReturns($user, $sales, $products);
        $this->seedSerialPurchaseReturns($user, $purchases, $products);

        $this->seedRepairs($user, $customers, $employees, $repairCompanies, $suppliers);
        $this->seedWarranties($user, $customers, $employees, $suppliers);
        $this->seedDamages($user, $products);
        $this->seedQuotations($user, $products);
        $this->seedAssets($user);
        $this->seedCheques($user, $customers);

        $this->seedCashTransactions($user, $transactionAccounts);
        $this->seedBankTransactions($user, $bankAccounts);
        $this->seedCustomerPayments($user, $customers, $bankAccounts);
        $this->seedSupplierPayments($user, $suppliers, $bankAccounts);
        $this->seedCashTransfers($user, $bankAccounts);
        $this->seedLoanTransactions($user, $loanAccounts, $bankAccounts);
        $this->seedInvestmentTransactions($user, $investmentAccounts, $bankAccounts);
        $this->seedSalariesAndPayments($user, $employees, $months, $bankAccounts);
    }

    private function seedAreas()
    {
        $names = ['Dhanmondi', 'Gulshan', 'Banani', 'Mirpur', 'Uttara', 'Motijheel', 'Mohammadpur', 'Badda', 'Khilgaon', 'Farmgate'];

        return collect($names)->map(fn ($name) => Area::firstOrCreate(['name' => $name]));
    }

    private function seedCategories()
    {
        $names = ['Desktop Computer', 'Laptop', 'CCTV Camera', 'DVR/NVR', 'Printer', 'Networking', 'Monitor', 'Storage', 'Accessories', 'UPS'];

        return collect($names)->map(fn ($name) => Category::firstOrCreate(['name' => $name]));
    }

    private function seedBrands()
    {
        $names = ['Dell', 'HP', 'Asus', 'Lenovo', 'Hikvision', 'Dahua', 'Epson', 'TP-Link', 'Samsung', 'Logitech'];

        return collect($names)->map(fn ($name) => Brand::firstOrCreate(['name' => $name]));
    }

    private function seedColors()
    {
        $names = ['Black', 'Silver', 'White', 'Grey', 'Blue', 'Red', 'Gold', 'Space Grey', 'Charcoal', 'Navy'];

        return collect($names)->map(fn ($name) => Color::firstOrCreate(['name' => $name]));
    }

    private function seedUnits()
    {
        $units = [
            ['name' => 'Piece', 'description' => 'Single unit'],
            ['name' => 'Set', 'description' => 'A complete set'],
            ['name' => 'Box', 'description' => 'Boxed quantity'],
            ['name' => 'Pair', 'description' => 'Two matching units'],
            ['name' => 'Meter', 'description' => 'Cable length'],
            ['name' => 'Roll', 'description' => 'Cable roll'],
            ['name' => 'Pack', 'description' => 'Packaged quantity'],
            ['name' => 'Unit', 'description' => 'Generic unit'],
            ['name' => 'Carton', 'description' => 'Carton quantity'],
            ['name' => 'Dozen', 'description' => 'Twelve pieces'],
        ];

        return collect($units)->map(fn ($unit) => Unit::firstOrCreate(['name' => $unit['name']], $unit));
    }

    private function seedProducts($categories, $brands, $colors, $units)
    {
        $products = [
            ['name' => 'Dell Vostro 3510 Laptop', 'category' => 'Laptop', 'brand' => 'Dell', 'rate' => 58000],
            ['name' => 'HP Pavilion 15 Laptop', 'category' => 'Laptop', 'brand' => 'HP', 'rate' => 62000],
            ['name' => 'Asus Desktop Core i5 PC', 'category' => 'Desktop Computer', 'brand' => 'Asus', 'rate' => 45000],
            ['name' => 'Hikvision 2MP Dome Camera', 'category' => 'CCTV Camera', 'brand' => 'Hikvision', 'rate' => 2200],
            ['name' => 'Dahua 4MP Bullet Camera', 'category' => 'CCTV Camera', 'brand' => 'Dahua', 'rate' => 3100],
            ['name' => 'Hikvision 8-Channel NVR', 'category' => 'DVR/NVR', 'brand' => 'Hikvision', 'rate' => 9500],
            ['name' => 'Epson L3210 Printer', 'category' => 'Printer', 'brand' => 'Epson', 'rate' => 14500],
            ['name' => 'TP-Link 8-Port Switch', 'category' => 'Networking', 'brand' => 'TP-Link', 'rate' => 1800],
            ['name' => 'Samsung 24" LED Monitor', 'category' => 'Monitor', 'brand' => 'Samsung', 'rate' => 11500],
            ['name' => 'Logitech Wireless Mouse M185', 'category' => 'Accessories', 'brand' => 'Logitech', 'rate' => 850],
        ];

        return collect($products)->map(function (array $product, int $index) use ($categories, $brands, $colors, $units) {
            $code = 'PRD-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT);

            return Product::updateOrCreate(
                ['product_code' => $code],
                [
                    'name' => $product['name'],
                    'category_id' => $categories->firstWhere('name', $product['category'])->id,
                    'brand_id' => $brands->firstWhere('name', $product['brand'])->id,
                    'color_id' => $colors->random()->id,
                    'model' => strtoupper(fake()->bothify('MD-####')),
                    'barcode' => fake()->unique()->ean13(),
                    'unit_id' => $units->firstWhere('name', 'Piece')->id,
                    'vat' => 0,
                    'warranty_days' => fake()->randomElement([180, 365, 730]),
                    'reorder_level' => 5,
                    'purchase_rate' => round($product['rate'] * 0.8, 2),
                    'sale_rate' => $product['rate'],
                    'min_sale_rate' => round($product['rate'] * 0.9, 2),
                    'wholesale_rate' => round($product['rate'] * 0.95, 2),
                    'is_service' => false,
                    'status' => 'active',
                ]
            );
        });
    }

    private function seedCustomers($areas)
    {
        $names = ['Rahim Uddin', 'Karim Hossain', 'Abdul Jabbar', 'Fahim Ahmed', 'Nusrat Jahan', 'Tania Akter', 'Shakil Rana', 'Mahfuzur Rahman', 'Jannatul Ferdous', 'Imran Hossain'];

        return collect($names)->map(function (string $name, int $index) use ($areas) {
            $code = 'CUST-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT);

            return Customer::updateOrCreate(
                ['customer_code' => $code],
                [
                    'mobile' => '01' . fake()->numerify('#########'),
                    'name' => $name,
                    'area_id' => $areas->random()->id,
                    'address' => fake()->streetAddress() . ', Dhaka',
                    'owner_name' => $name,
                    'office_phone' => fake()->optional()->numerify('02########'),
                    'email' => strtolower(str_replace(' ', '.', $name)) . '@example.com',
                    'previous_due' => fake()->randomElement([0, 500, 1200, 2500]),
                    'credit_limit' => fake()->randomElement([0, 5000, 10000]),
                    'customer_type' => fake()->randomElement(['regular', 'wholesale']),
                ]
            );
        });
    }

    private function seedSuppliers()
    {
        $names = ['Karim Electronics', 'Smart Tech Distribution', 'Dhaka CCTV House', 'Computer Source BD', 'Elite IT Solutions', 'Mega Tech Traders', 'Prime Electronics Ltd', 'Digital World BD', 'Network Zone', 'City Computer Hub'];

        return collect($names)->map(function (string $name, int $index) {
            $code = 'SUP-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT);

            return Supplier::updateOrCreate(
                ['supplier_code' => $code],
                [
                    'mobile' => '01' . fake()->numerify('#########'),
                    'serial_number' => 'SN-' . fake()->unique()->numerify('########'),
                    'name' => $name,
                    'owner_name' => fake()->name('male'),
                    'mode' => fake()->randomElement(['cash', 'credit']),
                    'address' => fake()->streetAddress() . ', Dhaka',
                    'email' => strtolower(str_replace(' ', '.', $name)) . '@example.com',
                    'previous_due' => fake()->randomElement([0, 1500, 3000, 8000]),
                ]
            );
        });
    }

    private function seedCompanyProfile(): void
    {
        CompanyProfile::firstOrCreate(['name' => '3G Computers'], [
            'description' => 'Sales and service center for computers, laptops, and CCTV surveillance systems.',
            'branch_name' => 'Main Branch',
            'branch_title' => '3G Computers - Banarupa Branch',
            'invoice_print_type' => 'a4',
            'branch_address' => 'ShopNo# ICR Shopping Plaza (2nd Floor), Banarupa, Rangamati',
            'invoice_header' => 'Thank you for choosing 3G Computers',
            'invoice_footer' => 'Goods once sold cannot be returned without receipt.',
        ]);
    }

    private function seedTransactionAccounts()
    {
        $accounts = [
            ['name' => 'Shop Rent', 'type' => 'expense'],
            ['name' => 'Electricity Bill', 'type' => 'expense'],
            ['name' => 'Internet Bill', 'type' => 'expense'],
            ['name' => 'Staff Salary', 'type' => 'expense'],
            ['name' => 'Office Supplies', 'type' => 'expense'],
            ['name' => 'Transport Cost', 'type' => 'expense'],
            ['name' => 'Sales Income', 'type' => 'income'],
            ['name' => 'Service Income', 'type' => 'income'],
            ['name' => 'Repair Income', 'type' => 'income'],
            ['name' => 'Miscellaneous', 'type' => 'expense'],
        ];

        return collect($accounts)->map(fn ($account) => TransactionAccount::firstOrCreate(['name' => $account['name']], $account));
    }

    private function seedBankAccounts()
    {
        $banks = ['Dutch-Bangla Bank', 'BRAC Bank', 'City Bank', 'Islami Bank', 'Eastern Bank', 'Prime Bank', 'Mutual Trust Bank', 'Standard Chartered', 'Sonali Bank', 'bKash Business'];

        return collect($banks)->map(function (string $bank, int $index) {
            return BankAccount::firstOrCreate(
                ['account_name' => $bank . ' - Main'],
                [
                    'account_no' => fake()->numerify('##########'),
                    'account_type' => fake()->randomElement(['current', 'savings']),
                    'bank_name' => $bank,
                    'branch_name' => 'Dhanmondi Branch',
                    'initial_balance' => fake()->numberBetween(10000, 500000),
                    'description' => "Primary {$bank} account for business transactions.",
                ]
            );
        });
    }

    private function seedLoanAccounts()
    {
        return collect(range(1, self::COUNT))->map(function (int $i) {
            return LoanAccount::firstOrCreate(
                ['account_name' => "Business Loan #{$i}"],
                [
                    'account_no' => fake()->numerify('LN-######'),
                    'account_type' => fake()->randomElement(['term loan', 'overdraft']),
                    'bank_name' => fake()->randomElement(['Dutch-Bangla Bank', 'BRAC Bank', 'City Bank']),
                    'branch_name' => 'Dhanmondi Branch',
                    'initial_balance' => fake()->numberBetween(50000, 1000000),
                    'description' => 'Loan facility for business expansion.',
                ]
            );
        });
    }

    private function seedInvestmentAccounts()
    {
        return collect(range(1, self::COUNT))->map(function (int $i) {
            return InvestmentAccount::firstOrCreate(
                ['account_name' => "Partner Investment #{$i}"],
                [
                    'account_no' => fake()->numerify('INV-######'),
                    'account_type' => 'equity',
                    'bank_name' => fake()->randomElement(['Dutch-Bangla Bank', 'BRAC Bank', 'City Bank']),
                    'branch_name' => 'Dhanmondi Branch',
                    'initial_balance' => fake()->numberBetween(100000, 2000000),
                    'description' => 'Partner capital investment.',
                ]
            );
        });
    }

    private function seedDesignations()
    {
        $names = ['Sales Executive', 'Technician', 'CCTV Installer', 'Accountant', 'Store Manager', 'Delivery Man', 'Customer Support', 'Hardware Engineer', 'Network Engineer', 'Branch Manager'];

        return collect($names)->map(fn ($name) => Designation::firstOrCreate(['name' => $name]));
    }

    private function seedDepartments()
    {
        $names = ['Sales', 'Repair & Service', 'CCTV Installation', 'Accounts', 'Inventory', 'Delivery', 'Customer Service', 'IT Support', 'Networking', 'Administration'];

        return collect($names)->map(fn ($name) => Department::firstOrCreate(['name' => $name]));
    }

    private function seedMonths()
    {
        $names = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October'];

        return collect($names)->map(fn ($name) => Month::firstOrCreate(['name' => $name]));
    }

    private function seedEmployees($designations, $departments)
    {
        $names = ['Jahangir Alam', 'Mizanur Rahman', 'Sohel Rana', 'Nasima Begum', 'Rafiqul Islam', 'Shirin Akter', 'Kamal Hossain', 'Delwar Hossain', 'Farida Yasmin', 'Anisur Rahman'];

        return collect($names)->map(function (string $name, int $index) use ($designations, $departments) {
            $code = 'EMP-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT);

            return Employee::updateOrCreate(
                ['employee_code' => $code],
                [
                    'name' => $name,
                    'designation_id' => $designations->random()->id,
                    'department_id' => $departments->random()->id,
                    'join_date' => fake()->dateTimeBetween('-3 years', '-1 month')->format('Y-m-d'),
                    'salary_range' => fake()->randomElement([15000, 18000, 22000, 28000, 35000]),
                    'status' => 'active',
                    'present_address' => fake()->streetAddress() . ', Dhaka',
                    'permanent_address' => fake()->streetAddress() . ', Bangladesh',
                    'contact_no' => '01' . fake()->numerify('#########'),
                    'email' => strtolower(str_replace(' ', '.', $name)) . '@akcomputer.com',
                    'father_name' => fake()->name('male'),
                    'mother_name' => fake()->name('female'),
                    'gender' => fake()->randomElement(['male', 'female']),
                    'date_of_birth' => fake()->dateTimeBetween('-45 years', '-20 years')->format('Y-m-d'),
                    'marital_status' => fake()->randomElement(['single', 'married']),
                ]
            );
        });
    }

    private function seedRepairCompanies()
    {
        $names = ['Dell Service Center', 'HP Authorized Service', 'Asus Service Point', 'Lenovo Care', 'Hikvision Support', 'Local Hardware Lab', 'Motherboard Clinic BD', 'Laptop Doctor', 'Screen Repair Zone', 'Power Supply Experts'];

        return collect($names)->map(fn ($name) => RepairCompany::firstOrCreate(['name' => $name], ['description' => "Authorized repair partner: {$name}."]));
    }

    private function seedSales(User $user, $products)
    {
        return collect(range(1, self::COUNT))->map(function (int $i) use ($user, $products) {
            $invoiceNo = str_pad((string) (260100010 + $i), 9, '0', STR_PAD_LEFT);
            $product = $products->random();
            $quantity = fake()->numberBetween(1, 3);
            $rate = (float) $product->sale_rate;
            $subtotal = round($quantity * $rate, 2);
            $vat = 0;
            $discount = round($subtotal * 0.02, 2);
            $total = round($subtotal + $vat - $discount, 2);
            $paid = fake()->randomElement([$total, round($total * 0.5, 2), 0]);

            $sale = Sale::create([
                'user_id' => $user->id,
                'invoice_no' => $invoiceNo,
                'sale_type' => fake()->randomElement(['retail', 'wholesale']),
                'customer_name' => fake()->name(),
                'customer_mobile' => '01' . fake()->numerify('#########'),
                'customer_address' => fake()->streetAddress() . ', Dhaka',
                'sale_date' => $this->thisMonthDate(),
                'subtotal' => $subtotal, 'vat' => $vat, 'discount' => $discount,
                'transport_cost' => 0, 'total' => $total, 'paid' => $paid, 'due' => max($total - $paid, 0),
            ]);

            $sale->items()->create([
                'product_code' => $product->product_code, 'product_name' => $product->name,
                'warranty_days' => $product->warranty_days, 'quantity' => $quantity, 'rate' => $rate,
                'discount_percent' => 2, 'discount_amount' => $discount, 'subtotal' => $subtotal, 'total' => $total,
            ]);

            return $sale;
        });
    }

    private function seedPurchases(User $user, $suppliers, $products)
    {
        return collect(range(1, self::COUNT))->map(function (int $i) use ($user, $suppliers, $products) {
            $supplier = $suppliers->random();
            $product = $products->random();
            $quantity = fake()->numberBetween(2, 10);
            $rate = (float) $product->purchase_rate;
            $subtotal = round($quantity * $rate, 2);
            $total = $subtotal;
            $paid = fake()->randomElement([$total, round($total * 0.6, 2), 0]);

            $purchase = Purchase::create([
                'user_id' => $user->id, 'supplier_id' => $supplier->id,
                'invoice_no' => 'PUR-' . str_pad((string) (1000 + $i), 6, '0', STR_PAD_LEFT),
                'supplier_name' => $supplier->name, 'supplier_mobile' => $supplier->mobile,
                'supplier_address' => $supplier->address,
                'purchase_date' => $this->thisMonthDate(),
                'subtotal' => $subtotal, 'vat' => 0, 'discount' => 0,
                'transport_cost' => fake()->randomElement([0, 200, 500]), 'total' => $total, 'paid' => $paid, 'due' => max($total - $paid, 0),
            ]);

            $purchase->items()->create([
                'product_code' => $product->product_code, 'product_name' => $product->name,
                'warranty_days' => $product->warranty_days, 'quantity' => $quantity, 'rate' => $rate, 'total' => $subtotal,
            ]);

            return $purchase;
        });
    }

    private function seedServiceEntries(User $user, $products): void
    {
        $problems = ['Laptop screen replacement', 'CCTV camera not recording', 'PC not booting', 'Motherboard repair', 'DVR configuration', 'Network cabling', 'RAM upgrade', 'Hard disk data recovery', 'Printer head cleaning', 'Power supply replacement'];

        collect(range(1, self::COUNT))->each(function (int $i) use ($user, $products, $problems) {
            $product = $products->random();
            $quantity = 1;
            $rate = fake()->randomElement([500, 800, 1200, 2000, 3500]);
            $subtotal = $quantity * $rate;
            $total = $subtotal;

            $entry = ServiceEntry::create([
                'user_id' => $user->id,
                'invoice_no' => 'SRV-' . str_pad((string) (1000 + $i), 6, '0', STR_PAD_LEFT),
                'service_type' => fake()->randomElement(['repair', 'installation', 'maintenance']),
                'customer_name' => fake()->name(),
                'customer_mobile' => '01' . fake()->numerify('#########'),
                'customer_address' => fake()->streetAddress() . ', Dhaka',
                'technician' => fake()->name('male'),
                'service_date' => $this->thisMonthDate(),
                'subtotal' => $subtotal, 'vat' => 0, 'discount' => 0, 'transport_cost' => 0,
                'total' => $total, 'paid' => $total, 'due' => 0,
            ]);

            $entry->items()->create([
                'service_code' => 'SVC-' . $i, 'service_name' => $problems[$i - 1],
                'device_serial' => strtoupper(fake()->bothify('SN-########')),
                'problem_description' => $problems[$i - 1], 'warranty_days' => 90,
                'quantity' => $quantity, 'rate' => $rate, 'discount_percent' => 0, 'discount_amount' => 0,
                'subtotal' => $subtotal, 'total' => $total,
            ]);
        });
    }

    private function seedSalesReturns(User $user, $sales): void
    {
        $sales->take(self::COUNT)->each(function (Sale $sale, int $i) use ($user) {
            $item = $sale->items()->first();
            $quantity = 1;
            $rate = (float) $item->rate;
            $subtotal = $quantity * $rate;

            $return = SalesReturn::create([
                'user_id' => $user->id, 'sale_id' => $sale->id,
                'return_no' => 'SRET-' . str_pad((string) ($i + 1), 6, '0', STR_PAD_LEFT),
                'invoice_no' => $sale->invoice_no, 'customer_name' => $sale->customer_name,
                'return_date' => fake()->dateTimeBetween($sale->sale_date, 'now')->format('Y-m-d'),
                'reason' => fake()->randomElement(['Defective item', 'Wrong product delivered', 'Customer changed mind', 'Found cheaper elsewhere']),
                'subtotal' => $subtotal, 'total' => $subtotal,
            ]);

            $return->items()->create([
                'product_code' => $item->product_code, 'product_name' => $item->product_name,
                'serial_no' => strtoupper(fake()->bothify('SN-########')),
                'quantity' => $quantity, 'rate' => $rate, 'subtotal' => $subtotal,
                'reason' => 'Defective item',
            ]);
        });
    }

    private function seedPurchaseReturns(User $user, $purchases): void
    {
        $purchases->take(self::COUNT)->each(function (Purchase $purchase, int $i) use ($user) {
            $item = $purchase->items()->first();
            $quantity = 1;
            $rate = (float) $item->rate;
            $subtotal = $quantity * $rate;

            $return = PurchaseReturn::create([
                'user_id' => $user->id, 'purchase_id' => $purchase->id,
                'return_no' => 'PRET-' . str_pad((string) ($i + 1), 6, '0', STR_PAD_LEFT),
                'invoice_no' => $purchase->invoice_no, 'supplier_name' => $purchase->supplier_name,
                'return_date' => fake()->dateTimeBetween($purchase->purchase_date, 'now')->format('Y-m-d'),
                'reason' => fake()->randomElement(['Damaged on arrival', 'Wrong item shipped', 'Overstock']),
                'subtotal' => $subtotal, 'total' => $subtotal,
            ]);

            $return->items()->create([
                'product_code' => $item->product_code, 'product_name' => $item->product_name,
                'serial_no' => strtoupper(fake()->bothify('SN-########')),
                'quantity' => $quantity, 'rate' => $rate, 'subtotal' => $subtotal,
                'reason' => 'Damaged on arrival',
            ]);
        });
    }

    private function seedSerialSalesReturns(User $user, $sales, $products): void
    {
        collect(range(1, self::COUNT))->each(function (int $i) use ($user, $sales, $products) {
            $sale = $sales->random();
            $product = $products->random();

            SerialSalesReturn::create([
                'user_id' => $user->id, 'sale_id' => $sale->id,
                'return_no' => 'SSRET-' . str_pad((string) $i, 6, '0', STR_PAD_LEFT),
                'serial_no' => strtoupper(fake()->bothify('SN-########')),
                'invoice_no' => $sale->invoice_no, 'customer_name' => $sale->customer_name,
                'product_code' => $product->product_code, 'product_name' => $product->name,
                'reason' => fake()->randomElement(['Manufacturing defect', 'Dead on arrival', 'Not as described']),
                'return_date' => $this->thisMonthDate(),
                'quantity' => 1, 'rate' => $product->sale_rate, 'total' => $product->sale_rate,
            ]);
        });
    }

    private function seedSerialPurchaseReturns(User $user, $purchases, $products): void
    {
        collect(range(1, self::COUNT))->each(function (int $i) use ($user, $purchases, $products) {
            $purchase = $purchases->random();
            $product = $products->random();

            SerialPurchaseReturn::create([
                'user_id' => $user->id, 'purchase_id' => $purchase->id,
                'return_no' => 'SPRET-' . str_pad((string) $i, 6, '0', STR_PAD_LEFT),
                'serial_no' => strtoupper(fake()->bothify('SN-########')),
                'invoice_no' => $purchase->invoice_no, 'supplier_name' => $purchase->supplier_name,
                'product_code' => $product->product_code, 'product_name' => $product->name,
                'reason' => fake()->randomElement(['Faulty unit', 'Wrong serial received']),
                'return_date' => $this->thisMonthDate(),
                'quantity' => 1, 'rate' => $product->purchase_rate, 'total' => $product->purchase_rate,
            ]);
        });
    }

    private function seedRepairs(User $user, $customers, $employees, $repairCompanies, $suppliers): void
    {
        $problems = ['Laptop not turning on', 'CCTV camera night vision not working', 'PC overheating', 'Keyboard not responding', 'Blue screen error', 'Hard disk failure', 'Charging port broken', 'Display flickering', 'No internet connectivity', 'Battery not charging'];
        $brands = ['Dell', 'HP', 'Asus', 'Lenovo', 'Hikvision', 'Dahua'];

        collect(range(1, self::COUNT))->each(function (int $i) use ($user, $customers, $employees, $repairCompanies, $suppliers, $problems, $brands) {
            $customer = $customers->random();
            $total = fake()->randomElement([800, 1500, 2500, 4000, 6000]);
            $paid = fake()->randomElement([$total, round($total * 0.5), 0]);

            Repair::create([
                'user_id' => $user->id,
                'invoice_no' => 'REP-' . str_pad((string) $i, 6, '0', STR_PAD_LEFT),
                'repair_date' => $this->thisMonthDate(),
                'customer_id' => $customer->id, 'customer_name' => $customer->name, 'customer_mobile' => $customer->mobile,
                'customer_address' => $customer->address,
                'assigned_employee_id' => $employees->random()->id,
                'expected_delivery_date' => fake()->dateTimeBetween('now', '+1 week')->format('Y-m-d'),
                'status' => fake()->randomElement(['pending', 'in-progress', 'completed', 'delivered']),
                'repair_company_id' => $repairCompanies->random()->id,
                'brand' => fake()->randomElement($brands), 'model' => strtoupper(fake()->bothify('MD-####')),
                'serial_no' => strtoupper(fake()->bothify('SN-########')),
                'problem' => $problems[$i - 1],
                'warranty_period' => fake()->boolean(30), 'extra_received' => fake()->boolean(20),
                'parts_name' => fake()->randomElement(['RAM module', 'SMPS', 'Display panel', 'Hard disk', 'Motherboard', null]),
                'parts_supplier_id' => $suppliers->random()->id,
                'parts_purchase_date' => $this->thisMonthDate(),
                'parts_purchase_rate' => fake()->randomElement([500, 1200, 2000]),
                'parts_quantity' => 1, 'parts_purchase_amount' => fake()->randomElement([500, 1200, 2000]),
                'parts_warranty_days' => 90,
                'subtotal' => $total, 'discount' => 0, 'total' => $total,
                'payment_method' => fake()->randomElement(['cash', 'bank', 'mobile banking']),
                'paid' => $paid, 'due' => max($total - $paid, 0),
            ]);
        });
    }

    private function seedWarranties(User $user, $customers, $employees, $suppliers): void
    {
        $products = ['Dell Vostro 3510 Laptop', 'HP Pavilion 15 Laptop', 'Hikvision 2MP Dome Camera', 'Dahua 4MP Bullet Camera', 'Samsung 24" LED Monitor', 'Epson L3210 Printer', 'TP-Link 8-Port Switch', 'Asus Desktop Core i5 PC', 'Hikvision 8-Channel NVR', 'Logitech Wireless Mouse M185'];

        collect(range(1, self::COUNT))->each(function (int $i) use ($user, $customers, $employees, $suppliers, $products) {
            $customer = $customers->random();

            Warranty::create([
                'user_id' => $user->id,
                'invoice_no' => 'WAR-' . str_pad((string) $i, 6, '0', STR_PAD_LEFT),
                'warranty_date' => $this->thisMonthDate(),
                'customer_id' => $customer->id, 'customer_name' => $customer->name, 'customer_mobile' => $customer->mobile,
                'received_by_employee_id' => $employees->random()->id,
                'warranty_status' => fake()->randomElement(['pending', 'in-progress', 'resolved']),
                'estimated_delivery_date' => fake()->dateTimeBetween('now', '+2 weeks')->format('Y-m-d'),
                'claim_status' => fake()->randomElement(['approved', 'pending', 'rejected']),
                'product_name' => $products[$i - 1],
                'sale_out_date' => fake()->dateTimeBetween('-1 year', '-1 month')->format('Y-m-d'),
                'serial_no' => strtoupper(fake()->bothify('SN-########')),
                'quantity' => 1,
                'problem' => fake()->randomElement(['Not powering on', 'Overheating', 'Image distortion', 'No display output', 'Random restarts']),
                'condition' => fake()->randomElement(['good', 'minor scratches', 'damaged casing']),
                'note' => 'Handled under manufacturer warranty policy.',
                'warranty_validity' => true,
                'supplier_id' => $suppliers->random()->id,
                'transfer_by_employee_id' => $employees->random()->id,
                'transfer_date' => $this->thisMonthDate(),
                'receive_by_sr' => fake()->name('male'),
                'return_received_by_employee_id' => $employees->random()->id,
                'return_condition' => 'good',
                'received_date' => $this->thisMonthDate(),
                'delivered_by_employee_id' => $employees->random()->id,
                'remarks' => 'Replacement unit delivered to customer.',
                'delivered_date' => fake()->dateTimeBetween('now', '+1 week')->format('Y-m-d'),
            ]);
        });
    }

    private function seedDamages(User $user, $products): void
    {
        collect(range(1, self::COUNT))->each(function (int $i) use ($user, $products) {
            $product = $products->random();
            $quantity = fake()->numberBetween(1, 3);
            $rate = (float) $product->purchase_rate;

            Damage::create([
                'user_id' => $user->id,
                'invoice_no' => 'DMG-' . str_pad((string) $i, 6, '0', STR_PAD_LEFT),
                'damage_date' => $this->thisMonthDate(),
                'product_id' => $product->id, 'product_name' => $product->name,
                'quantity' => $quantity, 'rate' => $rate, 'amount' => round($quantity * $rate, 2),
                'description' => fake()->randomElement(['Damaged during transport', 'Dropped in warehouse', 'Water damage', 'Manufacturing defect found before sale']),
            ]);
        });
    }

    private function seedQuotations(User $user, $products): void
    {
        collect(range(1, self::COUNT))->each(function (int $i) use ($user, $products) {
            $product = $products->random();
            $quantity = fake()->numberBetween(1, 5);
            $rate = (float) $product->sale_rate;
            $subtotal = round($quantity * $rate, 2);

            $quotation = Quotation::create([
                'user_id' => $user->id,
                'quotation_no' => 'QUO-' . str_pad((string) $i, 6, '0', STR_PAD_LEFT),
                'customer_name' => fake()->name(), 'customer_mobile' => '01' . fake()->numerify('#########'),
                'customer_address' => fake()->streetAddress() . ', Dhaka',
                'quotation_date' => $this->thisMonthDate(),
                'valid_until' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
                'subtotal' => $subtotal, 'vat' => 0, 'discount' => 0, 'transport_cost' => 0,
                'total' => $subtotal, 'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            ]);

            $quotation->items()->create([
                'product_code' => $product->product_code, 'product_name' => $product->name,
                'quantity' => $quantity, 'rate' => $rate, 'discount_percent' => 0, 'discount_amount' => 0,
                'subtotal' => $subtotal, 'total' => $subtotal,
            ]);
        });
    }

    private function seedAssets(User $user): void
    {
        $assets = ['Office AC Unit', 'CCTV Installation Van', 'Soldering Station', 'Diagnostic Toolkit', 'Office Furniture', 'Billing Computer', 'Generator', 'Security Alarm System', 'Display Rack', 'Signboard'];

        collect($assets)->each(function (string $name, int $index) use ($user) {
            $rate = fake()->numberBetween(3000, 80000);

            Asset::create([
                'user_id' => $user->id, 'type' => fake()->randomElement(['purchase', 'rent']),
                'name' => $name, 'party_name' => fake()->company(),
                'rate' => $rate, 'quantity' => 1, 'amount' => $rate,
                'note' => "Asset #{$index} for shop operations.",
            ]);
        });
    }

    private function seedCheques(User $user, $customers): void
    {
        collect(range(1, self::COUNT))->each(function (int $i) use ($user, $customers) {
            $issueDate = fake()->dateTimeBetween(now()->startOfMonth(), now());

            Cheque::create([
                'user_id' => $user->id, 'customer_id' => $customers->random()->id,
                'bank_name' => fake()->randomElement(['Dutch-Bangla Bank', 'BRAC Bank', 'City Bank', 'Islami Bank']),
                'branch_name' => 'Dhanmondi Branch', 'cheque_no' => fake()->unique()->numerify('CHQ-######'),
                'amount' => fake()->numberBetween(2000, 50000),
                'status' => fake()->randomElement(['pending', 'cleared', 'bounced']),
                'issue_date' => $issueDate->format('Y-m-d'),
                'cheque_date' => fake()->dateTimeBetween($issueDate, '+1 month')->format('Y-m-d'),
                'reminder_date' => fake()->dateTimeBetween('now', '+1 week')->format('Y-m-d'),
                'submit_date' => null,
                'description' => "Customer cheque payment #{$i}.",
            ]);
        });
    }

    private function seedCashTransactions(User $user, $transactionAccounts): void
    {
        collect(range(1, self::COUNT))->each(function (int $i) use ($user, $transactionAccounts) {
            CashTransaction::create([
                'user_id' => $user->id, 'transaction_account_id' => $transactionAccounts->random()->id,
                'type' => fake()->randomElement(['in', 'out']),
                'transaction_date' => $this->thisMonthDate(),
                'description' => "Cash transaction #{$i}.",
                'amount' => fake()->numberBetween(500, 20000),
            ]);
        });
    }

    private function seedBankTransactions(User $user, $bankAccounts): void
    {
        collect(range(1, self::COUNT))->each(function (int $i) use ($user, $bankAccounts) {
            BankTransaction::create([
                'user_id' => $user->id, 'bank_account_id' => $bankAccounts->random()->id,
                'type' => fake()->randomElement(['deposit', 'withdraw']),
                'transaction_date' => $this->thisMonthDate(),
                'amount' => fake()->numberBetween(1000, 100000),
                'note' => "Bank transaction #{$i}.",
            ]);
        });
    }

    private function seedCustomerPayments(User $user, $customers, $bankAccounts): void
    {
        collect(range(1, self::COUNT))->each(function (int $i) use ($user, $customers, $bankAccounts) {
            CustomerPayment::create([
                'user_id' => $user->id, 'customer_id' => $customers->random()->id,
                'transaction_type' => 'in', 'payment_type' => fake()->randomElement(['cash', 'bank', 'mobile banking']),
                'bank_account_id' => $bankAccounts->random()->id,
                'payment_date' => $this->thisMonthDate(),
                'description' => "Customer due payment #{$i}.",
                'amount' => fake()->numberBetween(500, 15000),
                'discount' => 0, 'discount_percent' => 0,
            ]);
        });
    }

    private function seedSupplierPayments(User $user, $suppliers, $bankAccounts): void
    {
        collect(range(1, self::COUNT))->each(function (int $i) use ($user, $suppliers, $bankAccounts) {
            SupplierPayment::create([
                'user_id' => $user->id, 'supplier_id' => $suppliers->random()->id,
                'transaction_type' => 'out', 'payment_type' => fake()->randomElement(['cash', 'bank', 'mobile banking']),
                'bank_account_id' => $bankAccounts->random()->id,
                'payment_date' => $this->thisMonthDate(),
                'description' => "Supplier due payment #{$i}.",
                'amount' => fake()->numberBetween(1000, 20000),
                'discount' => 0, 'discount_percent' => 0,
            ]);
        });
    }

    private function seedCashTransfers(User $user, $bankAccounts): void
    {
        collect(range(1, self::COUNT))->each(function (int $i) use ($user, $bankAccounts) {
            [$from, $to] = [$bankAccounts->random(), $bankAccounts->random()];

            CashTransfer::create([
                'user_id' => $user->id,
                'from_bank_account_id' => $from->id, 'to_bank_account_id' => $to->id,
                'transfer_date' => $this->thisMonthDate(),
                'amount' => fake()->numberBetween(5000, 50000),
                'note' => "Internal fund transfer #{$i}.",
            ]);
        });
    }

    private function seedLoanTransactions(User $user, $loanAccounts, $bankAccounts): void
    {
        collect(range(1, self::COUNT))->each(function (int $i) use ($user, $loanAccounts, $bankAccounts) {
            LoanTransaction::create([
                'user_id' => $user->id, 'loan_account_id' => $loanAccounts->random()->id,
                'transaction_type' => fake()->randomElement(['disbursement', 'repayment']),
                'payment_type' => fake()->randomElement(['cash', 'bank']),
                'bank_account_id' => $bankAccounts->random()->id,
                'transaction_date' => $this->thisMonthDate(),
                'amount' => fake()->numberBetween(5000, 100000),
                'note' => "Loan transaction #{$i}.",
            ]);
        });
    }

    private function seedInvestmentTransactions(User $user, $investmentAccounts, $bankAccounts): void
    {
        collect(range(1, self::COUNT))->each(function (int $i) use ($user, $investmentAccounts, $bankAccounts) {
            InvestmentTransaction::create([
                'user_id' => $user->id, 'investment_account_id' => $investmentAccounts->random()->id,
                'transaction_type' => fake()->randomElement(['deposit', 'withdrawal']),
                'payment_type' => fake()->randomElement(['cash', 'bank']),
                'bank_account_id' => $bankAccounts->random()->id,
                'transaction_date' => $this->thisMonthDate(),
                'amount' => fake()->numberBetween(10000, 150000),
                'note' => "Investment transaction #{$i}.",
            ]);
        });
    }

    private function seedSalariesAndPayments(User $user, $employees, $months, $bankAccounts): void
    {
        $employees->take(self::COUNT)->values()->each(function (Employee $employee, int $index) use ($user, $months, $bankAccounts) {
            $month = $months[$index % $months->count()];
            $amount = (float) $employee->salary_range;
            $paid = round($amount * fake()->randomElement([1, 0.5]), 2);

            $salary = Salary::updateOrCreate(
                ['employee_id' => $employee->id, 'month_id' => $month->id],
                [
                    'amount' => $amount, 'paid' => $paid, 'due' => max($amount - $paid, 0),
                    'generated_date' => $this->thisMonthDate(),
                ]
            );

            if ($paid > 0) {
                SalaryPayment::create([
                    'user_id' => $user->id, 'salary_id' => $salary->id,
                    'payment_method' => fake()->randomElement(['cash', 'bank']),
                    'bank_account_id' => $bankAccounts->random()->id,
                    'payment_date' => $salary->generated_date,
                    'amount' => $paid, 'note' => "Salary payment for {$employee->name}.",
                ]);
            }
        });
    }
}
