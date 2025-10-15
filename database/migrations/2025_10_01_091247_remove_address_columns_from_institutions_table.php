<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = ['street', 'number', 'complement', 'district', 'city', 'uf', 'cep'];
        $existingColumns = array_filter($columns, fn (string $column) => Schema::hasColumn('institutions', $column));

        if ($existingColumns === []) {
            return;
        }

        Schema::table('institutions', function (Blueprint $table) use ($existingColumns) {
            $table->dropColumn($existingColumns);
        });
    }

    public function down(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->string('street')->nullable();
            $table->string('number', 20)->nullable();
            $table->string('complement')->nullable();
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->string('uf', 2)->nullable();
            $table->string('cep', 9)->nullable();
        });
    }
};
