<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('app_settings.passphrase_store', 'key_vault');
        $this->migrator->add('app_settings.passphrase_length', 32);
        $this->migrator->add('app_settings.key_type', 'rsa');
        $this->migrator->add('app_settings.elliptic_curve', 'secp521r1');
        $this->migrator->add('app_settings.rsa_key_size', 's2048');
        $this->migrator->add('app_settings.dh_key_size', 'l2048_n224');
    }
};
