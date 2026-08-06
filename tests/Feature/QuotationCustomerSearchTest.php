<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Country;
use App\Models\Customer;
use App\Models\Permission;
use App\Models\Role;
use App\Models\State;
use App\Models\City;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuotationCustomerSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\PermissionSeeder::class);
    }

    public function test_quotations_create_page_does_not_load_all_customers_upfront()
    {
        $role = Role::firstOrCreate(['name' => 'Admin', 'slug' => 'admin']);
        $role->permissions()->sync(Permission::all());
        $user = User::factory()->create(['role_id' => $role->id]);

        $country = Country::create(['name' => 'India', 'code' => 'IN', 'status' => 1]);
        $state = State::create(['country_id' => $country->id, 'name' => 'Maharashtra', 'code' => 'MH', 'status' => 1]);
        $city = City::create(['state_id' => $state->id, 'name' => 'Mumbai', 'status' => 1]);
        $company = Company::create(['name' => 'Test Company', 'code' => 'COMP1', 'status' => 1]);

        Customer::create([
            'company_id' => $company->id,
            'customer_name' => 'John Doe',
            'customer_code' => 'CUST001',
            'mobile' => '9876543210',
            'customer_type' => 'B2C',
            'country_id' => $country->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
            'pincode' => '400001',
            'status' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('quotations.create'));

        $response->assertStatus(200);
        $response->assertDontSee('data-search-url=""');
        $response->assertSee(route('quotations.search-customers'));
    }

    public function test_search_customers_ajax_endpoint_returns_matching_active_customers()
    {
        $role = Role::firstOrCreate(['name' => 'Admin', 'slug' => 'admin']);
        $role->permissions()->sync(Permission::all());
        $user = User::factory()->create(['role_id' => $role->id]);

        $country = Country::create(['name' => 'India', 'code' => 'IN', 'status' => 1]);
        $state = State::create(['country_id' => $country->id, 'name' => 'Maharashtra', 'code' => 'MH', 'status' => 1]);
        $city = City::create(['state_id' => $state->id, 'name' => 'Mumbai', 'status' => 1]);
        $company = Company::create(['name' => 'Test Company', 'code' => 'COMP1', 'status' => 1]);

        $cust1 = Customer::create([
            'company_id' => $company->id,
            'customer_name' => 'Alice Smith',
            'customer_code' => 'CUST001',
            'mobile' => '9111111111',
            'customer_type' => 'B2C',
            'country_id' => $country->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
            'pincode' => '400001',
            'status' => 1,
        ]);

        $cust2 = Customer::create([
            'company_id' => $company->id,
            'customer_name' => 'Bob Builder',
            'customer_code' => 'CUST002',
            'mobile' => '9222222222',
            'customer_type' => 'B2B',
            'country_id' => $country->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
            'pincode' => '400001',
            'status' => 1,
        ]);

        $response = $this->actingAs($user)->getJson(route('quotations.search-customers', ['q' => 'Alice']));

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonCount(1, 'customers');
        $response->assertJsonPath('customers.0.name', 'Alice Smith');
    }

    public function test_quotations_filter_works_with_date_range()
    {
        $role = Role::firstOrCreate(['name' => 'Admin', 'slug' => 'admin']);
        $role->permissions()->sync(Permission::all());
        $user = User::factory()->create(['role_id' => $role->id]);

        $country = Country::create(['name' => 'India', 'code' => 'IN', 'status' => 1]);
        $state = State::create(['country_id' => $country->id, 'name' => 'Maharashtra', 'code' => 'MH', 'status' => 1]);
        $city = City::create(['state_id' => $state->id, 'name' => 'Mumbai', 'status' => 1]);
        $company = Company::create(['name' => 'Test Company', 'code' => 'COMP1', 'status' => 1]);
        $branch = \App\Models\Branch::create(['company_id' => $company->id, 'name' => 'Coimbatore Branch', 'branch_name' => 'Coimbatore', 'branch_code' => 'CBE', 'status' => 1]);

        $cust = Customer::create([
            'company_id' => $company->id,
            'customer_name' => 'Alice Smith',
            'customer_code' => 'CUST001',
            'mobile' => '9111111111',
            'customer_type' => 'B2C',
            'country_id' => $country->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
            'pincode' => '400001',
            'status' => 1,
        ]);

        // Create one quotation today
        $qToday = \App\Models\Quotation::create([
            'quotation_no' => 'QT-001',
            'business_date' => now()->toDateString(),
            'branch_id' => $branch->id,
            'customer_id' => $cust->id,
            'customer_type' => 'B2C',
            'status' => \App\Models\Quotation::STATUS_CREATED,
            'subtotal' => 100,
            'tax_amount' => 5,
            'grand_total' => 105,
            'created_by' => $user->id,
        ]);

        // Create one quotation yesterday
        $qYesterday = \App\Models\Quotation::create([
            'quotation_no' => 'QT-002',
            'business_date' => now()->subDay()->toDateString(),
            'branch_id' => $branch->id,
            'customer_id' => $cust->id,
            'customer_type' => 'B2C',
            'status' => \App\Models\Quotation::STATUS_CREATED,
            'subtotal' => 100,
            'tax_amount' => 5,
            'grand_total' => 105,
            'created_by' => $user->id,
        ]);

        // Post request with preset = custom, date_from = today and X-Requested-With header
        $response = $this->actingAs($user)->postJson(route('quotations.filter'), [
            'preset' => 'custom',
            'date_from' => now()->toDateString(),
            'date_to' => now()->toDateString(),
        ], [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);

        $response->assertStatus(200);
        $this->assertStringContainsString('QT-001', $response->json('html'));
        $this->assertStringNotContainsString('QT-002', $response->json('html'));
    }
}
