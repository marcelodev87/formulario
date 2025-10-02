<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! $this->indexExists('members', 'members_institution_id_index')) {
            Schema::table('members', function (Blueprint $table) {
                $table->index('institution_id', 'members_institution_id_index');
            });
        }

        if ($this->indexExists('members', 'members_institution_id_email_unique')) {
            Schema::table('members', function (Blueprint $table) {
                $table->dropUnique('members_institution_id_email_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if ($this->indexExists('members', 'members_institution_id_index')) {
            Schema::table('members', function (Blueprint $table) {
                $table->dropIndex('members_institution_id_index');
            });
        }

        Schema::table('members', function (Blueprint $table) {
            $table->unique(['institution_id', 'email'], 'members_institution_id_email_unique');
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection()->getName();
        $database = config("database.connections.$connection.database");

        $result = DB::select(
            'SELECT COUNT(*) as total FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ?',
            [$database, $table, $indexName]
        );

        return ! empty($result) && (int) $result[0]->total > 0;
    }
};
