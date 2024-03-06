<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonalKeyResource\Pages;
use App\Filament\Resources\PersonalKeyResource\RelationManagers;
use App\Models\PersonalKey;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PersonalKeyResource extends Resource
{
    protected static ?string $model = PersonalKey::class;
    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $slug = 'personal-key';

    public static function getNavigationLabel(): string
    {
        return __('Personal Keys');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPersonalKeys::route('/'),
            'view' => Pages\ViewPersonalKey::route('/{record}'),
        ];
    }
}
