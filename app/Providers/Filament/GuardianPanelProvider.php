<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;

final class GuardianPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('guardian')
            ->path('guardian')
            ->login()
            ->brandName('Guardian Portal')
            ->colors(['primary' => '#b45309'])
            ->pages([])
            ->widgets([])
            ->middleware(['web', 'auth', 'active-context']);
    }
}
