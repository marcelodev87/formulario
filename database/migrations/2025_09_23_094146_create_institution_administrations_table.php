<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institution_administrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('dissolution_mode');
            $table->string('governance_model');
            $table->boolean('president_term_indefinite')->default(false);
            $table->unsignedTinyInteger('president_term_years')->nullable();
            $table->unsignedTinyInteger('board_term_years');
            $table->string('ordination_decision');
            $table->string('financial_responsible');
            $table->json('ministerial_roles')->nullable();
            $table->string('stipend_policy');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institution_administrations');
    }
};
