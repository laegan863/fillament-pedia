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
        Schema::create('regions', function (Blueprint $table) {
            $table->string('code', 10)->primary();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('provinces', function (Blueprint $table) {
            $table->string('code', 10)->primary();
            $table->string('region_code', 10)->index();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('cities_municipalities', function (Blueprint $table) {
            $table->string('code', 10)->primary();
            $table->string('region_code', 10)->index();
            $table->string('province_code', 10)->nullable()->index();
            $table->string('name');
            $table->boolean('is_city')->default(false);
            $table->timestamps();
        });

        Schema::create('barangays', function (Blueprint $table) {
            $table->string('code', 10)->primary();
            $table->string('city_municipality_code', 10)->index();
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangays');
        Schema::dropIfExists('cities_municipalities');
        Schema::dropIfExists('provinces');
        Schema::dropIfExists('regions');
    }
};
