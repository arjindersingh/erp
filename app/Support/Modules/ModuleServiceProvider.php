<?php

namespace App\Support\Modules;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

abstract class ModuleServiceProvider extends ServiceProvider
{
    protected string $name;

    protected string $basePath;

    public function boot(): void
    {
        $this->loadModuleRoutes();
        $this->loadModuleMigrations();
        $this->loadModuleViews();
        $this->loadModuleTranslations();
    }

    protected function modulePath(string $path = ''): string
    {
        return rtrim($this->basePath, DIRECTORY_SEPARATOR)
            .($path === '' ? '' : DIRECTORY_SEPARATOR.ltrim($path, DIRECTORY_SEPARATOR));
    }

    protected function moduleKey(): string
    {
        return Str::kebab($this->name);
    }

    protected function loadModuleRoutes(): void
    {
        $routes = $this->modulePath('routes/web.php');

        if (is_file($routes)) {
            $this->loadRoutesFrom($routes);
        }
    }

    protected function loadModuleMigrations(): void
    {
        $migrations = $this->modulePath('database/migrations');

        if (is_dir($migrations)) {
            $this->loadMigrationsFrom($migrations);
        }
    }

    protected function loadModuleViews(): void
    {
        $views = $this->modulePath('resources/views');

        if (is_dir($views)) {
            $this->loadViewsFrom($views, $this->moduleKey());
        }
    }

    protected function loadModuleTranslations(): void
    {
        $translations = $this->modulePath('resources/lang');

        if (is_dir($translations)) {
            $this->loadTranslationsFrom($translations, $this->moduleKey());
        }
    }
}
