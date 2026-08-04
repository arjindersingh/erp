<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admissions;

use App\Domains\Admissions\Models\AdmissionApplication;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class AdmissionApplicationResource extends Resource
{
    protected static ?string $model = AdmissionApplication::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Admissions';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('uuid')->label('Reference')->disabled(),
            TextInput::make('status')->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('uuid')->label('Reference'),
                TextColumn::make('status'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\Admissions\Pages\ListAdmissionApplications::route('/'),
        ];
    }
}
