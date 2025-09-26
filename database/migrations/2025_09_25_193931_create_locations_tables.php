<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('process_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('name')->nullable();
            $table->string('street')->nullable();
            $table->string('number', 20)->nullable();
            $table->string('complement')->nullable();
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->string('uf', 2)->nullable();
            $table->string('cep', 9)->nullable();
            $table->timestamps();

            $table->index(['institution_id', 'type']);
        });

        Schema::create('location_properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            $table->string('iptu_registration')->nullable();
            $table->decimal('built_area_sqm', 10, 2)->nullable();
            $table->decimal('land_area_sqm', 10, 2)->nullable();
            $table->string('tenure_type')->nullable();
            $table->unsignedInteger('capacity')->nullable();
            $table->unsignedTinyInteger('floors')->nullable();
            $table->string('activity_floor')->nullable();
            $table->string('property_use')->nullable();
            $table->string('property_section')->nullable();
            $table->timestamps();
        });

        DB::transaction(function () {
            $institutions = DB::table('institutions')->get();

            foreach ($institutions as $institution) {
                $createdAt = Carbon::parse($institution->created_at ?? now());
                $updatedAt = Carbon::parse($institution->updated_at ?? now());

                $locationId = DB::table('locations')->insertGetId([
                    'institution_id' => $institution->id,
                    'process_id' => null,
                    'type' => 'headquarters',
                    'name' => $institution->name,
                    'street' => $institution->street,
                    'number' => $institution->number,
                    'complement' => $institution->complement,
                    'district' => $institution->district,
                    'city' => $institution->city,
                    'uf' => $institution->uf,
                    'cep' => $institution->cep,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ]);

                $property = DB::table('institution_properties')->where('institution_id', $institution->id)->first();

                if ($property) {
                    DB::table('location_properties')->insert([
                        'location_id' => $locationId,
                        'iptu_registration' => $property->iptu_registration,
                        'built_area_sqm' => $property->built_area_sqm,
                        'land_area_sqm' => $property->land_area_sqm,
                        'tenure_type' => $property->tenure_type,
                        'capacity' => $property->capacity,
                        'floors' => $property->floors,
                        'activity_floor' => $property->activity_floor,
                        'property_use' => $property->property_use,
                        'property_section' => $property->property_section,
                        'created_at' => Carbon::parse($property->created_at ?? now()),
                        'updated_at' => Carbon::parse($property->updated_at ?? now()),
                    ]);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('location_properties');
        Schema::dropIfExists('locations');
    }
};
