<?php

namespace Tests\Feature\Calculation;

use App\Models\Branch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\City;
use App\Models\Company;
use App\Models\Country;
use App\Models\Counter;
use App\Models\Customer;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\Role;
use App\Models\State;
use App\Models\Tax;
use App\Models\Uom;
use App\Models\User;
use App\Services\QuotationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesPreviewTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Branch $branch;
    protected Counter $counter;
    protected Customer $customer;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);

        $role = Role::create(['name' => 'Sales Role', 'slug' => 'sales-role']);
        $role->permissions()->sync(Permission::whereIn('slug', ['sales.create', 'quotation.create'])->pluck('id'));
        $this->user = User::factory()->create(['role_id' => $role->id]);

        $country = Country::create(['name' => 'India', 'code' => 'IN', 'status' => 1]);
        $state = State::create(['country_id' => $country->id, 'name' => 'Maharashtra', 'code' => 'MH', 'status' => 1]);
        $city = City::create(['state_id' => $state->id, 'name' => 'Mumbai', 'status' => 1]);
        $company = Company::create(['name' => 'Test Company', 'code' => 'COMP1', 'status' => 1]);
        $this->branch = Branch::create(['company_id' => $company->id, 'name' => 'Head Office', 'branch_name' => 'Head Office', 'branch_code' => 'HO', 'status' => 1]);
        $this->counter = Counter::create(['branch_id' => $this->branch->id, 'counter_name' => 'Counter 1', 'counter_code' => 'C1', 'status' => 1]);
        $category = Category::create(['company_id' => $company->id, 'name' => 'Electronics', 'code' => 'ELEC', 'status' => 1]);
        $brand = Brand::create(['company_id' => $company->id, 'name' => 'Generic', 'code' => 'GEN', 'status' => 1]);
        $uom = Uom::create(['name' => 'Pieces', 'shortcode' => 'PCS', 'status' => 1]);
        $tax = Tax::create(['code' => 'GST18', 'name' => 'GST 18%', 'percentage' => 18, 'status' => 1]);

        $this->customer = Customer::create([
            'company_id' => $company->id,
            'country_id' => $country->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
            'customer_name' => 'John Doe',
            'mobile' => '9876543210',
            'pincode' => '400001',
            'customer_type' => 'B2C',
            'status' => 1,
        ]);

        $this->product = Product::create([
            'company_id' => $company->id,
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'uom_id' => $uom->id,
            'tax_id' => $tax->id,
            'name' => 'Widget',
            'code' => 'WID001',
            'product_code' => 'WID001',
            'tracking_type' => 1,
            'status' => 1,
        ]);

        \App\Models\StockMovement::create([
            'company_id' => $company->id,
            'branch_id' => $this->branch->id,
            'counter_id' => $this->counter->id,
            'product_id' => $this->product->id,
            'movement_type' => \App\Models\StockMovement::TYPE_OPENING,
            'transaction_type' => 'in',
            'quantity' => 1000,
            'movement_date' => now()->toDateString(),
            'business_date' => now()->toDateString(),
            'created_by' => $this->user->id,
        ]);
    }

    protected function createQuotation(float $qty, float $rate): Quotation
    {
        $this->actingAs($this->user);

        return app(QuotationService::class)->store([
            'branch_id' => $this->branch->id,
            'counter_id' => $this->counter->id,
            'customer_id' => $this->customer->id,
            'customer_type' => 'B2C',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'product_name' => $this->product->name,
                    'uom_id' => $this->product->uom_id,
                    'qty' => $qty,
                    'rate' => $rate,
                ],
            ],
        ]);
    }

    public function test_preview_matches_what_conversion_would_store()
    {
        $quotation = $this->createQuotation(1, 120.00);

        $response = $this->actingAs($this->user)->postJson(
            route('sales.calculate', $quotation),
            ['gst_type' => 1, 'invoice_discount' => 2.00]
        );

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('totals.subtotal', 120);
        $response->assertJsonPath('totals.tax_amount', 21.6);
        $response->assertJsonPath('totals.invoice_discount', 2);
        $response->assertJsonPath('totals.round_off', 0.4);
        $response->assertJsonPath('totals.grand_total', 140);
    }

    public function test_preview_defaults_to_cgst_sgst_and_no_discount()
    {
        $quotation = $this->createQuotation(1, 100);

        $response = $this->actingAs($this->user)->postJson(route('sales.calculate', $quotation));

        $response->assertStatus(200);
        $response->assertJsonPath('totals.cgst_amount', 9);
        $response->assertJsonPath('totals.sgst_amount', 9);
        $response->assertJsonPath('totals.grand_total', 118);
    }

    public function test_preview_uses_igst_when_requested()
    {
        $quotation = $this->createQuotation(1, 1000);

        $response = $this->actingAs($this->user)->postJson(route('sales.calculate', $quotation), ['gst_type' => 2]);

        $response->assertStatus(200);
        $response->assertJsonPath('totals.cgst_amount', 0);
        $response->assertJsonPath('totals.sgst_amount', 0);
        $response->assertJsonPath('totals.igst_amount', 180);
        $response->assertJsonPath('totals.grand_total', 1180);
    }

    public function test_preview_is_denied_without_sales_create_permission()
    {
        $role = Role::create(['name' => 'No Sales Role', 'slug' => 'no-sales-role']);
        $role->permissions()->sync(Permission::whereIn('slug', ['quotation.create'])->pluck('id'));
        $otherUser = User::factory()->create(['role_id' => $role->id]);

        $quotation = $this->createQuotation(1, 100);

        $response = $this->actingAs($otherUser)->postJson(route('sales.calculate', $quotation));

        // The `permission` middleware logs the failing user out and redirects to login.
        $response->assertRedirect(route('login'));
    }
}
