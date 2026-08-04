<?php

namespace App\Providers;

use App\Core\AcademicYear\AcademicYearContext;
use App\Core\Tenancy\CurrentTenant;
use App\Core\Tenancy\TenantContext;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(CurrentTenant::class);
        $this->app->alias(CurrentTenant::class, TenantContext::class);
        $this->app->scoped(AcademicYearContext::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
