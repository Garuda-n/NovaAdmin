<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Counter;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Role;
use App\Models\StockInward;
use App\Models\StockInwardItem;
use App\Models\StockItem;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AllocatedItemHistoryReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->seed(\Database\Seeders\MenuSeeder::class);
    }

    public function test_allocated_item_history_page_can_be_rendered_for_authorized_user()
    {
        $role = Role::firstOrCreate(['name' => 'Admin', 'slug' => 'admin']);
        $role->permissions()->sync(Permission::all());
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user)->get(route('reports.allocated-item-history.index'));

        $response->assertStatus(200);
        $response->assertSee('Allocated Item History');
        $response->assertSee('Search Allocated Item History');
    }

    public function test_allocated_item_history_returns_no_history_found_for_invalid_code()
    {
        $role = Role::firstOrCreate(['name' => 'Admin', 'slug' => 'admin']);
        $role->permissions()->sync(Permission::all());
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user)->postJson(route('reports.allocated-item-history.search'), [
            'item_code' => 'INVALID_CODE_999',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('historyData.found', false);
        $response->assertSee('No history found for Item Code');
    }

    public function test_allocated_item_history_returns_timeline_events_for_valid_allocated_item()
    {
        $role = Role::firstOrCreate(['name' => 'Admin', 'slug' => 'admin']);
        $role->permissions()->sync(Permission::all());
        $user = User::factory()->create(['role_id' => $role->id]);

        $country = \App\Models\Country::create(['name' => 'India', 'code' => 'IN', 'status' => 1]);
        $state = \App\Models\State::create(['country_id' => $country->id, 'name' => 'Maharashtra', 'code' => 'MH', 'status' => 1]);
        $city = \App\Models\City::create(['state_id' => $state->id, 'name' => 'Mumbai', 'status' => 1]);
        $category = \App\Models\Category::create(['name' => 'Apparel', 'code' => 'APP01', 'status' => 1]);
        $brand = \App\Models\Brand::create(['name' => 'Nike', 'code' => 'NKE01', 'status' => 1]);
        $uom = \App\Models\Uom::create(['name' => 'Pcs', 'shortcode' => 'PCS01', 'status' => 1]);

        $company = Company::create(['name' => 'Test Company', 'code' => 'COMP1', 'status' => 1]);
        $branch = Branch::create(['company_id' => $company->id, 'name' => 'Main Branch', 'branch_code' => 'BR001', 'status' => 1]);
        $counter = Counter::create(['branch_id' => $branch->id, 'counter_name' => 'Counter 1', 'counter_code' => 'C001', 'status' => 1]);
        $supplier = Supplier::create([
            'supplier_name' => 'Test Supplier',
            'supplier_code' => 'SUP001',
            'mobile' => '9876543210',
            'supplier_type' => 'Manufacturer',
            'country_id' => $country->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
            'pincode' => '400001',
            'status' => 1,
        ]);
        $product = Product::create([
            'code' => 'PROD1',
            'name' => 'Gold Item',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'uom_id' => $uom->id,
            'tracking_type' => Product::TRACKING_INDIVIDUAL,
            'status' => 1,
        ]);

        $stockInward = StockInward::create([
            'company_id' => $company->id,
            'branch_id' => $branch->id,
            'counter_id' => $counter->id,
            'supplier_id' => $supplier->id,
            'invoice_no' => 'INV-TEST-001',
            'invoice_date' => now()->toDateString(),
            'status' => 1,
            'created_by' => $user->id,
        ]);

        $stockInwardItem = StockInwardItem::create([
            'stock_inward_id' => $stockInward->id,
            'product_id' => $product->id,
            'qty' => 1,
            'purchase_price' => 100.00,
            'selling_price' => 150.00,
        ]);

        $stockItem = StockItem::create([
            'stock_inward_id' => $stockInward->id,
            'stock_inward_item_id' => $stockInwardItem->id,
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'counter_id' => $counter->id,
            'item_code' => 'TESTITEM001',
            'status' => 1,
            'allocated_by' => $user->id,
            'allocated_at' => now(),
        ]);

        $response = $this->actingAs($user)->postJson(route('reports.allocated-item-history.search'), [
            'item_code' => 'TESTITEM001',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('historyData.found', true);
        $response->assertJsonPath('historyData.item_code', 'TESTITEM001');
        $response->assertSee('Lifecycle History Timeline');
        $response->assertSee('Stock Inward');
        $response->assertSee('Counter Allocation');
    }
}
