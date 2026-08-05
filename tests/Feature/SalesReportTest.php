<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\City;
use App\Models\Company;
use App\Models\Counter;
use App\Models\Country;
use App\Models\Customer;
use App\Models\PaymentMode;
use App\Models\Sale;
use App\Models\State;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected Branch $branch;
    protected Counter $counter;
    protected Customer $customer;
    protected PaymentMode $paymentMode;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);

        $this->user = User::factory()->create();
        $country = Country::create(['name' => 'India', 'code' => 'IN', 'status' => 1]);
        $state = State::create(['country_id' => $country->id, 'name' => 'Tamil Nadu', 'code' => 'TN', 'status' => 1]);
        $city = City::create(['state_id' => $state->id, 'name' => 'Coimbatore', 'status' => 1]);
        $this->company = Company::create(['name' => 'Test Company', 'code' => 'COMP1', 'status' => 1]);
        $this->branch = Branch::create(['company_id' => $this->company->id, 'name' => 'Coimbatore Branch', 'branch_name' => 'Coimbatore', 'branch_code' => 'CBE', 'status' => 1]);
        $this->counter = Counter::create(['branch_id' => $this->branch->id, 'counter_name' => 'Counter 1', 'counter_code' => 'C1', 'status' => 1]);
        $this->paymentMode = PaymentMode::create(['company_id' => $this->company->id, 'mode_code' => 'CASH', 'mode_name' => 'Cash', 'mode_type' => 1, 'status' => 1, 'created_by' => $this->user->id, 'updated_by' => $this->user->id]);

        $this->customer = Customer::create([
            'company_id' => $this->company->id,
            'country_id' => $country->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
            'customer_name' => 'Jane Smith',
            'customer_type' => 'B2B',
            'mobile' => '9876543212',
            'pincode' => '641001',
            'status' => 1,
        ]);

        $role = \App\Models\Role::create(['company_id' => $this->company->id, 'name' => 'Admin', 'slug' => 'admin', 'status' => 1]);
        $permission = \App\Models\Permission::firstOrCreate(['slug' => 'reports.sales'], ['name' => 'View Sales Report', 'module' => 'reports', 'status' => 1]);
        $role->permissions()->attach($permission->id);
        $this->user->role_id = $role->id;
        $this->user->save();

        $this->actingAs($this->user);
    }

    public function test_sales_report_index_renders_successfully()
    {
        $response = $this->get(route('reports.sales.index'));

        $response->assertStatus(200);
        $response->assertSee('Sales Performance Report');
    }

    public function test_sales_report_search_returns_filtered_results()
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
            'invoice_no' => 101,
            'invoice_no_display' => 'INV-101',
            'invoice_date' => now()->toDateString(),
            'status' => Sale::STATUS_COMPLETED,
            'subtotal' => 1000.00,
            'tax_amount' => 180.00,
            'discount_amount' => 0.00,
            'grand_total' => 1180.00,
            'created_by' => $this->user->id,
        ]);

        $response = $this->post(route('reports.sales.search'), [
            'from_date' => now()->startOfMonth()->toDateString(),
            'to_date' => now()->toDateString(),
            'branch_id' => $this->branch->id,
            'customer_type' => 'B2B',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['html', 'reportData']);
        $this->assertEquals(1, $response->json('reportData.summary.completed_invoices'));
        $this->assertEquals(1180.00, $response->json('reportData.summary.gross_revenue'));
    }
}
