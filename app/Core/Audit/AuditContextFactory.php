<?php

declare(strict_types=1);

namespace App\Core\Audit;

use App\Core\Tenancy\CurrentTenant;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final class AuditContextFactory
{
    public function __construct(
        private readonly CurrentTenant $currentTenant,
        private readonly AuthFactory $auth,
    ) {}

    public function make(?Request $request = null): AuditContext
    {
        $request ??= app()->runningInConsole() ? null : request();
        $user = $this->auth->guard()->user();
        $trusted = $request?->attributes->get('audit_context', []);
        $trusted = is_array($trusted) ? $trusted : [];
        $source = app()->runningInConsole()
            ? AuditSource::Console
            : ($request?->is('api/*') ? AuditSource::Api : AuditSource::Web);
        $requestId = $request?->attributes->get('request_id');

        return new AuditContext(
            tenantId: $this->intOrNull($trusted['tenant_id'] ?? $this->currentTenant->id()),
            companyId: $this->intOrNull($trusted['company_id'] ?? null),
            campusId: $this->intOrNull($trusted['campus_id'] ?? null),
            instituteId: $this->intOrNull($trusted['institute_id'] ?? null),
            academicYearId: $this->intOrNull($trusted['academic_year_id'] ?? null),
            actorType: $user === null ? AuditActorType::System : AuditActorType::User,
            actorId: $this->intOrNull($user?->getAuthIdentifier()),
            userId: $this->intOrNull($user?->getAuthIdentifier()),
            personId: $this->intOrNull($trusted['person_id'] ?? null),
            membershipId: $this->intOrNull($trusted['membership_id'] ?? null),
            accessScopeId: $this->intOrNull($trusted['access_scope_id'] ?? null),
            roleAssignmentId: $this->intOrNull($trusted['role_assignment_id'] ?? null),
            roleId: $this->intOrNull($trusted['role_id'] ?? null),
            portalId: $this->intOrNull($trusted['portal_id'] ?? null),
            sessionId: $request?->hasSession() ? $request->session()->getId() : null,
            requestId: is_string($requestId) ? $requestId : (string) Str::uuid(),
            correlationId: $this->stringOrNull($trusted['correlation_id'] ?? null),
            batchId: $this->stringOrNull($trusted['batch_id'] ?? null),
            jobId: $this->stringOrNull($trusted['job_id'] ?? null),
            apiTokenId: $this->intOrNull($trusted['api_token_id'] ?? null),
            source: $source,
            routeName: $request?->route()?->getName(),
            requestMethod: $request?->method(),
            requestUrl: $request?->url(),
            ipAddress: $request?->ip(),
            forwardedIp: null,
            userAgent: $request?->userAgent(),
        );
    }

    private function intOrNull(mixed $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }

    private function stringOrNull(mixed $value): ?string
    {
        return is_string($value) && $value !== '' ? $value : null;
    }
}
