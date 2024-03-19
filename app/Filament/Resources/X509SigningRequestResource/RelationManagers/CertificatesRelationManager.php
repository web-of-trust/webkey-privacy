<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources\X509SigningRequestResource\RelationManagers;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

/**
 * X509 certificates relation manager
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class CertificatesRelationManager extends RelationManager
{
    protected static string $relationship = 'certificates';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            FileUpload::make('certificate')->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('subject_cn')->label(__('Subject Common Name')),
            TextColumn::make('issuer_cn')->label(__('Issuer Common Name')),
            TextColumn::make('fingerprint')->label(__('Fingerprint')),
            TextColumn::make('not_before')->label(__('Not Before')),
            TextColumn::make('not_after')->label(__('Not After')),
        ])
        ->headerActions([
            CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    return $data;
                })
                ->createAnother(false)
                ->label(__('New Certificate')),
        ])
        ->actions([
        ])->recordTitleAttribute('Certificates');
    }
}
