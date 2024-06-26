<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('openpgp_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('domain_id')->index();
            $table->string('fingerprint', 64)->unique();
            $table->string('key_id', 16)->index();
            $table->string('wkd_hash', 32)->index();
            $table->tinyInteger('key_algorithm');
            $table->mediumInteger('key_strength');
            $table->tinyInteger('key_version');
            $table->boolean('is_revoked')->default(false);
            $table->string('primary_user', 1024);
            $table->text('key_data')->nullable();
            $table->timestamp('creation_time')->nullable();
            $table->timestamp('expiration_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('openpgp_certificates');
    }
};
