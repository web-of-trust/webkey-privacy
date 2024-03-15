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
        Schema::create('pki_certificates', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('domain_id');
            $table->unsignedInteger('pki_csr_id');
            $table->string('subject_common_name');
            $table->string('issuer_common_name');
            $table->timestamp('not_before')->nullable();
            $table->timestamp('not_after')->nullable();
            $table->string('fingerprint');
            $table->text('certificate_data')->nullable();
            $table->timestamps();
            $table->index(['domain_id', 'pki_csr_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pki_certificates');
    }
};
