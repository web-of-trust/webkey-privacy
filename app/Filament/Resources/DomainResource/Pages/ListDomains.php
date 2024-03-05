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
use Filament\Tables\Actions\{
    BulkActionGroup,
    DeleteBulkAction,
    EditAction,
};
use Filament\Tables\Columns\TextColumn;
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
        ])->actions([
            EditAction::make(),
        ])->bulkActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
