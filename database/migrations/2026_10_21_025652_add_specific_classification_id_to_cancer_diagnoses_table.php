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
        Schema::table('cancer_diagnoses', function (Blueprint $table) {
            $table->foreignId('specific_classification_id')
                ->nullable()
                ->after('iccc_specific_classification')
                ->constrained()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cancer_diagnoses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('specific_classification_id');
        });
    }
};
