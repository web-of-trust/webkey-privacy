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
        Schema::create('revocations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('certificate_id');
            $table->string('revoke_by', 64);
            $table->tinyInteger('reason');
            $table->string('description', 1024);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revocations');
    }
};
