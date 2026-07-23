<?php

use App\Models\Branch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\City;
use App\Models\Company;
use App\Models\Country;
use App\Models\Counter;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Role;
use App\Models\State;
use App\Models\StockInward;
use App\Models\StockInwardItem;
use App\Models\Supplier;
use App\Models\Uom;
use App\Models\User;
use App\Services\Inventory\ItemAllocationService;

function setupInwardTestUser() {
    $role = Role::create(['name' => 'Admin_' . uniqid()]);

    $permissions = [
        ['name' => 'View Stock Inward', 'slug' => 'stock-inwards.view', 'group' => 'Inventory'],
        ['name' => 'Create Stock Inward', 'slug' => 'stock-inwards.create', 'group' => 'Inventory'],
        ['name' => 'Edit Stock Inward', 'slug' => 'stock-inwards.edit', 'group' => 'Inventory'],
        ['name' => 'Delete Stock Inward', 'slug' => 'stock-inwards.delete', 'group' => 'Inventory'],
    ];

    foreach ($permissions as $permData) {
        $permission = Permission::firstOrCreate(['slug' => $permData['slug']], $permData);
        if (!$role->permissions()->where('permission_id', $permission->id)->exists()) {
            $role->permissions()->attach($permission->id);
        }
    }

    return User::factory()->create([
        'role_id' => $role->id,
        'status' => 1,
    ]);
}

function createInwardTestData() {
    $country = Country::create(['name' => 'India', 'code' => 'IN', 'status' => 1]);
    $state = State::create(['country_id' => $country->id, 'name' => 'Maharashtra', 'code' => 'MH', 'status' => 1]);
    $city = City::create(['state_id' => $state->id, 'name' => 'Mumbai', 'status' => 1]);

    $supplier = Supplier::create([
        'supplier_name' => 'Test Supplier',
        'supplier_code' => 'SUP_' . uniqid(),
        'mobile' => '9876543210',
        'supplier_type' => 'Manufacturer',
        'country_id' => $country->id,
        'state_id' => $state->id,
        'city_id' => $city->id,
        'pincode' => '400001',
        'status' => 1,
    ]);

    $company = Company::create(['name' => 'Test Company', 'code' => 'TC_' . uniqid(), 'status' => 1]);
    $branch = Branch::create(['company_id' => $company->id, 'branch_code' => 'B01_' . uniqid(), 'name' => 'Main Branch', 'status' => 1]);
    $counter = Counter::create(['counter_name' => 'Counter 1', 'counter_code' => 'C01_' . uniqid(), 'status' => 1]);

    $category = Category::create(['name' => 'Apparel', 'code' => 'APP_' . uniqid(), 'status' => 1]);
    $brand = Brand::create(['name' => 'Nike', 'code' => 'NKE_' . uniqid(), 'status' => 1]);
    $uom = Uom::create(['name' => 'Pcs', 'shortcode' => 'PCS_' . uniqid(), 'status' => 1]);

    $product = Product::create([
        'code' => 'RING',
        'name' => 'Gold Ring',
        'category_id' => $category->id,
        'brand_id' => $brand->id,
        'uom_id' => $uom->id,
        'tracking_type' => Product::TRACKING_INDIVIDUAL,
        'item_generation_mode' => Product::ITEM_GEN_BULK,
        'status' => 1,
    ]);

    $stockInward = StockInward::create([
        'company_id' => $company->id,
        'branch_id' => $branch->id,
        'counter_id' => $counter->id,
        'supplier_id' => $supplier->id,
        'invoice_no' => 'INV-TEST-001',
        'invoice_date' => now(),
        'status' => 1,
    ]);

    $stockInwardItem = StockInwardItem::create([
        'stock_inward_id' => $stockInward->id,
        'product_id' => $product->id,
        'qty' => 10,
    ]);

    return compact('company', 'branch', 'counter', 'supplier', 'product', 'stockInward', 'stockInwardItem');
}

test('bulk inward is unlocked before allocation and locked after allocation', function () {
    $data = createInwardTestData();
    /** @var StockInward $stockInward */
    $stockInward = $data['stockInward'];
    /** @var StockInwardItem $stockInwardItem */
    $stockInwardItem = $data['stockInwardItem'];

    expect($stockInward->hasAllocatedItems())->toBeFalse();
    expect($stockInward->isLocked())->toBeFalse();

    // Allocate 1 item
    $service = new ItemAllocationService();
    $service->allocateItems([
        'stock_inward_item_id' => $stockInwardItem->id,
        'counter_id' => $data['counter']->id,
        'quantity' => 1,
    ]);

    $stockInward->refresh();
    expect($stockInward->hasAllocatedItems())->toBeTrue();
    expect($stockInward->isLocked())->toBeTrue();
});

