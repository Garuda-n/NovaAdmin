<?php

use App\Enums\InventoryReferenceType;
use App\Enums\InventoryTransactionType;
use App\Enums\StockItemStatus;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\City;
use App\Models\Company;
use App\Models\Country;
use App\Models\Counter;
use App\Models\Product;
use App\Models\ProductSequence;
use App\Models\State;
use App\Models\StockInward;
use App\Models\StockInwardItem;
use App\Models\StockItem;
use App\Models\StockItemLog;
use App\Models\Supplier;
use App\Models\Uom;
use App\Services\Inventory\ItemAllocationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(Tests\TestCase::class, RefreshDatabase::class);

function createTestSupplier() {
    $country = Country::create(['name' => 'India', 'code' => 'IN', 'status' => 1]);
    $state = State::create(['country_id' => $country->id, 'name' => 'Maharashtra', 'code' => 'MH', 'status' => 1]);
    $city = City::create(['state_id' => $state->id, 'name' => 'Mumbai', 'status' => 1]);

    return Supplier::create([
        'supplier_name' => 'Test Supplier',
        'supplier_code' => 'SUP01',
        'mobile' => '9876543210',
        'supplier_type' => 'Manufacturer',
        'country_id' => $country->id,
        'state_id' => $state->id,
        'city_id' => $city->id,
        'pincode' => '400001',
        'status' => 1,
    ]);
}

function createTestProduct($code = 'SHR', $name = 'Men Shirt', $trackingType = Product::TRACKING_INDIVIDUAL, $genMode = Product::ITEM_GEN_BULK) {
    $category = Category::create(['name' => 'Apparel', 'code' => 'APP_' . uniqid(), 'status' => 1]);
    $brand = Brand::create(['name' => 'Nike', 'code' => 'NKE_' . uniqid(), 'status' => 1]);
    $uom = Uom::create(['name' => 'Pcs', 'shortcode' => 'PCS_' . uniqid(), 'status' => 1]);

    return Product::create([
        'code' => $code,
        'name' => $name,
        'category_id' => $category->id,
        'brand_id' => $brand->id,
        'uom_id' => $uom->id,
        'tracking_type' => $trackingType,
        'item_generation_mode' => $genMode,
        'status' => 1,
    ]);
}

test('allocates bulk stock into individual stock items with correct code sequence and log entries', function () {
    $company = Company::create(['name' => 'Test Company', 'code' => 'TC', 'status' => 1]);
    $branch = Branch::create(['company_id' => $company->id, 'branch_code' => 'B01', 'name' => 'Main Branch', 'status' => 1]);
    $counter = Counter::create(['counter_name' => 'Counter 1', 'counter_code' => 'C01', 'status' => 1]);
    $supplier = createTestSupplier();
    $product = createTestProduct('SHR', 'Men Shirt', Product::TRACKING_INDIVIDUAL, Product::ITEM_GEN_BULK);

    $stockInward = StockInward::create([
        'company_id' => $company->id,
        'branch_id' => $branch->id,
        'supplier_id' => $supplier->id,
        'invoice_no' => 'INV-1001',
        'invoice_date' => now(),
        'status' => 1,
    ]);

    $stockInwardItem = StockInwardItem::create([
        'stock_inward_id' => $stockInward->id,
        'product_id' => $product->id,
        'qty' => 10,
    ]);

    $service = new ItemAllocationService();

    // Allocate 3 items
    $result = $service->allocateItems([
        'stock_inward_item_id' => $stockInwardItem->id,
        'counter_id' => $counter->id,
        'quantity' => 3,
    ]);

    expect($result['allocated_count'])->toBe(3);
    expect($result['remaining_pending'])->toBe(7);
    expect($result['item_codes'])->toEqual(['SHR00001', 'SHR00002', 'SHR00003']);

    // Check product sequence table
    $sequence = ProductSequence::where('product_id', $product->id)->first();
    expect($sequence->next_sequence)->toBe(4);

    // Check stock_items table
    expect(StockItem::count())->toBe(3);
    $firstItem = StockItem::where('item_code', 'SHR00001')->first();
    expect($firstItem->status)->toBe(StockItemStatus::AVAILABLE->value);
    expect($firstItem->counter_id)->toBe($counter->id);

    // Check stock_item_logs table
    expect(StockItemLog::count())->toBe(3);
    $log = StockItemLog::where('stock_item_id', $firstItem->id)->first();
    expect($log->transaction_type)->toBe(InventoryTransactionType::ALLOCATION->value);
    expect($log->reference_type)->toBe(InventoryReferenceType::BULK_INWARD->value);
    expect($log->reference_id)->toBe($stockInward->id);
});

test('rejects allocation when requested quantity exceeds pending quantity', function () {
    $company = Company::create(['name' => 'Test Company', 'code' => 'TC', 'status' => 1]);
    $branch = Branch::create(['company_id' => $company->id, 'branch_code' => 'B01', 'name' => 'Main Branch', 'status' => 1]);
    $counter = Counter::create(['counter_name' => 'Counter 1', 'counter_code' => 'C01', 'status' => 1]);
    $supplier = createTestSupplier();
    $product = createTestProduct('MOB', 'Mobile Phone', Product::TRACKING_INDIVIDUAL, Product::ITEM_GEN_BULK);

    $stockInward = StockInward::create([
        'company_id' => $company->id,
        'branch_id' => $branch->id,
        'supplier_id' => $supplier->id,
        'invoice_no' => 'INV-1002',
        'invoice_date' => now(),
        'status' => 1,
    ]);

    $stockInwardItem = StockInwardItem::create([
        'stock_inward_id' => $stockInward->id,
        'product_id' => $product->id,
        'qty' => 5,
    ]);

    $service = new ItemAllocationService();

    $service->allocateItems([
        'stock_inward_item_id' => $stockInwardItem->id,
        'counter_id' => $counter->id,
        'quantity' => 10,
    ]);
})->throws(ValidationException::class);

test('rejects allocation for products configured with bulk tracking type', function () {
    $company = Company::create(['name' => 'Test Company', 'code' => 'TC', 'status' => 1]);
    $branch = Branch::create(['company_id' => $company->id, 'branch_code' => 'B01', 'name' => 'Main Branch', 'status' => 1]);
    $counter = Counter::create(['counter_name' => 'Counter 1', 'counter_code' => 'C01', 'status' => 1]);
    $supplier = createTestSupplier();
    $product = createTestProduct('SUG', 'Sugar Bag', Product::TRACKING_QUANTITY, Product::ITEM_GEN_BULK);

    $stockInward = StockInward::create([
        'company_id' => $company->id,
        'branch_id' => $branch->id,
        'supplier_id' => $supplier->id,
        'invoice_no' => 'INV-1003',
        'invoice_date' => now(),
        'status' => 1,
    ]);

    $stockInwardItem = StockInwardItem::create([
        'stock_inward_id' => $stockInward->id,
        'product_id' => $product->id,
        'qty' => 100,
    ]);

    $service = new ItemAllocationService();

    $service->allocateItems([
        'stock_inward_item_id' => $stockInwardItem->id,
        'counter_id' => $counter->id,
        'quantity' => 10,
    ]);
})->throws(ValidationException::class);
