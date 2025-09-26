<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('members', 'gender')) {
            Schema::table('members', function (Blueprint $table) {
                $table->string('gender', 20)->nullable()->after('role');
            });
        }

        if (!Schema::hasColumn('leaders', 'gender')) {
            Schema::table('leaders', function (Blueprint $table) {
                $table->string('gender', 20)->nullable()->after('rg_issuer');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('leaders', 'gender')) {
            Schema::table('leaders', function (Blueprint $table) {
                $table->dropColumn('gender');
            });
        }

        if (Schema::hasColumn('members', 'gender')) {
            Schema::table('members', function (Blueprint $table) {
                $table->dropColumn('gender');
            });
        }
    }
};
