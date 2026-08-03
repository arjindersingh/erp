<?php

declare(strict_types=1);

namespace Tests\Feature\Core;

use App\Core\Modules\Module;
use App\Core\Modules\ModuleFeature;
use App\Core\Modules\ModuleFeatureGroup;
use App\Core\Modules\TenantModule;
use App\Core\Navigation\Exceptions\InvalidMenuItem;
use App\Core\Navigation\MenuItem;
use App\Core\Navigation\MenuSet;
use App\Core\Navigation\Portal;
use App\Core\Tenancy\Tenant;
use Database\Seeders\CoreModuleSeeder;
use Database\Seeders\CorePermissionSeeder;
use Database\Seeders\NavigationFoundationSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use Tests\TestCase;

class NavigationFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_module_feature_hierarchy_remains_inside_one_module(): void
    {
        $transport = Module::factory()->create(['code' => 'transport']);
        $fees = Module::factory()->create(['code' => 'fees']);
        $foreignGroup = ModuleFeatureGroup::factory()->for($fees)->create();

        $this->expectException(LogicException::class);

        ModuleFeature::factory()->for($transport)->create(['feature_group_id' => $foreignGroup->id]);
    }

    public function test_tenant_module_settings_are_isolated_and_unique(): void
    {
        $module = Module::factory()->create();
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();
        TenantModule::factory()->for($tenantA)->for($module)->create(['configuration_json' => ['label' => 'A Fleet']]);
        TenantModule::factory()->for($tenantB)->for($module)->create(['configuration_json' => ['label' => 'B Fleet']]);

        $this->assertSame('A Fleet', TenantModule::query()->where('tenant_id', $tenantA->id)->firstOrFail()->configuration_json['label']);

        $this->expectException(QueryException::class);
        TenantModule::factory()->for($tenantA)->for($module)->create();
    }

    public function test_core_module_cannot_be_disabled(): void
    {
        $module = Module::factory()->create(['is_core' => true]);

        $this->expectException(LogicException::class);

        TenantModule::factory()->for($module)->create(['is_enabled' => false]);
    }

    public function test_menu_order_and_depth_are_derived_from_valid_groups(): void
    {
        $menu = MenuSet::factory()->create();
        $group = MenuItem::factory()->for($menu)->create(['title' => 'Transport', 'route_name' => null, 'item_type' => 'group', 'display_order' => 10]);
        $second = MenuItem::factory()->for($menu)->create(['parent_id' => $group->id, 'title' => 'Routes', 'display_order' => 20]);
        $first = MenuItem::factory()->for($menu)->create(['parent_id' => $group->id, 'title' => 'Vehicles', 'display_order' => 10]);

        $this->assertSame(1, $first->depth);
        $this->assertSame(['Vehicles', 'Routes'], $group->children()->pluck('title')->all());
        $this->assertSame($menu->portal_id, $group->menuSet->portal_id);
        $this->assertNotSame($first->id, $second->id);
    }

    public function test_non_group_and_cross_menu_parents_are_rejected(): void
    {
        $menu = MenuSet::factory()->create();
        $otherMenu = MenuSet::factory()->create();
        $link = MenuItem::factory()->for($menu)->create();

        $this->expectException(InvalidMenuItem::class);

        MenuItem::factory()->for($otherMenu)->create(['parent_id' => $link->id]);
    }

    public function test_circular_menu_hierarchy_is_rejected(): void
    {
        $menu = MenuSet::factory()->create();
        $root = MenuItem::factory()->for($menu)->create(['route_name' => null, 'item_type' => 'group']);
        $child = MenuItem::factory()->for($menu)->create(['parent_id' => $root->id, 'route_name' => null, 'item_type' => 'group']);

        $this->expectException(InvalidMenuItem::class);

        $root->update(['parent_id' => $child->id]);
    }

    public function test_external_urls_reject_unsafe_schemes(): void
    {
        $this->expectException(InvalidMenuItem::class);

        MenuItem::factory()->create(['route_name' => null, 'external_url' => 'javascript:alert(1)', 'item_type' => 'external_link']);
    }

    public function test_seeded_portals_produce_different_transport_navigation(): void
    {
        $this->seed(CoreModuleSeeder::class);
        $this->seed(CorePermissionSeeder::class);
        $this->seed(NavigationFoundationSeeder::class);
        $this->seed(NavigationFoundationSeeder::class);

        $parent = Portal::query()->where('code', 'parent')->firstOrFail();
        $staff = Portal::query()->where('code', 'staff')->firstOrFail();
        $parentItems = $parent->menuSets()->firstOrFail()->items()->pluck('title');
        $staffItems = $staff->menuSets()->firstOrFail()->items()->pluck('title');

        $this->assertTrue($parentItems->contains("My Child's Route"));
        $this->assertFalse($parentItems->contains('Vehicles'));
        $this->assertTrue($staffItems->contains('Vehicles'));
        $this->assertSame(9, Portal::query()->count());
    }
}
