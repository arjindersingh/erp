<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Support\Facades\Blade;

final class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('administration')
            ->path('admin')
            ->login()
            ->passwordReset()
            ->profile()
            ->brandName('Administration')
            ->colors([
                'primary' => '#0f766e',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->pages([])
            ->widgets([])
            ->middleware([
                'web',
                'auth',
                'active-context',
            ]);
    }
}
