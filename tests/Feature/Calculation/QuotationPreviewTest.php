<?php

namespace Tests\Feature\Calculation;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuotationPreviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\PermissionSeeder::class);
    }

    protected function userWithPermissions(array $slugs): User
    {
        $role = Role::create(['name' => 'Test Role '.uniqid(), 'slug' => 'test-role-'.uniqid()]);
        $role->permissions()->sync(Permission::whereIn('slug', $slugs)->pluck('id'));

        return User::factory()->create(['role_id' => $role->id]);
    }

    public function test_preview_returns_totals_matching_the_calculation_engine()
    {
        $user = $this->userWithPermissions(['quotation.create']);

        $response = $this->actingAs($user)->postJson(route('quotations.calculate'), [
            'items' => [
                ['qty' => 2, 'rate' => 50, 'tax_percent' => 18],
                ['qty' => 1, 'rate' => 100, 'tax_percent' => 5],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('totals.items.0.tax_amount', 18);
        $response->assertJsonPath('totals.items.0.line_total', 118);
        $response->assertJsonPath('totals.items.1.tax_amount', 5);
        $response->assertJsonPath('totals.items.1.line_total', 105);
        $response->assertJsonPath('totals.subtotal', 200);
        $response->assertJsonPath('totals.tax_amount', 23);
        $response->assertJsonPath('totals.grand_total', 223);
    }

    public function test_preview_is_allowed_with_edit_permission_only()
    {
        $user = $this->userWithPermissions(['quotation.edit']);

        $response = $this->actingAs($user)->postJson(route('quotations.calculate'), [
            'items' => [['qty' => 1, 'rate' => 10.30, 'tax_percent' => 18]],
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('totals.items.0.tax_amount', 1.85);
        $response->assertJsonPath('totals.items.0.line_total', 12.15);
    }

    public function test_preview_is_denied_without_quotation_permissions()
    {
        $user = $this->userWithPermissions(['sales.create']);

        $response = $this->actingAs($user)->postJson(route('quotations.calculate'), [
            'items' => [['qty' => 1, 'rate' => 100, 'tax_percent' => 18]],
        ]);

        $response->assertStatus(403);
        $response->assertJsonPath('success', false);
    }

    public function test_preview_handles_empty_items()
    {
        $user = $this->userWithPermissions(['quotation.create']);

        $response = $this->actingAs($user)->postJson(route('quotations.calculate'), ['items' => []]);

        $response->assertStatus(200);
        $response->assertJsonPath('totals.subtotal', 0);
        $response->assertJsonPath('totals.grand_total', 0);
    }
}
