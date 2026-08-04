<?php

declare(strict_types=1);

namespace Tests\Feature\Foundation\Tenancy;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('foundation')]
#[Group('tenancy')]
#[Group('security')]
final class DataOwnershipAuditCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_registered_tenant_models_pass_the_ownership_audit(): void
    {
        $this->artisan('erp:data-ownership-audit')
            ->expectsOutputToContain('PASS  companies contains tenant_id')
            ->expectsOutputToContain('PASS  App\\Core\\Organization\\Institute uses TenantScope')
            ->assertSuccessful();
    }
}
