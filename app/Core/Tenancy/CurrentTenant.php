<?php

namespace App\Core\Tenancy;

class CurrentTenant
{
    public function __construct(
        protected ?Tenant $tenant = null,
        protected ?TenantDomain $domain = null,
    ) {}

    public function set(?Tenant $tenant, ?TenantDomain $domain = null): void
    {
        $this->tenant = $tenant;
        $this->domain = $domain;
    }

    public function clear(): void
    {
        $this->set(null);
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
        return $this->tenant?->id;
    }

    public function hasTenant(): bool
    {
        return $this->tenant !== null;
    }
}
