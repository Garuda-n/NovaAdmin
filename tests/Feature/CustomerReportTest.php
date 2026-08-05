<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\City;
use App\Models\Company;
use App\Models\Counter;
use App\Models\Country;
use App\Models\Customer;
use App\Models\PaymentMode;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Sale;
use App\Models\State;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected Branch $branch;
    protected Counter $counter;
    protected Customer $customer;
    protected State $state;
    protected City $city;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);

        $this->user = User::factory()->create();
        $country = Country::create(['name' => 'India', 'code' => 'IN', 'status' => 1]);
        $this->state = State::create(['country_id' => $country->id, 'name' => 'Tamil Nadu', 'code' => 'TN', 'status' => 1]);
        $this->city = City::create(['state_id' => $this->state->id, 'name' => 'Coimbatore', 'status' => 1]);
        $this->company = Company::create(['name' => 'Test Company', 'code' => 'COMP1', 'status' => 1]);
        $this->branch = Branch::create(['company_id' => $this->company->id, 'name' => 'Coimbatore Branch', 'branch_name' => 'Coimbatore', 'branch_code' => 'CBE', 'status' => 1]);
        $this->counter = Counter::create(['branch_id' => $this->branch->id, 'counter_name' => 'Counter 1', 'counter_code' => 'C1', 'status' => 1]);

        $this->customer = Customer::create([
            'company_id' => $this->company->id,
            'country_id' => $country->id,
            'state_id' => $this->state->id,
            'city_id' => $this->city->id,
            'customer_name' => 'Robert Baratheon',
            'customer_type' => 'B2B',
            'gst_number' => '33AAAAA0000A1Z5',
            'mobile' => '9876543219',
            'pincode' => '641001',
            'status' => 1,
        ]);

        $role = Role::create(['company_id' => $this->company->id, 'name' => 'Admin', 'slug' => 'admin', 'status' => 1]);
        $permission = Permission::firstOrCreate(['slug' => 'reports.customer'], ['name' => 'View Customer Report', 'module' => 'reports', 'status' => 1]);
        $role->permissions()->attach($permission->id);
        $this->user->role_id = $role->id;
        $this->user->save();

        $this->actingAs($this->user);
    }

    public function test_customer_report_index_renders_successfully()
    {
        $response = $this->get(route('reports.customer.index'));

        $response->assertStatus(200);
        $response->assertSee('Customer Performance');
    }

    public function test_customer_report_search_returns_filtered_results()
    {
        Sale::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'counter_id' => $this->counter->id,
            'sales_person_id' => $this->user->id,
            'customer_id' => $this->customer->id,
            'customer_type' => 'B2B',
            'sale_type' => 1,
            'gst_type' => 1,
            'invoice_no' => 102,
            'invoice_no_display' => 'INV-102',
            'invoice_date' => now()->toDateString(),
            'status' => Sale::STATUS_COMPLETED,
            'subtotal' => 2000.00,
            'item_discount' => 100.00,
            'invoice_discount' => 50.00,
            'tax_amount' => 360.00,
            'grand_total' => 2210.00,
            'created_by' => $this->user->id,
        ]);

        $response = $this->post(route('reports.customer.search'), [
            'from_date' => now()->startOfMonth()->toDateString(),
            'to_date' => now()->toDateString(),
            'customer_type' => 'B2B',
            'state_id' => $this->state->id,
            'search_text' => 'Robert',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['html', 'reportData']);
        $this->assertEquals(1, $response->json('reportData.summary.total_customers'));
        $this->assertEquals(2210.00, $response->json('reportData.summary.total_revenue'));
    }

    public function test_customer_sales_modal_returns_sales_history()
    {
        Sale::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'counter_id' => $this->counter->id,
            'sales_person_id' => $this->user->id,
            'customer_id' => $this->customer->id,
            'customer_type' => 'B2B',
            'sale_type' => 1,
            'gst_type' => 1,
            'invoice_no' => 103,
            'invoice_no_display' => 'INV-103',
            'invoice_date' => now()->toDateString(),
            'status' => Sale::STATUS_COMPLETED,
            'subtotal' => 1500.00,
            'tax_amount' => 270.00,
            'grand_total' => 1770.00,
            'created_by' => $this->user->id,
        ]);

        $response = $this->get(route('reports.customer.sales-modal', $this->customer->id));

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'html', 'customer_name']);
        $this->assertTrue($response->json('success'));
        $this->assertEquals($this->customer->customer_name, $response->json('customer_name'));
    }
}
