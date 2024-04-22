<?php

use App\Enums\Role as RoleEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Contracts\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableNames = config('permission.table_names');
        if (Schema::hasTable($tableNames['roles'])) {
            foreach (RoleEnum::cases() as $role) {
                app(Role::class)->findOrCreate($role->value, 'web');
            }
        }
    }
};
