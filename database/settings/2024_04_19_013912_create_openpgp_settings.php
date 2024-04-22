<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('openpgp.password_store', 'local');
        $this->migrator->add('openpgp.password_length', 32);
        $this->migrator->add('openpgp.preferred_hash', '');
        $this->migrator->add('openpgp.preferred_symmetric', '');
        $this->migrator->add('openpgp.key_type', '');
        $this->migrator->add('openpgp.elliptic_curve', '');
        $this->migrator->add('openpgp.rsa_key_size', '');
        $this->migrator->add('openpgp.dh_key_size', '');
    }
};
