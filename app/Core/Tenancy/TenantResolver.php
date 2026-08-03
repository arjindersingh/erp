<?php

namespace App\Core\Tenancy;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TenantResolver
{
    public function resolvePublicTenant(Request $request): ?TenantDomain
    {
        if (! config('tenancy.public_resolution.enabled', true)) {
            return null;
        }

        return match (config('tenancy.public_resolution.strategy', 'domain')) {
            'domain' => $this->resolveByDomain($request),
            default => null,
        };
    }

    public function isCentralDomain(Request $request): bool
    {
        $host = $this->normalizeHost($request->getHost());

        return in_array($host, $this->centralDomains(), true);
    }

    protected function resolveByDomain(Request $request): ?TenantDomain
    {
        $host = $this->normalizeHost($request->getHost());

        return TenantDomain::query()
            ->with('tenant')
            ->where('domain', $host)
            ->first();
    }

    protected function normalizeHost(string $host): string
    {
        return Str::of($host)
            ->lower()
            ->trim()
            ->trim('.')
            ->toString();
    }

    /**
     * @return list<string>
     */
    protected function centralDomains(): array
    {
        return array_map(
            fn (string $domain) => $this->normalizeHost($domain),
            config('tenancy.public_resolution.central_domains', [])
        );
    }
}
