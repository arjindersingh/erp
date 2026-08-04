<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;

final class StudentPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('student')
            ->path('student')
            ->login()
            ->brandName('Student Portal')
            ->colors(['primary' => '#0f766e'])
            ->pages([])
            ->widgets([])
            ->middleware(['web', 'auth', 'active-context']);
    }
}
