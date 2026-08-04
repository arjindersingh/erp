<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;

final class TeacherPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('teacher')
            ->path('teacher')
            ->login()
            ->brandName('Teacher Portal')
            ->colors(['primary' => '#7c3aed'])
            ->pages([])
            ->widgets([])
            ->middleware(['web', 'auth', 'active-context']);
    }
}
