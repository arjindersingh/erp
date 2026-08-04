<?php

namespace App\Providers;

use App\Core\AcademicYear\AcademicYearContext;
use App\Core\Attribution\ActorContext;
use App\Core\Attribution\ActorContextResolver;
use App\Core\Platform\DomainEventRecorder;
use App\Core\Platform\OutboxProcessor;
use App\Core\Platform\ReferenceDataImportService;
use App\Core\Platform\ReferenceDataResolver;
use App\Core\Platform\ReferenceMappingService;
use App\Core\Platform\ReferenceValueValidator;
use App\Core\Platform\TransactionalOutboxWriter;
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
        $this->app->singleton(ActorContextResolver::class);
        $this->app->singleton(ActorContext::class, fn ($app): ActorContext => $app->make(ActorContextResolver::class)->resolve());
        $this->app->singleton(ReferenceDataResolver::class);
        $this->app->singleton(ReferenceValueValidator::class);
        $this->app->singleton(ReferenceMappingService::class);
        $this->app->singleton(ReferenceDataImportService::class);
        $this->app->singleton(DomainEventRecorder::class);
        $this->app->singleton(TransactionalOutboxWriter::class);
        $this->app->singleton(OutboxProcessor::class);
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
