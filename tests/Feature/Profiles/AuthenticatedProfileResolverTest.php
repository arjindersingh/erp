<?php

declare(strict_types=1);

namespace Tests\Feature\Profiles;

use App\Core\Authentication\AuthenticatedProfileResolver;
use App\Core\Tenancy\Tenant;
use App\Models\User;
use Database\Seeders\UatDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AuthenticatedProfileResolverTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_resolution_is_bound_to_user_and_tenant(): void
    {
        putenv('UAT_TEMP_PASSWORD=Uat-Test-Password-2027!');
        $this->seed();
        $this->seed(UatDemoSeeder::class);
        $user = User::query()->where('email', 'admissions.uat-a@erp-uat.test')->firstOrFail();
        $tenantA = Tenant::query()->where('code', 'UAT-A')->firstOrFail();
        $tenantB = Tenant::query()->where('code', 'UAT-B')->firstOrFail();
        $resolver = app(AuthenticatedProfileResolver::class);
        $this->assertSame('Reena Sharma', $resolver->resolveFor($user, $tenantA)->person?->display_name);
        $this->assertNull($resolver->resolveFor($user, $tenantB)->person);
    }
}
