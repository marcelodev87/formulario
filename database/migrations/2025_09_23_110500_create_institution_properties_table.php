<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institution_properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->string('iptu_registration')->nullable();
            $table->decimal('built_area_sqm', 10, 2)->nullable();
            $table->decimal('land_area_sqm', 10, 2)->nullable();
            $table->string('tenure_type');
            $table->unsignedInteger('capacity')->nullable();
            $table->unsignedTinyInteger('floors')->nullable();
            $table->string('activity_floor')->nullable();
            $table->string('property_use')->nullable();
            $table->string('property_section')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institution_properties');
    }
};
