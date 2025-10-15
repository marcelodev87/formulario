<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_log_internal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internal_user_id')->constrained('internal_users')->cascadeOnDelete();
            $table->string('entity');
            $table->unsignedBigInteger('entity_id');
            $table->string('action');
            $table->json('diff')->nullable();
            $table->timestamps();

            $table->index(['entity', 'entity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_log_internal');
    }
};
