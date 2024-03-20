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
        Schema::create('x509_signing_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('domain_id')->index();
            $table->string('cn')->index();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('locality')->nullable();
            $table->string('organization')->nullable();
            $table->string('organization_unit')->nullable();
            $table->string('fingerprint');
            $table->mediumInteger('key_algorithm');
            $table->mediumInteger('key_strength');
            $table->boolean('with_password')->default(false);
            $table->text('key_data')->nullable();
            $table->text('csr_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('x509_signing_requests');
    }
};
