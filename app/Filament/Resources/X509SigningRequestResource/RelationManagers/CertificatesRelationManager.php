<?php declare(strict_types=1);
/**
 * This file is part of the Webkey Privacy project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Filament\Resources\X509SigningRequestResource\RelationManagers;

use App\Models\X509Certificate;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
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
                fn (): \Closure => function (
                    string $attribute, mixed $value, \Closure $fail
                ) {
                    $cert = new X509();
                    $certInfo = $cert->loadX509($value->get());

                    $serialNumber = $certInfo['tbsCertificate']['serialNumber'];
                    X509Certificate::where(
                        'serial_number', $serialNumber->toHex()
                    )->firstOr(function () use ($fail) {
                        $fail(__('Certificate already exists.'));
                    });

                    $csr = new X509();
                    $csr->loadCSR($this->ownerRecord->csr_data);

                    $certFp = $cert->getPublicKey()->getFingerprint();
                    $certCn = $cert->getSubjectDNProp('cn')[0];
                    $csrFp = $csr->getPublicKey()->getFingerprint();
                    $csrCn = $csr->getDNProp('cn')[0];

                    $invalid = ($certFp !== $csrFp) || ($certCn !== $csrCn);
                    if ($invalid) {
                        $fail(__('Certificate file is invalid.'));
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
            TextColumn::make('subject_cn')->label(__('Subject Common Name')),
            TextColumn::make('issuer_cn')->label(__('Issuer Common Name')),
            TextColumn::make('serial_number')->label(__('Serial Number')),
            TextColumn::make('not_before')->label(__('Not Before')),
            TextColumn::make('not_after')->label(__('Not After')),
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

                    $data['not_before'] = new \DateTimeImmutable(
                        $notBefore, new \DateTimeZone(@date_default_timezone_get())
                    );
                    $data['not_after'] = new \DateTimeImmutable(
                        $notAfter, new \DateTimeZone(@date_default_timezone_get())
                    );

                    $serialNumber = $certInfo['tbsCertificate']['serialNumber'];
                    $data['serial_number'] = $serialNumber->toHex();

                    $data['subject_cn'] = $cert->getSubjectDNProp('cn')[0];
                    $data['issuer_cn'] = $cert->getIssuerDNProp('cn')[0];
                    $data['fingerprint'] = $cert->getPublicKey()->getFingerprint();
                    $data['certificate_data'] = $certData;
                    return $data;
                })
                ->createAnother(false)
                ->label(__('New Certificate')),
        ])
        ->actions([
        ])->recordTitleAttribute('Certificates');
    }
}
