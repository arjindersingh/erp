<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Core\Modules\Module;
use App\Core\Modules\ModuleFeature;
use Illuminate\Database\Seeder;

class CoreModuleSeeder extends Seeder
{
    public function run(): void
    {
        $catalogue = [
            'core' => ['Core', true, ['dashboard', 'settings']],
            'organization' => ['Organisation', true, ['companies', 'campuses', 'institutes']],
            'access' => ['Users and Access', true, ['users', 'roles', 'permissions', 'overrides', 'audit']],
            'audit' => ['Audit and Security', true, ['logs', 'security', 'retention', 'legal_holds', 'alerts', 'integrity', 'archives', 'impersonation']],
            'academics' => ['Academic Foundation', true, ['dashboard', 'years', 'levels', 'authorities', 'affiliations', 'nomenclature', 'structure']],
            'admissions' => ['Admissions', false, ['campaigns', 'applications', 'manual_entry', 'scrutiny', 'assessments', 'merit', 'seats', 'selection', 'offers', 'conversion', 'reports']],
            'transport' => ['Transport', false, ['dashboard', 'vehicles', 'drivers', 'routes', 'stops', 'students', 'tracking', 'complaints', 'reports', 'child', 'own']],
        ];

        foreach ($catalogue as $order => $definition) {
            [$name, $isCore, $features] = $definition;
            $module = Module::query()->updateOrCreate(
                ['code' => $order],
                ['name' => $name, 'display_order' => array_search($order, array_keys($catalogue), true), 'is_core' => $isCore, 'status' => 'active'],
            );

            foreach ($features as $position => $feature) {
                ModuleFeature::query()->updateOrCreate(
                    ['module_id' => $module->id, 'code' => $feature],
                    ['name' => str($feature)->headline()->toString(), 'display_order' => $position, 'status' => 'active'],
                );
            }
        }
    }
}
