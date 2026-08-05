<?php

namespace Tests\Feature;

use App\Enums\StockItemStatus;
use App\Models\Branch;
use App\Models\Brand;
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
use App\Models\SalesDetail;
use App\Models\State;
use App\Models\StockItem;
use App\Models\StockMovement;
use App\Models\Uom;
use App\Models\User;
use App\Services\QuotationService;
use App\Services\Sales\SalesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Tests\TestCase;

class SerializedItemSalesIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected Branch $branch;
    protected Counter $counter;
    protected Customer $customer;
    protected Product $serializedProduct;
    protected StockItem $stockItem6;
    protected StockItem $stockItem7;
    protected PaymentMode $paymentMode;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);

        $this->user = User::factory()->create();
        $country = Country::create(['name' => 'India', 'code' => 'IN', 'status' => 1]);
        $state = State::create(['country_id' => $country->id, 'name' => 'Maharashtra', 'code' => 'MH', 'status' => 1]);
        $city = City::create(['state_id' => $state->id, 'name' => 'Mumbai', 'status' => 1]);
        $this->company = Company::create(['name' => 'Test Company', 'code' => 'COMP1', 'status' => 1]);
        $this->branch = Branch::create(['company_id' => $this->company->id, 'name' => 'Head Office', 'branch_name' => 'Head Office', 'branch_code' => 'HO', 'status' => 1]);
        $this->counter = Counter::create(['branch_id' => $this->branch->id, 'counter_name' => 'Counter 1', 'counter_code' => 'C1', 'status' => 1]);
        $category = Category::create(['company_id' => $this->company->id, 'name' => 'Electronics', 'code' => 'ELEC', 'status' => 1]);
        $brand = Brand::create(['company_id' => $this->company->id, 'name' => 'Apple', 'code' => 'AAPL', 'status' => 1]);
        $uom = Uom::create(['company_id' => $this->company->id, 'name' => 'Pieces', 'shortcode' => 'PCS', 'code' => 'PCS', 'status' => 1]);
        $this->paymentMode = PaymentMode::create(['company_id' => $this->company->id, 'mode_code' => 'CASH', 'mode_name' => 'Cash', 'mode_type' => 1, 'status' => 1, 'created_by' => $this->user->id, 'updated_by' => $this->user->id]);

        $this->customer = Customer::create([
            'company_id' => $this->company->id,
            'country_id' => $country->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
            'customer_name' => 'John Doe',
            'mobile' => '9876543210',
            'pincode' => '400001',
            'status' => 1,
        ]);

        $supplier = \App\Models\Supplier::create([
            'company_id' => $this->company->id,
            'country_id' => $country->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
            'supplier_name' => 'Test Supplier',
            'supplier_type' => 'B2B',
            'mobile' => '9876543211',
            'pincode' => '400001',
            'status' => 1,
        ]);

        $this->serializedProduct = Product::create([
            'company_id' => $this->company->id,
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'uom_id' => $uom->id,
            'name' => 'iPhone 15 Pro',
            'code' => 'IP15P',
            'product_code' => 'IP15P',
            'price' => 100000.00,
            'sales_rate' => 100000.00,
            'tax_percentage' => 18,
            'tracking_type' => 2, // Individual Tracking
            'status' => 1,
        ]);

        $stockInward = \App\Models\StockInward::create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'counter_id' => $this->counter->id,
            'supplier_id' => $supplier->id,
            'invoice_no' => 'INV-TEST-001',
            'invoice_date' => now()->toDateString(),
            'status' => 1,
            'created_by' => $this->user->id,
        ]);

        $stockInwardItem = \App\Models\StockInwardItem::create([
            'stock_inward_id' => $stockInward->id,
            'product_id' => $this->serializedProduct->id,
            'qty' => 2,
            'purchase_price' => 80000.00,
            'selling_price' => 100000.00,
        ]);

        $this->stockItem6 = StockItem::create([
            'stock_inward_id' => $stockInward->id,
            'stock_inward_item_id' => $stockInwardItem->id,
            'product_id' => $this->serializedProduct->id,
            'branch_id' => $this->branch->id,
            'counter_id' => $this->counter->id,
            'item_code' => 'PRD00006',
            'status' => StockItemStatus::AVAILABLE->value,
        ]);

        $this->stockItem7 = StockItem::create([
            'stock_inward_id' => $stockInward->id,
            'stock_inward_item_id' => $stockInwardItem->id,
            'product_id' => $this->serializedProduct->id,
            'branch_id' => $this->branch->id,
            'counter_id' => $this->counter->id,
            'item_code' => 'PRD00007',
            'status' => StockItemStatus::AVAILABLE->value,
        ]);

        $this->actingAs($this->user);
    }

    public function test_explicitly_selected_serialized_item_is_sold_during_quotation_conversion()
    {
        /** @var QuotationService $quotationService */
        $quotationService = app(QuotationService::class);

        // 1. Create Quotation with explicit selection PRD00007 (stock_item_id = 7)
        $quotation = $quotationService->store([
            'branch_id' => $this->branch->id,
            'counter_id' => $this->counter->id,
            'customer_id' => $this->customer->id,
            'customer_type' => 'B2C',
            'remarks' => 'Selling PRD00007',
            'items' => [
                [
                    'product_id' => $this->serializedProduct->id,
                    'stock_item_id' => $this->stockItem7->id,
                    'product_name' => $this->serializedProduct->name,
                    'uom_id' => $this->serializedProduct->uom_id,
                    'qty' => 1,
                    'rate' => 100000.00,
                ],
            ],
        ]);

        // Assert quotation detail preserved PRD00007 stock_item_id
        $detail = $quotation->details()->first();
        $this->assertEquals($this->stockItem7->id, $detail->stock_item_id);

        /** @var SalesService $salesService */
        $salesService = app(SalesService::class);

        // 2. Convert Quotation to Sale Invoice
        $sale = $salesService->convertQuotationToSale($quotation, [
            'sale_type' => 1,
            'gst_type' => 1,
            'payment_mode_id' => $this->paymentMode->id,
            'paid_amount' => 118000.00,
        ]);

        // 3. Verify exact item status updates
        $this->stockItem7->refresh();
        $this->stockItem6->refresh();

        // PRD00007 MUST be SOLD
        $this->assertEquals(StockItemStatus::SOLD->value, $this->stockItem7->status);
        // PRD00006 MUST REMAIN AVAILABLE
        $this->assertEquals(StockItemStatus::AVAILABLE->value, $this->stockItem6->status);

        // Verify sales_details allocated_item_id points to PRD00007
        $salesDetail = $sale->details()->first();
        $this->assertEquals($this->stockItem7->id, $salesDetail->allocated_item_id);

        // Verify stock movement belongs to PRD00007
        $movement = StockMovement::where('reference_type', Sale::class)
            ->where('reference_id', $sale->id)
            ->first();
        $this->assertNotNull($movement);
        $this->assertEquals($this->stockItem7->id, $movement->stock_item_id);
    }

    public function test_conversion_fails_with_item_code_if_selected_item_becomes_unavailable()
    {
        /** @var QuotationService $quotationService */
        $quotationService = app(QuotationService::class);

        $quotation = $quotationService->store([
            'branch_id' => $this->branch->id,
            'counter_id' => $this->counter->id,
            'customer_id' => $this->customer->id,
            'customer_type' => 'B2C',
            'items' => [
                [
                    'product_id' => $this->serializedProduct->id,
                    'stock_item_id' => $this->stockItem7->id,
                    'product_name' => $this->serializedProduct->name,
                    'uom_id' => $this->serializedProduct->uom_id,
                    'qty' => 1,
                    'rate' => 100000.00,
                ],
            ],
        ]);

        // Simulate external sale / damage of PRD00007 before conversion
        $this->stockItem7->update(['status' => StockItemStatus::SOLD->value]);

        /** @var SalesService $salesService */
        $salesService = app(SalesService::class);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("Selected stock item 'PRD00007' for product 'iPhone 15 Pro' is no longer available (Status: Sold)");

        $salesService->convertQuotationToSale($quotation, [
            'sale_type' => 1,
            'gst_type' => 1,
            'payment_mode_id' => $this->paymentMode->id,
            'paid_amount' => 118000.00,
        ]);
    }

    public function test_quotation_update_allows_same_already_selected_item()
    {
        /** @var QuotationService $quotationService */
        $quotationService = app(QuotationService::class);

        $quotation = $quotationService->store([
            'branch_id' => $this->branch->id,
            'counter_id' => $this->counter->id,
            'customer_id' => $this->customer->id,
            'customer_type' => 'B2C',
            'items' => [
                [
                    'product_id' => $this->serializedProduct->id,
                    'stock_item_id' => $this->stockItem7->id,
                    'product_name' => $this->serializedProduct->name,
                    'uom_id' => $this->serializedProduct->uom_id,
                    'qty' => 1,
                    'rate' => 100000.00,
                ],
            ],
        ]);

        // Temporarily change PRD00007 status to RESERVED or COUNTER_TRANSFERRED
        $this->stockItem7->update(['status' => StockItemStatus::RESERVED->value]);

        // Updating quotation with the SAME stock_item_id should succeed
        $updatedQuotation = $quotationService->update($quotation, [
            'branch_id' => $this->branch->id,
            'counter_id' => $this->counter->id,
            'customer_id' => $this->customer->id,
            'customer_type' => 'B2C',
            'remarks' => 'Updated remarks',
            'items' => [
                [
                    'product_id' => $this->serializedProduct->id,
                    'stock_item_id' => $this->stockItem7->id,
                    'product_name' => $this->serializedProduct->name,
                    'uom_id' => $this->serializedProduct->uom_id,
                    'qty' => 1,
                    'rate' => 100000.00,
                ],
            ],
        ]);

        $this->assertEquals('Updated remarks', $updatedQuotation->remarks);
        $this->assertEquals($this->stockItem7->id, $updatedQuotation->details()->first()->stock_item_id);
    }

    public function test_quotation_creation_fails_if_item_branch_does_not_match_quotation_branch()
    {
        $otherBranch = Branch::create(['company_id' => $this->company->id, 'name' => 'Coimbatore Branch', 'branch_name' => 'Coimbatore', 'branch_code' => 'CBE', 'status' => 1]);

        /** @var QuotationService $quotationService */
        $quotationService = app(QuotationService::class);

        // stockItem7 belongs to $this->branch (Head Office), but quotation is submitted under $otherBranch (Coimbatore)
        $this->expectException(ValidationException::class);

        $quotationService->store([
            'branch_id' => $otherBranch->id,
            'counter_id' => null,
            'customer_id' => $this->customer->id,
            'customer_type' => 'B2C',
            'items' => [
                [
                    'product_id' => $this->serializedProduct->id,
                    'stock_item_id' => $this->stockItem7->id,
                    'product_name' => $this->serializedProduct->name,
                    'uom_id' => $this->serializedProduct->uom_id,
                    'qty' => 1,
                    'rate' => 100000.00,
                ],
            ],
        ]);
    }
}
