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
        Schema::create('patient_followup_ammendments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('patient_follow_up_form_id')
                ->nullable()
                ->constrained('patient_follow_up_forms')
                ->nullOnDelete();

            $table->foreignId('form_demographic_id')
                ->nullable()
                ->constrained('form_demographics')
                ->nullOnDelete();

            $table->string('health_facility_registration_no', 100)->nullable();
            $table->string('patient_health_facility_id_no', 50)->nullable();

            $table->boolean('has_multiple_active_primary_cancer_sites')->default(false);
            $table->unsignedTinyInteger('primary_cancer_site_number')->default(1);

            $table->date('diagnosis_date')->nullable();
            $table->unsignedTinyInteger('age_at_diagnosis_years')->nullable();
            $table->unsignedTinyInteger('age_at_diagnosis_months')->nullable();

            $table->string('basis_for_diagnosis')->nullable();
            $table->foreignId('specific_classification_id')
                ->nullable()
                ->constrained('specific_classifications')
                ->nullOnDelete();
            $table->string('iccc_specific_classification')->nullable();
            $table->string('iccc_parent_classification')->nullable();
            $table->string('iccc_general_classification')->nullable();

            $table->boolean('is_gicc_indexed_cancer')->default(false);
            $table->string('gicc_indexed_cancer_type')->nullable();

            $table->string('topography')->nullable();
            $table->string('topography_other')->nullable();
            $table->string('laterality')->nullable();
            $table->string('metastasis_status')->nullable();
            $table->json('metastasis_sites')->nullable();
            $table->string('metastasis_other_site')->nullable();

            $table->text('details_for_diagnosis')->nullable();
            $table->string('icd10_code')->nullable();
            $table->string('icdo3_topography_code')->nullable();
            $table->string('icdo3_morphology_code')->nullable();

            $table->string('clinical_stage')->nullable();
            $table->string('clinical_stage_other')->nullable();
            $table->json('staging_used')->nullable();
            $table->text('staging_other_remarks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_followup_ammendments');
    }
};
