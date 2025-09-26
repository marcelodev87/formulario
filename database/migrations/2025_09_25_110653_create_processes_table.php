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
        Schema::create('processes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('title');
            $table->string('status')->default('draft');
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        $now = now();
        $existingInstitutions = DB::table('institutions')->select('id')->get();

        if ($existingInstitutions->isNotEmpty()) {
            $defaultTitle = 'Processo de abertura';

            $records = $existingInstitutions->map(function ($institution) use ($now, $defaultTitle) {
                return [
                    'institution_id' => $institution->id,
                    'type' => 'institution_opening',
                    'title' => $defaultTitle,
                    'status' => 'in_progress',
                    'meta' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            });

            if ($records->isNotEmpty()) {
                DB::table('processes')->insert($records->all());
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processes');
    }
};
