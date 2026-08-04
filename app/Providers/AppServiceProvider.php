<?php

namespace App\Providers;

use App\Core\AcademicYear\AcademicYearContext;
use App\Core\Tenancy\CurrentTenant;
use App\Core\Tenancy\TenantContext;
use App\Domains\Academics\Contracts\AcademicCalendarProvider;
use App\Domains\Academics\Contracts\AcademicContextProvider;
use App\Domains\Academics\Contracts\AcademicNomenclatureProvider;
use App\Domains\Academics\Contracts\ClassSectionProvider;
use App\Domains\Academics\Contracts\ProgrammeSemesterProvider;
use App\Domains\Academics\Contracts\SubjectOfferingProvider;
use App\Domains\Academics\Services\CanonicalAcademicProvider;
use App\Domains\Academics\Services\ContractAcademicNomenclatureProvider;
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
        foreach ([AcademicContextProvider::class, ClassSectionProvider::class, ProgrammeSemesterProvider::class, SubjectOfferingProvider::class, AcademicCalendarProvider::class] as $contract) {
            $this->app->bind($contract, CanonicalAcademicProvider::class);
        }
        $this->app->bind(AcademicNomenclatureProvider::class, ContractAcademicNomenclatureProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
