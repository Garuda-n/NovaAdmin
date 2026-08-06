<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\City;
use App\Models\Company;
use App\Models\Counter;
use App\Models\Country;
use App\Models\Customer;
use App\Models\CustomerReceivable;
use App\Models\PaymentMode;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Sale;
use App\Models\State;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReceivablePaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected Branch $branch;
    protected Counter $counter;
    protected Customer $customer;
    protected State $state;
    protected City $city;
    protected PaymentMode $paymentMode;

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
            'customer_name' => 'Gokul',
            'customer_type' => 'B2C',
            'mobile' => '9876543219',
            'pincode' => '641001',
            'status' => 1,
        ]);

        $this->seed(\Database\Seeders\PaymentModeSeeder::class);
        $this->paymentMode = PaymentMode::where('status', PaymentMode::STATUS_ACTIVE)->first();

        $role = Role::create(['company_id' => $this->company->id, 'name' => 'Admin', 'slug' => 'admin', 'status' => 1]);
        // View sales and allocate payments permissions
        $permView = Permission::firstOrCreate(['slug' => 'sales.view'], ['name' => 'View Sales', 'module' => 'Sales', 'status' => 1]);
        $permAllocate = Permission::firstOrCreate(['slug' => 'receivable.allocate'], ['name' => 'Allocate Payments', 'module' => 'Receivables', 'status' => 1]);
        $role->permissions()->attach([$permView->id, $permAllocate->id]);

        $this->user->role_id = $role->id;
        $this->user->save();

        $this->actingAs($this->user);
    }

    public function test_collect_payment_creates_payment_record_and_allocates_successfully()
    {
        // 1. Create a credit sale
        $sale = Sale::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'counter_id' => $this->counter->id,
            'sales_person_id' => $this->user->id,
            'customer_id' => $this->customer->id,
            'sale_type' => Sale::TYPE_CREDIT,
            'gst_type' => 1,
            'invoice_no' => 101,
            'invoice_no_display' => 'INV-101',
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'status' => Sale::STATUS_COMPLETED,
            'subtotal' => 100.00,
            'item_discount' => 0.00,
            'invoice_discount' => 0.00,
            'tax_amount' => 5.00,
            'grand_total' => 105.00,
            'created_by' => $this->user->id,
        ]);

        // 2. Create customer receivable for it
        $receivable = CustomerReceivable::create([
            'sales_id' => $sale->id,
            'customer_id' => $this->customer->id,
            'invoice_date' => $sale->invoice_date,
            'due_date' => $sale->due_date,
            'original_amount' => $sale->grand_total,
            'paid_amount' => 0.00,
            'balance_amount' => $sale->grand_total,
            'status' => CustomerReceivable::STATUS_PENDING,
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        // 3. Make POST request to collect payment
        $response = $this->post(route('sales.collect-payment', $sale->id), [
            'payment_date' => now()->toDateString(),
            'payment_mode_id' => $this->paymentMode->id,
            'amount' => 50.00,
            'reference_no' => 'TXN12345',
            'remarks' => 'Partial payment',
        ]);

        // 4. Assert response is redirect
        $response->assertRedirect(route('sales.show', $sale->id));
        $response->assertSessionHas('success', 'Payment collected and allocated successfully.');

        // 5. Assert database updates
        $receivable->refresh();
        $this->assertEquals(50.00, (float)$receivable->paid_amount);
        $this->assertEquals(55.00, (float)$receivable->balance_amount);
        $this->assertEquals(CustomerReceivable::STATUS_PARTIALLY_PAID, $receivable->status);

        $this->assertDatabaseHas('sales_payments', [
            'sales_id' => $sale->id,
            'payment_mode_id' => $this->paymentMode->id,
            'amount' => 50.00,
            'reference_no' => 'TXN12345',
        ]);
    }
}
