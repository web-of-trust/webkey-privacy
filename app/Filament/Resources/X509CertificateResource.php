<?php

namespace App\Filament\Resources;

use App\Filament\Resources\X509CertificateResource\Pages;
use App\Filament\Resources\X509CertificateResource\RelationManagers;
use App\Models\X509Certificate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class X509CertificateResource extends Resource
{
    protected static ?string $model = X509Certificate::class;
    protected static ?string $navigationGroup = 'X509';
    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $slug = 'x509/certificate';


    public static function getNavigationLabel(): string
    {
        return __('Certificates');
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
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListX509Certificates::route('/'),
            'create' => Pages\CreateX509Certificate::route('/create'),
            'view' => Pages\ViewX509Certificate::route('/{record}'),
        ];
    }
}
