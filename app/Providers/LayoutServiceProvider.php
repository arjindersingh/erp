<?php

declare(strict_types=1);

namespace App\Providers;

use App\Core\Layout\InterfaceLayoutResolver;
use App\Core\Settings\InterfacePreferenceResolver;
use Illuminate\Support\ServiceProvider;

final class LayoutServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(InterfaceLayoutResolver::class, function ($app) {
            return new InterfaceLayoutResolver(
                $app->make(InterfacePreferenceResolver::class),
            );
        });
    }

    public function boot(): void
    {
        // optional view composers and shared data here
    }
}