test('prevents loading edit page for locked bulk inward', function () {
    $user = setupInwardTestUser();
    $data = createInwardTestData();
    $stockInward = $data['stockInward'];
    $stockInwardItem = $data['stockInwardItem'];

    // Before allocation, edit page loads successfully
    $response = $this->actingAs($user)->get(route('stock-inwards.edit', $stockInward));
    $response->assertStatus(200);

    // Perform allocation
    $service = new ItemAllocationService();
    $service->allocateItems([
        'stock_inward_item_id' => $stockInwardItem->id,
        'counter_id' => $data['counter']->id,
        'quantity' => 1,
    ]);

    // After allocation, edit page redirects with error
    $response = $this->actingAs($user)->get(route('stock-inwards.edit', $stockInward));
    $response->assertRedirect(route('stock-inwards.index'));
    $response->assertSessionHas('error', 'Bulk Inward cannot be edited because item allocation has already started.');
});

test('prevents updating locked bulk inward transaction', function () {
    $user = setupInwardTestUser();
    $data = createInwardTestData();
    $stockInward = $data['stockInward'];
    $stockInwardItem = $data['stockInwardItem'];

    $updatePayload = [
        'company_id' => $data['company']->id,
        'branch_id' => $data['branch']->id,
        'counter_id' => $data['counter']->id,
        'supplier_id' => $data['supplier']->id,
        'invoice_no' => 'INV-TEST-001-MODIFIED',
        'invoice_date' => now()->format('Y-m-d'),
        'items' => [
            [
                'product_id' => $data['product']->id,
                'qty' => 15,
            ]
        ]
    ];

    // Perform allocation
    $service = new ItemAllocationService();
    $service->allocateItems([
        'stock_inward_item_id' => $stockInwardItem->id,
        'counter_id' => $data['counter']->id,
        'quantity' => 1,
    ]);

    // Direct HTTP PUT request must be rejected
    $response = $this->actingAs($user)->put(route('stock-inwards.update', $stockInward), $updatePayload);
    $response->assertRedirect(route('stock-inwards.index'));
    $response->assertSessionHas('error', 'Bulk Inward cannot be edited because item allocation has already started.');

    // Direct JSON API PUT request must return 422 JSON error
    $jsonResponse = $this->actingAs($user)->putJson(route('stock-inwards.update', $stockInward), $updatePayload);
    $jsonResponse->assertStatus(422);
    $jsonResponse->assertJson(['message' => 'Bulk Inward cannot be edited because item allocation has already started.']);
});

test('prevents deleting locked bulk inward transaction', function () {
    $user = setupInwardTestUser();
    $data = createInwardTestData();
    $stockInward = $data['stockInward'];
    $stockInwardItem = $data['stockInwardItem'];

    // Perform allocation
    $service = new ItemAllocationService();
    $service->allocateItems([
        'stock_inward_item_id' => $stockInwardItem->id,
        'counter_id' => $data['counter']->id,
        'quantity' => 1,
    ]);

    // Direct HTTP DELETE request must be rejected
    $response = $this->actingAs($user)->delete(route('stock-inwards.destroy', $stockInward));
    $response->assertRedirect(route('stock-inwards.index'));
    $response->assertSessionHas('error', 'Bulk Inward cannot be deleted because item allocation has already started.');

    // Direct JSON API DELETE request must return 422 JSON error
    $jsonResponse = $this->actingAs($user)->deleteJson(route('stock-inwards.destroy', $stockInward));
    $jsonResponse->assertStatus(422);
    $jsonResponse->assertJson(['message' => 'Bulk Inward cannot be deleted because item allocation has already started.']);

    // Ensure database record still exists
    $this->assertDatabaseHas('stock_inwards', ['id' => $stockInward->id]);
});

test('renders locked badge and hides edit/delete buttons in index and show views', function () {
    $user = setupInwardTestUser();
    $data = createInwardTestData();
    $stockInward = $data['stockInward'];
    $stockInwardItem = $data['stockInwardItem'];

    // Perform allocation
    $service = new ItemAllocationService();
    $service->allocateItems([
        'stock_inward_item_id' => $stockInwardItem->id,
        'counter_id' => $data['counter']->id,
        'quantity' => 1,
    ]);

    // Check Index view
    $indexResponse = $this->actingAs($user)->get(route('stock-inwards.index'));
    $indexResponse->assertStatus(200);
    $indexResponse->assertSee('Locked');

    // Check Show view
    $showResponse = $this->actingAs($user)->get(route('stock-inwards.show', $stockInward));
    $showResponse->assertStatus(200);
    $showResponse->assertSee('Locked (Allocation Started)');
    $showResponse->assertSee('Bulk Inward cannot be edited because item allocation has already started.');
});
