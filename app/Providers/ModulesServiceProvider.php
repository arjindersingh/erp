<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ModulesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        foreach (config('modules.enabled', []) as $module) {
            $provider = config("modules.modules.{$module}.provider");

            if (is_string($provider) && class_exists($provider)) {
                $this->app->register($provider);
            }
        }
    }
}
