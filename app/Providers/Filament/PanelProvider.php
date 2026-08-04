<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;

final class PanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('administration')
            ->path('admin')
            ->brandName('Administration')
            ->pages([])
            ->widgets([])
            ->middleware(['web', 'auth', 'active-context']);
    }
}
