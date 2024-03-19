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
        Schema::create('x509_certificates', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('domain_id')->index();
            $table->unsignedInteger('signing_request_id')->index();
            $table->string('serial_number')->unique();
            $table->string('subject_cn');
            $table->string('issuer_cn');
            $table->timestamp('not_before')->nullable();
            $table->timestamp('not_after')->nullable();
            $table->text('certificate_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('x509_certificates');
    }
};
