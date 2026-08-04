<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Core\Modules\Module;
use App\Core\Modules\ModuleFeatureGroup;
use App\Core\Navigation\MenuItem;
use App\Core\Navigation\MenuSet;
use App\Core\Navigation\Portal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Route;

class NavigationFoundationSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPortals();
        $this->structureTransport();
        $this->seedTransportMenus();
    }

    private function seedPortals(): void
    {
        $portals = [
            'public' => ['Public Portal', false], 'site_admin' => ['Site Administration Portal', true],
            'management' => ['Management Portal', true], 'administration' => ['Administration Portal', true],
            'staff' => ['Staff Portal', true], 'teacher' => ['Teacher Portal', true],
            'student' => ['Student Portal', true], 'parent' => ['Parent Portal', true],
            'alumni' => ['Alumni Portal', true],
        ];

        foreach ($portals as $code => [$name, $requiresAuthentication]) {
            Portal::query()->updateOrCreate(
                ['code' => $code],
                ['name' => $name, 'requires_authentication' => $requiresAuthentication, 'status' => 'active'],
            );
        }
    }

    private function structureTransport(): void
    {
        $module = Module::query()->where('code', 'transport')->firstOrFail();
        $module->update([
            'short_name' => 'Transport', 'module_type' => 'student_service',
            'supports_company_scope' => true, 'supports_campus_scope' => true, 'supports_institute_scope' => true,
        ]);

        $groups = [
            'fleet-management' => ['Fleet Management', ['vehicles', 'drivers']],
            'route-management' => ['Route Management', ['routes', 'stops', 'students']],
            'daily-operations' => ['Daily Operations', ['tracking', 'complaints']],
            'reports' => ['Reports', ['reports']],
        ];

        foreach ($groups as $position => $definition) {
            [$name, $features] = $definition;
            $group = ModuleFeatureGroup::query()->updateOrCreate(
                ['module_id' => $module->id, 'code' => $position],
                ['name' => $name, 'display_order' => array_search($position, array_keys($groups), true), 'status' => 'active'],
            );
            $module->features()->whereIn('code', $features)->update(['feature_group_id' => $group->id]);
        }

        $module->features()->where('code', 'dashboard')->update(['feature_type' => 'dashboard', 'route_name' => Route::has('transport.dashboard') ? 'transport.dashboard' : null]);
        $module->features()->where('code', 'reports')->update(['feature_type' => 'report', 'route_name' => Route::has('transport.reports.index') ? 'transport.reports.index' : null]);
    }

    private function seedTransportMenus(): void
    {
        $definitions = [
            'site_admin' => ['site-admin-sidebar', [
                ['Transport Dashboard', 'transport.dashboard', 'transport.dashboard.view'],
                ['Routes', 'transport.routes.index', 'transport.routes.view'],
                ['Reports', 'transport.reports.index', 'transport.reports.view'],
            ]],
            'management' => ['management-sidebar', [
                ['Transport Dashboard', 'transport.dashboard', 'transport.dashboard.view'],
                ['Routes', 'transport.routes.index', 'transport.routes.view'],
                ['Transport Reports', 'transport.reports.index', 'transport.reports.view'],
            ]],
            'administration' => ['administration-sidebar', [
                ['Transport Dashboard', 'transport.dashboard', 'transport.dashboard.view'],
                ['Routes', 'transport.routes.index', 'transport.routes.view'],
                ['Student Allocation', 'transport.students.index', 'transport.students.view'],
                ['Reports', 'transport.reports.index', 'transport.reports.view'],
            ]],
            'staff' => ['staff-sidebar', [
                ['Dashboard', 'transport.dashboard', 'transport.dashboard.view'],
                ['Vehicles', 'transport.vehicles.index', 'transport.vehicles.view'],
                ['Routes', 'transport.routes.index', 'transport.routes.view'],
                ['Student Allocation', 'transport.students.index', 'transport.students.view'],
                ['Tracking', 'transport.tracking.index', 'transport.tracking.view'],
                ['Reports', 'transport.reports.index', 'transport.reports.view'],
            ]],
            'parent' => ['parent-sidebar', [
                ["My Child's Route", 'transport.child.route', 'transport.child.view'],
                ['Live Tracking', 'transport.tracking.index', 'transport.tracking.view'],
                ['Raise Complaint', 'transport.complaints.create', 'transport.complaints.create'],
            ]],
            'student' => ['student-sidebar', [
                ['My Route', 'transport.own.route', 'transport.own.view'],
                ['Tracking', 'transport.tracking.index', 'transport.tracking.view'],
            ]],
        ];
        $module = Module::query()->where('code', 'transport')->firstOrFail();

        foreach ($definitions as $portalCode => [$menuCode, $items]) {
            $portal = Portal::query()->where('code', $portalCode)->firstOrFail();
            $menu = MenuSet::query()->updateOrCreate(
                ['tenant_id' => null, 'portal_id' => $portal->id, 'code' => $menuCode, 'menu_type' => 'sidebar'],
                ['name' => $portal->name.' Navigation', 'is_default' => true, 'is_system' => true, 'status' => 'active'],
            );
            $group = MenuItem::query()->updateOrCreate(
                ['menu_set_id' => $menu->id, 'parent_id' => null, 'title' => 'Transport'],
                ['module_id' => $module->id, 'icon' => 'bus', 'display_order' => 10, 'item_type' => 'group', 'is_collapsible' => true, 'is_system' => true, 'status' => 'active'],
            );

            foreach ($items as $position => [$title, $route, $permission]) {
                MenuItem::query()->updateOrCreate(
                    ['menu_set_id' => $menu->id, 'parent_id' => $group->id, 'title' => $title],
                    ['module_id' => $module->id, 'route_name' => Route::has($route) ? $route : null, 'display_order' => $position, 'permission_code' => $permission, 'item_type' => 'link', 'is_system' => true, 'status' => Route::has($route) ? 'active' : 'inactive'],
                );
            }
        }
    }
}
