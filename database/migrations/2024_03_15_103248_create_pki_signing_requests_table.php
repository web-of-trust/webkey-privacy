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
        Schema::create('pki_signing_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('domain_id')->index();
            $table->string('common_name');
            $table->string('country_name');
            $table->string('province_name');
            $table->string('locality_name');
            $table->string('organization_name');
            $table->string('organization_unit_name');
            $table->mediumInteger('key_algorithm');
            $table->mediumInteger('key_strength');
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
        Schema::dropIfExists('pki_signing_requests');
    }
};
