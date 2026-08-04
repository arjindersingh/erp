<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admissions;

use App\Domains\Admissions\Models\AdmissionCampaign;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class AdmissionCampaignResource extends Resource
{
    protected static ?string $model = AdmissionCampaign::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Admissions';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('description')->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('status')->sortable(),
            ])
            ->actions([
                Action::make('view')->label('Open')->url(fn (AdmissionCampaign $record): string => route('admissions.public.apply', $record)),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\Admissions\Pages\ListAdmissionCampaigns::route('/'),
        ];
    }
}
