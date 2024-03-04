<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('app_settings.passphrase_repo', '');
        $this->migrator->add('app_settings.passphase_length', '');
        $this->migrator->add('app_settings.preferred_key_type', '');
        $this->migrator->add('app_settings.preferred_ecc', '');
        $this->migrator->add('app_settings.preferred_rsa_size', '');
        $this->migrator->add('app_settings.preferred_dh_size', '');
    }
};
