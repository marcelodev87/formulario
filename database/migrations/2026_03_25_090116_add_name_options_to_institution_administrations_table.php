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
        Schema::table('institution_administrations', function (Blueprint $table) {
            $table->string('name_option_1')->nullable();
            $table->string('name_option_2')->nullable();
            $table->string('name_option_3')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('institution_administrations', function (Blueprint $table) {
            $table->dropColumn(['name_option_1', 'name_option_2', 'name_option_3']);
        });
    }
};
