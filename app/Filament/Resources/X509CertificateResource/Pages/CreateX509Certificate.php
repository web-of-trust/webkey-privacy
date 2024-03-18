<?php

namespace App\Filament\Resources\X509CertificateResource\Pages;

use App\Filament\Resources\X509CertificateResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateX509Certificate extends CreateRecord
{
    protected static string $resource = X509CertificateResource::class;
}
