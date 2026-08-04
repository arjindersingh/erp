<?php

declare(strict_types=1);

namespace App\Filament\Resources\Students;

use App\Domains\Students\Models\StudentProfile;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class StudentProfileResource extends Resource
{
    protected static ?string $model = StudentProfile::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Students';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('student_number')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('student_number'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\Students\Pages\ListStudentProfiles::route('/'),
        ];
    }
}
