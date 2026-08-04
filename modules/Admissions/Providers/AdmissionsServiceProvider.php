<?php

declare(strict_types=1);

namespace Modules\Admissions\Providers;

use App\Support\Modules\ModuleServiceProvider;

final class AdmissionsServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Admissions';

    protected string $basePath = __DIR__.'/..';
}
