<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_name', 'Webkey Privacy');
        $this->migrator->add('general.timezone', 'UTC');
        $this->migrator->add('general.locale', 'en');
    }
};
