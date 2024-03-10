<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources\DomainResource\Pages;

use App\Filament\Resources\DomainResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\{
    IconColumn,
    TextColumn,
};
use Filament\Tables\Table;

/**
 * List domain record page
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class ListDomains extends ListRecords
{
    protected static string $resource = DomainResource::class;

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label(__('Name')),
            TextColumn::make('email')->label(__('Email')),
            TextColumn::make('organization')->label(__('Organization')),
            IconColumn::make('has_key')
                ->state(
                    static fn ($record): bool => !empty($record->key_data)
                )->boolean()->label(__('Has PGP Key')),
        ])->actions([
            EditAction::make(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
