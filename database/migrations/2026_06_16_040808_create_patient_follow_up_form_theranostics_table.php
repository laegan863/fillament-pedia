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
        Schema::create('patient_follow_up_form_theranostics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_follow_up_form_id')
                ->constrained(indexName: 'pfuf_theranostics_fk')
                ->cascadeOnDelete();
            $table->date('date_started');
            $table->date('date_last_anti_cancer_drug_therapy');
            $table->text('drugs_given');
            $table->json('drug_types');
            $table->string('drug_type_other')->nullable();
            $table->string('treatment_phase', 100);
            $table->string('treatment_phase_other')->nullable();
            $table->unsignedSmallInteger('cycle_no');
            $table->string('goal_of_anti_cancer_drug_therapy', 100);
            $table->string('goal_other')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_follow_up_form_theranostics');
    }
};
