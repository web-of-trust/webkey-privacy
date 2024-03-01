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
            $table->string('key_id', 16)->unique();
            $table->tinyInteger('key_algorithm');
            $table->mediumInteger('key_strength');
            $table->tinyInteger('key_version');
            $table->text('key_data')->nullable();
            $table->string('primary_user', 1024);
            $table->string('certify_by', 64)->nullable();
            $table->timestamp('creation_time')->nullable()->default(null);
            $table->timestamp('expiration_time')->nullable()->default(null);
            $table->timestamps();
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
