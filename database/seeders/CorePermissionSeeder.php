<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Core\Authorization\Permission;
use App\Core\Authorization\PermissionType;
use App\Core\Modules\Module;
use Illuminate\Database\Seeder;

class CorePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $codes = [
            'core' => ['core.dashboard.view', 'core.settings.view', 'core.settings.update'],
            'organization' => [
                'organization.companies.view', 'organization.companies.create', 'organization.companies.update',
                'organization.campuses.view', 'organization.campuses.create', 'organization.campuses.update',
                'organization.institutes.view', 'organization.institutes.create', 'organization.institutes.update',
            ],
            'access' => [
                'access.users.view', 'access.users.create', 'access.users.update', 'access.users.suspend',
                'access.roles.view', 'access.roles.create', 'access.roles.update', 'access.roles.delete',
                'access.permissions.view', 'access.permissions.assign', 'access.permissions.revoke',
                'access.overrides.view', 'access.overrides.grant', 'access.overrides.revoke',
                'access.audit.view', 'access.diagnostics.use',
            ],
            'audit' => [
                'audit.logs.view', 'audit.logs.view_sensitive', 'audit.logs.export', 'audit.logs.download_export',
                'audit.security.view', 'audit.security.acknowledge', 'audit.retention.view', 'audit.retention.manage',
                'audit.legal_holds.view', 'audit.legal_holds.manage', 'audit.alerts.view', 'audit.alerts.manage',
                'audit.alerts.acknowledge', 'audit.integrity.view', 'audit.integrity.verify',
                'audit.archives.view', 'audit.archives.restore', 'audit.impersonation.view',
            ],
            'academics' => [
                'academics.access', 'academics.dashboard.view',
                'academics.years.view', 'academics.years.create', 'academics.years.update', 'academics.years.activate',
                'academics.years.lock', 'academics.years.unlock', 'academics.years.close', 'academics.years.archive', 'academics.years.clone',
                'academics.levels.view', 'academics.levels.create', 'academics.levels.update', 'academics.levels.archive',
                'academics.authorities.view', 'academics.authorities.create', 'academics.authorities.update',
                'academics.affiliations.view', 'academics.affiliations.create', 'academics.affiliations.verify', 'academics.affiliations.approve',
                'academics.structure.view', 'academics.structure.manage', 'academics.structure.clone', 'academics.structure.validate',
                'academics.structure.publish', 'academics.structure.lock', 'academics.structure.unlock',
                'academics.programmes.view', 'academics.programmes.manage', 'academics.classes.view',
                'academics.classes.manage', 'academics.sections.manage', 'academics.subjects.manage',
                'academics.class_subjects.manage', 'academics.programme_subjects.manage',
                'academics.subject_offerings.manage',
            ],
            'transport' => [
                'transport.access', 'transport.dashboard.view',
                'transport.vehicles.view', 'transport.vehicles.create', 'transport.vehicles.update', 'transport.vehicles.retire',
                'transport.routes.view', 'transport.routes.create', 'transport.routes.update', 'transport.routes.approve',
                'transport.students.view', 'transport.students.assign', 'transport.students.remove',
                'transport.child.view', 'transport.own.view', 'transport.tracking.view',
                'transport.complaints.create', 'transport.complaints.manage',
                'transport.reports.view', 'transport.reports.export',
            ],
        ];

        foreach ($codes as $moduleCode => $permissions) {
            $module = Module::query()->where('code', $moduleCode)->firstOrFail();

            foreach ($permissions as $code) {
                $segments = explode('.', $code);
                $command = end($segments);
                $featureCode = count($segments) === 3 ? $segments[1] : null;
                $feature = $featureCode === null ? null : $module->features()->where('code', $featureCode)->first();
                $type = match ($command) {
                    'access' => PermissionType::Module,
                    'approve' => PermissionType::Approval,
                    'export' => PermissionType::Report,
                    default => PermissionType::Command,
                };

                Permission::query()->updateOrCreate(
                    ['code' => $code, 'guard_name' => 'web'],
                    [
                        'module_id' => $module->id,
                        'module_feature_id' => $feature?->id,
                        'name' => $code,
                        'command' => $command,
                        'permission_type' => $type,
                        'is_system' => true,
                        'status' => 'active',
                    ],
                );
            }
        }
    }
}
