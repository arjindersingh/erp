<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Core\Audit\AuditEventDefinition;
use Illuminate\Database\Seeder;

class AuditFoundationSeeder extends Seeder
{
    public function run(): void
    {
        $definitions = [
            'auth.login.succeeded' => ['authentication', 'login_succeeded', 'info', 'User signed in', false, false],
            'access.role.assigned' => ['authorization', 'role_assigned', 'notice', 'Role assigned to user', true, false],
            'context.scope.switched' => ['authorization', 'scope_access_changed', 'notice', 'Active scope changed', false, false],
            'admissions.application.approved' => ['workflow', 'approved', 'notice', 'Student admission approved', false, false],
            'fees.receipt.cancelled' => ['financial', 'cancelled', 'high', 'Fee receipt cancelled', true, true],
            'examination.marks.corrected' => ['academic', 'updated', 'high', 'Examination marks corrected', true, true],
            'payroll.run.finalized' => ['human_resource', 'finalized', 'high', 'Payroll finalized', true, true],
            'tenant.module.disabled' => ['configuration', 'module_disabled', 'warning', 'Tenant module disabled', true, false],
            'audit.export.requested' => ['export', 'export_requested', 'high', 'Audit export requested', true, true],
            'security.impersonation.started' => ['impersonation', 'created', 'critical', 'Site administrator impersonation started', true, true],
            'security.cross_tenant.denied' => ['security', 'permission_denied', 'high', 'Cross-tenant access denied', true, true],
            'academic.structure.cloned' => ['academic', 'cloned', 'notice', 'Academic structure cloned', true, false],
            'academic.structure.published' => ['academic', 'published', 'high', 'Academic structure published', true, false],
            'academic.year.locked' => ['academic', 'locked', 'high', 'Academic year locked', true, false],
            'academic.year.unlocked' => ['academic', 'unlocked', 'high', 'Academic year unlocked', true, true],
        ];

        foreach ($definitions as $eventCode => [$category, $action, $severity, $title, $security, $sensitive]) {
            AuditEventDefinition::query()->updateOrCreate(
                ['event_code' => $eventCode],
                [
                    'category' => $category, 'action' => $action, 'default_severity' => $severity,
                    'title_template' => $title, 'is_security_event' => $security, 'is_sensitive' => $sensitive,
                    'is_required' => true, 'status' => 'active',
                ],
            );
        }
    }
}
