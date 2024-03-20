<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources\X509SigningRequestResource\RelationManagers;

use App\Filament\Resources\X509CertificateResource;
use App\Models\X509Certificate;
use Filament\Forms\{
    Components\FileUpload,
    Components\Hidden,
    Form,
};
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\{
    Actions\Action,
    Actions\CreateAction,
    Columns\TextColumn,
    Table,
};
use phpseclib3\File\X509;

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
            FileUpload::make('cert_file')->required()->rules([
                fn () => function (
                    string $attribute, mixed $value, \Closure $fail
                ) {
                    $cert = new X509();
                    if ($certInfo = $cert->loadX509($value->get())) {
                        $serialNumber = $certInfo['tbsCertificate']['serialNumber'];
                        $count = X509Certificate::where(
                            'serial_number', $serialNumber->toHex()
                        )->count();
                        if ($count) {
                            $fail(__('Certificate already exists.'));
                        }

                        $fingerprint = $cert->getPublicKey()->getFingerprint('sha256');
                        if ($fingerprint !== $this->ownerRecord->fingerprint) {
                            $fail(__('Public key fingerprint does not match.'));
                        }

                        $cn = $cert->getSubjectDNProp('cn')[0];
                        if ($cn !== $this->ownerRecord->cn) {
                            $fail(__('Subject common name does not match.'));
                        }
                    }
                    else {
                        $fail(__('Certificate is invalid.'));
                    }
                },
            ])->storeFiles(false)->label(__('Certificate File')),
            Hidden::make('domain_id')
                ->default($this->ownerRecord->domain_id),
            Hidden::make('signing_request_id')
                ->default($this->ownerRecord->id),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('subject_dn')->wrap()->label(__('Subject DN')),
            TextColumn::make('issuer_dn')->wrap()->label(__('Issuer DN')),
            TextColumn::make('serial_number')->formatStateUsing(
                fn (string $state): string => strtoupper($state)
            )->label(__('Serial Number')),
            TextColumn::make('not_before')->wrap()->label(__('Not Before')),
            TextColumn::make('not_after')->wrap()->label(__('Not After')),
        ])
        ->headerActions([
            CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $certData = $data['cert_file']->get();
                    $cert = new X509();
                    $certInfo = $cert->loadX509($certData);

                    $notBefore = $certInfo['tbsCertificate']['validity']['notBefore'];
                    $notBefore = isset($notBefore['generalTime']) ? $notBefore['generalTime'] : $notBefore['utcTime'];
                    $notAfter = $certInfo['tbsCertificate']['validity']['notAfter'];
                    $notAfter = isset($notAfter['generalTime']) ? $notAfter['generalTime'] : $notAfter['utcTime'];

                    $data['not_before'] = (new \DateTimeImmutable(
                        $notBefore, new \DateTimeZone('UTC')
                    ))->setTimezone(new \DateTimeZone(config('app.timezone')));
                    $data['not_after'] = (new \DateTimeImmutable(
                        $notAfter, new \DateTimeZone('UTC')
                    ))->setTimezone(new \DateTimeZone(config('app.timezone')));

                    $serialNumber = $certInfo['tbsCertificate']['serialNumber'];
                    $data['serial_number'] = $serialNumber->toHex();

                    $data['subject_dn'] = $cert->getSubjectDN(X509::DN_STRING);
                    $data['issuer_dn'] = $cert->getIssuerDN(X509::DN_STRING);
                    $data['certificate_data'] = $certData;
                    return $data;
                })
                ->createAnother(false)
                ->label(__('New Certificate')),
        ])
        ->actions([
            Action::make('export_cert')->label(__('Export'))
                ->icon('heroicon-m-arrow-down-tray')
                ->action(
                    fn ($record) => X509CertificateResource::exportCertificate($record)
                ),
        ])->recordTitleAttribute('Certificates');
    }
}
