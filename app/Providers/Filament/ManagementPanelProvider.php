<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;

final class ManagementPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('management')
            ->path('management')
            ->login()
            ->brandName('Management Portal')
            ->colors(['primary' => '#4f46e5'])
            ->pages([])
            ->widgets([])
            ->middleware(['web', 'auth', 'active-context']);
    }
}
