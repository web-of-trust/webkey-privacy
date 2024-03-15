<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PkiCertificateResource\Pages;
use App\Filament\Resources\PkiCertificateResource\RelationManagers;
use App\Models\PkiCertificate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PkiCertificateResource extends Resource
{
    protected static ?string $model = PkiCertificate::class;
    protected static ?string $navigationGroup = 'X509';
    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $slug = 'pki/certificate';


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
            'index' => Pages\ListPkiCertificates::route('/'),
            'create' => Pages\CreatePkiCertificate::route('/create'),
            'view' => Pages\ViewPkiCertificate::route('/{record}'),
        ];
    }
}
