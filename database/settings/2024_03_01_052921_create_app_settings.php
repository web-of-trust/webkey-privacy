<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('app_settings.passphrase_store', '');
        $this->migrator->add('app_settings.passphrase_length', 0);
        $this->migrator->add('app_settings.key_type', '');
        $this->migrator->add('app_settings.elliptic_curve', '');
        $this->migrator->add('app_settings.rsa_key_size', '');
        $this->migrator->add('app_settings.dh_key_size', '');
    }
};
