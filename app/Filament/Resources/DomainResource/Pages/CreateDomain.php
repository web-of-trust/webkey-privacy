<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources\DomainResource\Pages;

use App\Filament\Resources\DomainResource;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Forms\Components\{
    Toggle,
    Textarea,
    TextInput,
};
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;
use OpenPGP\OpenPGP;

/**
 * Create domain record class
 *
 * @package  App
 * @category Filament
 * @author   Nguyen Van Nguyen - nguyennv1981@gmail.com
 */
class CreateDomain extends CreateRecord
{
    protected static string $resource = DomainResource::class;

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->rules([
                    function () {
                        return function (string $attribute, $value, \Closure $fail) {
                            if (filter_var($value, FILTER_VALIDATE_DOMAIN)) {
                                $fail('The domain name is invalid.');
                            }
                        };
                    },
                ])
                ->required()->unique()->label(__('Name')),
            TextInput::make('email')
                ->email()->required()->unique()->label(__('Email Address')),
            TextInput::make('organization')->label(__('Organization')),
            Toggle::make('generate_key')
                ->default(true)->inline(false)->label(__('Generate PGP Key')),
            Textarea::make('description')->columnSpan(2)->label(__('Description')),
        ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($data['generate_key']) {
            $passphase = Str::random();
            $privateKey = OpenPGP::generateKey(
                [$data['email']],
                $passphase,
                KeyType::Ecc,
                curve: CurveOid::Ed25519
            );
        }
        return $data;
    }

    protected function afterValidate(): void
    {
        $data = $this->form->getState();
        if (!Str::endsWith($data['email'], $data['name'])) {
            Notification::make()
                ->warning()
                ->title(__('The email address is invalid!'))
                ->body(__('The email address must match the domain name.'))
                ->send();
            $this->halt();
        }
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return __('Domain has been created!');
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
