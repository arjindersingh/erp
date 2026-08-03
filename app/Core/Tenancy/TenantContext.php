<?php

declare(strict_types=1);

namespace App\Core\Tenancy;

use LogicException;

class TenantContext
{
    public function __construct(
        protected ?Tenant $tenant = null,
        protected ?TenantDomain $domain = null,
    ) {}

    public function activate(Tenant $tenant, ?TenantDomain $domain = null): void
    {
        if ($domain !== null && (int) $domain->tenant_id !== (int) $tenant->getKey()) {
            throw new LogicException('Tenant domain does not belong to the tenant being activated.');
        }

        $this->tenant = $tenant;
        $this->domain = $domain;
    }

    public function set(?Tenant $tenant, ?TenantDomain $domain = null): void
    {
        if ($tenant === null) {
            $this->clear();

            return;
        }

        $this->activate($tenant, $domain);
    }

    public function clear(): void
    {
        $this->tenant = null;
        $this->domain = null;
    }

    public function tenant(): ?Tenant
    {
        return $this->tenant;
    }

    public function domain(): ?TenantDomain
    {
        return $this->domain;
    }

    public function id(): ?int
    {
        return $this->tenant?->getKey();
    }

    public function hasTenant(): bool
    {
        return $this->tenant !== null;
    }

    public function requireTenant(): Tenant
    {
        return $this->tenant ?? throw new LogicException('No tenant is active in the current execution context.');
    }
}
