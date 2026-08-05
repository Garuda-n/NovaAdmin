<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\City;
use App\Models\Company;
use App\Models\Country;
use App\Models\Permission;
use App\Models\Role;
use App\Models\State;
use App\Models\StockInward;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected Branch $branch;
    protected Supplier $supplier;
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

        $this->supplier = Supplier::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'country_id' => $country->id,
            'state_id' => $this->state->id,
            'city_id' => $this->city->id,
            'supplier_name' => 'Acme Wholesale Traders',
            'supplier_code' => 'SUP-001',
            'supplier_type' => 'Wholesaler',
            'gst_number' => '33BBBBB0000B1Z5',
            'mobile' => '9876543210',
            'pincode' => '641001',
            'status' => 1,
        ]);

        $role = Role::create(['company_id' => $this->company->id, 'name' => 'Admin', 'slug' => 'admin', 'status' => 1]);
        $permission = Permission::firstOrCreate(['slug' => 'reports.supplier'], ['name' => 'View Supplier Report', 'module' => 'reports', 'status' => 1]);
        $role->permissions()->attach($permission->id);
        $this->user->role_id = $role->id;
        $this->user->save();

        $this->actingAs($this->user);
    }

    public function test_supplier_report_index_renders_successfully()
    {
        $response = $this->get(route('reports.supplier.index'));

        $response->assertStatus(200);
        $response->assertSee('Supplier Procurement');
    }

    public function test_supplier_report_search_returns_filtered_results()
    {
        StockInward::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'supplier_id' => $this->supplier->id,
            'invoice_no' => 'INW-1001',
            'invoice_date' => now()->toDateString(),
            'status' => 1,
            'created_by' => $this->user->id,
        ]);

        $response = $this->post(route('reports.supplier.search'), [
            'from_date' => now()->startOfMonth()->toDateString(),
            'to_date' => now()->toDateString(),
            'supplier_type' => 'Wholesaler',
            'branch_id' => $this->branch->id,
            'search_text' => 'Acme',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['html', 'reportData']);
        $this->assertEquals(1, $response->json('reportData.summary.total_suppliers'));
        $this->assertEquals(1, $response->json('reportData.summary.total_inward_bills'));
    }

    public function test_supplier_inwards_modal_returns_procurement_history()
    {
        StockInward::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'supplier_id' => $this->supplier->id,
            'invoice_no' => 'INW-1002',
            'invoice_date' => now()->toDateString(),
            'status' => 1,
            'created_by' => $this->user->id,
        ]);

        $response = $this->get(route('reports.supplier.inwards-modal', $this->supplier->id));

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'html', 'supplier_name']);
        $this->assertTrue($response->json('success'));
        $this->assertEquals($this->supplier->supplier_name, $response->json('supplier_name'));
    }
}
