<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources;

use App\Filament\Resources\DomainResource\Pages;
use App\Models\Domain;
use App\Settings\AppSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Actions;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use OpenPGP\OpenPGP;

/**
 * Domain resource
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class DomainResource extends Resource
{
    protected static ?string $model = Domain::class;
    protected static ?string $slug = 'domain';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function getNavigationLabel(): string
    {
        return __('Domain Manager');
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label(__('Name')),
            TextColumn::make('email')->label(__('Email')),
            TextColumn::make('organization')->label(__('Organization')),
        ])->actions([
            Actions\EditAction::make(),
        ])->bulkActions([
            Actions\BulkActionGroup::make([
                Actions\DeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDomains::route('/'),
            'create' => Pages\CreateDomain::route('/create'),
            'edit' => Pages\EditDomain::route('/{record}/edit'),
        ];
    }

    public static function generateKey(string $domain, string $email): string
    {
        $settings = app(AppSettings::class);
        $passphase = Str::random();

        Storage::put(
            $settings->passphraseRepo() . '/' . $domain,
            Crypt::encryptString($passphase)
        );
        return OpenPGP::generateKey(
            [$email],
            $passphase,
            $settings->preferredKeyType(),
            curve: $settings->preferredEcc(),
            rsaKeySize: $settings->preferredRsaSize(),
            dhKeySize: $settings->preferredDhSize(),
        )->armor();
    }
}
