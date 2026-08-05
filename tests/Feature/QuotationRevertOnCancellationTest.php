<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Branch;
use App\Models\Category;
use App\Models\City;
use App\Models\Company;
use App\Models\Counter;
use App\Models\Country;
use App\Models\Customer;
use App\Models\PaymentMode;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationDetail;
use App\Models\Sale;
use App\Models\State;
use App\Models\StockMovement;
use App\Models\Uom;
use App\Models\User;
use App\Services\Sales\SalesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuotationRevertOnCancellationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
    }

    public function test_cancelling_sales_invoice_reverts_quotation_status_to_created()
    {
        $user = User::factory()->create();
        $country = Country::create(['name' => 'India', 'code' => 'IN', 'status' => 1]);
        $state = State::create(['country_id' => $country->id, 'name' => 'Maharashtra', 'code' => 'MH', 'status' => 1]);
        $city = City::create(['state_id' => $state->id, 'name' => 'Mumbai', 'status' => 1]);
        $company = Company::create(['name' => 'Test Company', 'code' => 'COMP1', 'status' => 1]);
        $branch = Branch::create(['company_id' => $company->id, 'name' => 'Head Office', 'branch_name' => 'Head Office', 'branch_code' => 'HO', 'status' => 1]);
        $counter = Counter::create(['branch_id' => $branch->id, 'counter_name' => 'Counter 1', 'counter_code' => 'C1', 'status' => 1]);
        $category = Category::create(['company_id' => $company->id, 'name' => 'General', 'code' => 'CAT1', 'status' => 1]);
        $brand = Brand::create(['company_id' => $company->id, 'name' => 'General', 'code' => 'BRD1', 'status' => 1]);
        $uom = Uom::create(['company_id' => $company->id, 'name' => 'Pieces', 'shortcode' => 'PCS', 'code' => 'PCS', 'status' => 1]);
        $paymentMode = PaymentMode::create(['company_id' => $company->id, 'mode_code' => 'CASH', 'mode_name' => 'Cash', 'mode_type' => 1, 'status' => 1, 'created_by' => $user->id, 'updated_by' => $user->id]);

        $customer = Customer::create([
            'company_id' => $company->id,
            'country_id' => $country->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
            'customer_name' => 'Test Customer',
            'mobile' => '9999999999',
            'pincode' => '400001',
            'status' => 1,
        ]);

        $product = Product::create([
            'company_id' => $company->id,
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'uom_id' => $uom->id,
            'name' => 'Test Product',
            'code' => 'P100',
            'product_code' => 'P100',
            'price' => 100.00,
            'sales_rate' => 100.00,
            'tax_percentage' => 18,
            'tracking_type' => 1, // Quantity based
            'status' => 1,
        ]);

        // Seed stock for testing
        StockMovement::create([
            'company_id' => $company->id,
            'branch_id' => $branch->id,
            'counter_id' => $counter->id,
            'product_id' => $product->id,
            'movement_type' => 1, // Inward
            'quantity' => 10,
            'movement_date' => now()->toDateString(),
            'business_date' => now()->toDateString(),
            'created_by' => $user->id,
        ]);

        $quotation = Quotation::create([
            'quotation_no' => 'Q-100',
            'business_date' => now()->toDateString(),
            'branch_id' => $branch->id,
            'counter_id' => $counter->id,
            'customer_id' => $customer->id,
            'customer_type' => 'B2C',
            'status' => Quotation::STATUS_CREATED,
            'subtotal' => 100.00,
            'tax_amount' => 18.00,
            'grand_total' => 118.00,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        QuotationDetail::create([
            'quotation_id' => $quotation->id,
            'product_id' => $product->id,
            'uom_id' => $uom->id,
            'uom_name' => 'PCS',
            'product_name' => $product->name,
            'qty' => 1,
            'rate' => 100.00,
            'tax_percent' => 18.00,
            'tax_amount' => 18.00,
            'line_total' => 118.00,
        ]);

        /** @var SalesService $salesService */
        $salesService = app(SalesService::class);

        // 1. Convert quotation to sale
        $sale = $salesService->convertQuotationToSale($quotation, [
            'sale_type' => 1,
            'gst_type' => 1,
            'payment_mode_id' => $paymentMode->id,
            'paid_amount' => 118.00,
        ]);

        $quotation->refresh();
        $this->assertEquals(Quotation::STATUS_CONVERTED, $quotation->status);
        $this->assertFalse($quotation->isConvertible());

        // 2. Cancel the sale invoice
        $salesService->cancelSale($sale, ['cancel_reason' => 'Test Cancellation']);

        $quotation->refresh();
        // 3. Verify quotation status is reverted to STATUS_CREATED and is convertible again
        $this->assertEquals(Quotation::STATUS_CREATED, $quotation->status);
        $this->assertTrue($quotation->isConvertible());
    }
}
