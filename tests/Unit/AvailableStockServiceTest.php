<?php

use App\Enums\StockItemStatus;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\City;
use App\Models\Company;
use App\Models\Country;
use App\Models\Counter;
use App\Models\Product;
use App\Models\State;
use App\Models\StockInward;
use App\Models\StockInwardItem;
use App\Models\StockItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Uom;
use App\Models\User;
use App\Services\Inventory\AvailableStockService;
use App\Services\SettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

function createAvailableStockTestData() {
    $user = User::factory()->create(['status' => 1]);
    $company = Company::create(['name' => 'Test Co', 'code' => 'TC', 'status' => 1]);
    $branch = Branch::create(['company_id' => $company->id, 'branch_code' => 'B1', 'name' => 'Branch 1', 'status' => 1]);
    $counter = Counter::create(['counter_name' => 'Counter 1', 'counter_code' => 'C1', 'status' => 1]);
    $category = Category::create(['name' => 'Electronics', 'code' => 'ELEC', 'status' => 1]);
    $brand = Brand::create(['name' => 'Apple', 'code' => 'AAPL', 'status' => 1]);
    $uom = Uom::create(['name' => 'Pcs', 'shortcode' => 'PCS', 'status' => 1]);

    $country = Country::create(['name' => 'India', 'code' => 'IN', 'status' => 1]);
    $state = State::create(['country_id' => $country->id, 'name' => 'Maharashtra', 'code' => 'MH', 'status' => 1]);
    $city = City::create(['state_id' => $state->id, 'name' => 'Mumbai', 'status' => 1]);

    $supplier = Supplier::create([
        'supplier_name' => 'Test Supplier',
        'supplier_code' => 'SUP1',
        'mobile' => '9876543210',
        'supplier_type' => 'Manufacturer',
        'country_id' => $country->id,
        'state_id' => $state->id,
        'city_id' => $city->id,
        'pincode' => '400001',
        'status' => 1,
    ]);

    $indivProduct = Product::create([
        'code' => 'MAC',
        'name' => 'MacBook Pro',
        'category_id' => $category->id,
        'brand_id' => $brand->id,
        'uom_id' => $uom->id,
        'tracking_type' => Product::TRACKING_INDIVIDUAL,
        'item_generation_mode' => Product::ITEM_GEN_BULK,
        'status' => 1,
    ]);

    $bulkProduct = Product::create([
        'code' => 'CBL',
        'name' => 'USB Cable',
        'category_id' => $category->id,
        'brand_id' => $brand->id,
        'uom_id' => $uom->id,
        'tracking_type' => Product::TRACKING_QUANTITY,
        'item_generation_mode' => Product::ITEM_GEN_BULK,
        'status' => 1,
    ]);

    $inward = StockInward::create([
        'company_id' => $company->id,
        'branch_id' => $branch->id,
        'supplier_id' => $supplier->id,
        'invoice_no' => 'INV-101',
        'invoice_date' => now(),
        'status' => 1,
    ]);

    $inwardItem = StockInwardItem::create([
        'stock_inward_id' => $inward->id,
        'product_id' => $indivProduct->id,
        'qty' => 10,
    ]);

    return compact('user', 'company', 'branch', 'counter', 'indivProduct', 'bulkProduct', 'inward', 'inwardItem');
}

test('calculates available quantity for individual and quantity tracked products', function () {
    $data = createAvailableStockTestData();
    $service = new AvailableStockService();

    // Add individual stock item
    StockItem::create([
        'stock_inward_id' => $data['inward']->id,
        'stock_inward_item_id' => $data['inwardItem']->id,
        'product_id' => $data['indivProduct']->id,
        'branch_id' => $data['branch']->id,
        'counter_id' => $data['counter']->id,
        'item_code' => 'MAC00001',
        'status' => StockItemStatus::AVAILABLE->value,
        'allocated_at' => now(),
    ]);

    // Add bulk stock movement
    StockMovement::create([
        'company_id' => $data['company']->id,
        'branch_id' => $data['branch']->id,
        'counter_id' => $data['counter']->id,
        'product_id' => $data['bulkProduct']->id,
        'movement_type' => StockMovement::TYPE_PURCHASE,
        'quantity' => 50,
        'movement_date' => now()->toDateString(),
        'business_date' => now()->toDateString(),
        'created_by' => $data['user']->id,
    ]);

    $indivQty = $service->getAvailableQuantity($data['indivProduct']->id, $data['branch']->id);
    $bulkQty = $service->getAvailableQuantity($data['bulkProduct']->id, $data['branch']->id);

    expect($indivQty)->toBe(1.0);
    expect($bulkQty)->toBe(50.0);
});

test('respects reserve_stock_on_quotation system setting', function () {
    $data = createAvailableStockTestData();
    $service = new AvailableStockService();

    // Create 1 Available and 1 Reserved item
    StockItem::create([
        'stock_inward_id' => $data['inward']->id,
        'stock_inward_item_id' => $data['inwardItem']->id,
        'product_id' => $data['indivProduct']->id,
        'branch_id' => $data['branch']->id,
        'counter_id' => $data['counter']->id,
        'item_code' => 'MAC00001',
        'status' => StockItemStatus::AVAILABLE->value,
        'allocated_at' => now(),
    ]);

    StockItem::create([
        'stock_inward_id' => $data['inward']->id,
        'stock_inward_item_id' => $data['inwardItem']->id,
        'product_id' => $data['indivProduct']->id,
        'branch_id' => $data['branch']->id,
        'counter_id' => $data['counter']->id,
        'item_code' => 'MAC00002',
        'status' => StockItemStatus::RESERVED->value,
        'allocated_at' => now(),
    ]);

    // Setting = false (Default): Reserved items are included as available
    SettingService::set('reserve_stock_on_quotation', 'no');
    $qtySettingNo = $service->getAvailableQuantity($data['indivProduct']->id, $data['branch']->id);
    expect($qtySettingNo)->toBe(2.0);

    // Setting = true: Reserved items are excluded from available
    SettingService::set('reserve_stock_on_quotation', 'yes');
    $qtySettingYes = $service->getAvailableQuantity($data['indivProduct']->id, $data['branch']->id);
    expect($qtySettingYes)->toBe(1.0);
});

test('returns accurate report data and header KPIs', function () {
    $data = createAvailableStockTestData();
    $service = new AvailableStockService();

    StockItem::create([
        'stock_inward_id' => $data['inward']->id,
        'stock_inward_item_id' => $data['inwardItem']->id,
        'product_id' => $data['indivProduct']->id,
        'branch_id' => $data['branch']->id,
        'counter_id' => $data['counter']->id,
        'item_code' => 'MAC00001',
        'status' => StockItemStatus::AVAILABLE->value,
        'allocated_at' => now(),
    ]);

    $report = $service->getReportData(['branch_id' => $data['branch']->id]);

    expect($report['summary']['total_closing'])->toBeGreaterThanOrEqual(1.0);
    expect($report['items']->count())->toBeGreaterThanOrEqual(1);
});
