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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('domain_id');
            $table->string('fingerprint', 64)->unique();
            $table->string('key_id', 16)->index();
            $table->string('wkd_hash', 32);
            $table->tinyInteger('key_algorithm');
            $table->mediumInteger('key_strength');
            $table->tinyInteger('key_version');
            $table->boolean('is_revoked')->default(false);
            $table->string('primary_user', 1024);
            $table->text('key_data')->nullable();
            $table->timestamp('creation_time')->nullable();
            $table->timestamp('expiration_time')->nullable();
            $table->timestamps();
            $table->index(['domain_id', 'wkd_hash']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
