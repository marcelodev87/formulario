<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invites', function (Blueprint $table) {
            $table->foreignId('process_id')->nullable()->after('institution_id')->constrained()->cascadeOnDelete();
        });

        Schema::table('members', function (Blueprint $table) {
            $table->foreignId('process_id')->nullable()->after('institution_id')->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropConstrainedForeignId('process_id');
        });

        Schema::table('invites', function (Blueprint $table) {
            $table->dropConstrainedForeignId('process_id');
        });
    }
};
