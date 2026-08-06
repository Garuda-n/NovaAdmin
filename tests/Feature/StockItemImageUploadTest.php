<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Company;
use App\Models\Counter;
use App\Models\Country;
use App\Models\Product;
use App\Models\State;
use App\Models\City;
use App\Models\StockItem;
use App\Models\StockItemImage;
use App\Models\Uom;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StockItemImageUploadTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected StockItem $stockItem;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->seed(\Database\Seeders\PermissionSeeder::class);

        $this->user = User::factory()->create();

        $country = Country::create(['name' => 'India', 'code' => 'IN', 'status' => 1]);
        $state = State::create(['country_id' => $country->id, 'name' => 'Tamil Nadu', 'code' => 'TN', 'status' => 1]);
        $city = City::create(['state_id' => $state->id, 'name' => 'Coimbatore', 'status' => 1]);
        $company = Company::create(['name' => 'Test Company', 'code' => 'COMP1', 'status' => 1]);
        $branch = Branch::create(['company_id' => $company->id, 'name' => 'Test Branch', 'branch_name' => 'Test Branch', 'branch_code' => 'CBE', 'status' => 1]);
        $counter = Counter::create(['branch_id' => $branch->id, 'counter_name' => 'Counter 1', 'counter_code' => 'C1', 'status' => 1]);
        $category = Category::create(['name' => 'Electronics', 'code' => 'ELE', 'status' => 1]);
        $brand = Brand::create(['company_id' => $company->id, 'name' => 'Apple', 'code' => 'APL', 'status' => 1]);
        $uom = Uom::create(['company_id' => $company->id, 'name' => 'Piece', 'shortcode' => 'PCS', 'code' => 'PCS', 'status' => 1]);

        $product = Product::create([
            'code' => 'PROD1',
            'name' => 'Phone',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'uom_id' => $uom->id,
            'status' => 1,
        ]);

        $supplier = \App\Models\Supplier::create([
            'company_id' => $company->id,
            'supplier_name' => 'Supplier 1',
            'supplier_code' => 'SUP001',
            'supplier_type' => 'B2B',
            'mobile' => '9876543210',
            'country_id' => $country->id,
            'state_id' => $state->id,
            'city_id' => $city->id,
            'pincode' => '400001',
            'status' => 1,
        ]);

        $inward = \App\Models\StockInward::create([
            'company_id' => $company->id,
            'branch_id' => $branch->id,
            'counter_id' => $counter->id,
            'supplier_id' => $supplier->id,
            'invoice_no' => 'INV-INW-01',
            'invoice_date' => now()->toDateString(),
            'status' => true,
            'created_by' => $this->user->id,
        ]);

        $inwardItem = \App\Models\StockInwardItem::create([
            'stock_inward_id' => $inward->id,
            'product_id' => $product->id,
            'qty' => 10,
            'purchase_price' => 100,
            'selling_price' => 150,
            'mrp' => 200,
        ]);

        $this->stockItem = StockItem::create([
            'stock_inward_id' => $inward->id,
            'stock_inward_item_id' => $inwardItem->id,
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'counter_id' => $counter->id,
            'item_code' => 'ITEM0001',
            'status' => 1,
        ]);

        $role = Role::create(['company_id' => $company->id, 'name' => 'Admin', 'slug' => 'admin', 'status' => 1]);
        $role->permissions()->sync(Permission::all());

        $this->user->role_id = $role->id;
        $this->user->save();

        $this->actingAs($this->user);
    }

    public function test_can_render_stock_item_images_page()
    {
        $response = $this->get(route('stock-item-images.index'));
        $response->assertStatus(200);
        $response->assertSee('Stock Item Images');
    }

    public function test_can_search_stock_item_ajax()
    {
        $response = $this->getJson(route('stock-item-images.search', ['item_code' => $this->stockItem->item_code]));
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'html']);
        $response->assertJson([
            'success' => true
        ]);
    }

    public function test_can_upload_stock_item_image_ajax()
    {
        $file = UploadedFile::fake()->image('stock_item_photo.jpg');

        $response = $this->postJson(route('stock-item-images.upload', $this->stockItem->id), [
            'image' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Image uploaded successfully.'
        ]);

        $this->assertDatabaseCount('stock_item_images', 1);

        $image = StockItemImage::first();
        $this->assertEquals($this->stockItem->id, $image->stock_item_id);
        $this->assertTrue($image->is_default);

        Storage::disk('public')->assertExists($image->image_path);
        $this->assertStringStartsWith('stockitemimg/', $image->image_path);
    }

    public function test_multiple_uploads_sets_first_as_default_and_allows_switching_default_ajax()
    {
        $file1 = UploadedFile::fake()->image('photo1.jpg');
        $this->postJson(route('stock-item-images.upload', $this->stockItem->id), ['image' => $file1]);

        $file2 = UploadedFile::fake()->image('photo2.jpg');
        $this->postJson(route('stock-item-images.upload', $this->stockItem->id), ['image' => $file2]);

        $this->assertDatabaseCount('stock_item_images', 2);

        $image1 = StockItemImage::orderBy('id', 'asc')->first();
        $image2 = StockItemImage::orderBy('id', 'desc')->first();

        $this->assertTrue($image1->is_default);
        $this->assertFalse($image2->is_default);

        $response = $this->putJson(route('stock-item-images.set-default', [$this->stockItem->id, $image2->id]));
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Default image updated successfully.'
        ]);

        $image1->refresh();
        $image2->refresh();

        $this->assertFalse($image1->is_default);
        $this->assertTrue($image2->is_default);
    }

    public function test_can_delete_image_and_default_shifts_to_remaining_image_ajax()
    {
        $file1 = UploadedFile::fake()->image('photo1.jpg');
        $this->postJson(route('stock-item-images.upload', $this->stockItem->id), ['image' => $file1]);

        $file2 = UploadedFile::fake()->image('photo2.jpg');
        $this->postJson(route('stock-item-images.upload', $this->stockItem->id), ['image' => $file2]);

        $image1 = StockItemImage::orderBy('id', 'asc')->first();
        $image2 = StockItemImage::orderBy('id', 'desc')->first();

        $this->assertTrue($image1->is_default);

        $response = $this->deleteJson(route('stock-item-images.delete', [$this->stockItem->id, $image1->id]));
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Image deleted successfully.'
        ]);

        $this->assertDatabaseMissing('stock_item_images', ['id' => $image1->id]);
        Storage::disk('public')->assertMissing($image1->image_path);

        $image2->refresh();
        $this->assertTrue($image2->is_default);
    }
}
