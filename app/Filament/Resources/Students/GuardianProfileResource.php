<?php

declare(strict_types=1);

namespace App\Filament\Resources\Students;

use App\Domains\Students\Models\GuardianProfile;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class GuardianProfileResource extends Resource
{
    protected static ?string $model = GuardianProfile::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Students';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('guardian_number')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('guardian_number'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\Students\Pages\ListGuardianProfiles::route('/'),
        ];
    }
}
