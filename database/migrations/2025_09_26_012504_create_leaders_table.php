<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leaders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->date('birth_date');
            $table->string('birthplace');
            $table->string('nationality');
            $table->string('father_name');
            $table->string('mother_name');
            $table->string('cpf', 14);
            $table->string('rg');
            $table->string('rg_issuer');
            $table->string('marital_status');
            $table->string('profession');
            $table->string('email');
            $table->string('phone', 20);
            $table->string('street');
            $table->string('number', 20);
            $table->string('complement')->nullable();
            $table->string('district');
            $table->string('city');
            $table->string('uf', 2);
            $table->string('cep', 9);
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['location_id', 'email']);
            $table->unique(['location_id', 'cpf']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaders');
    }
};
