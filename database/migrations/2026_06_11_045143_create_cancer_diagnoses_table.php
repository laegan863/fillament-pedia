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
        Schema::create('cancer_diagnoses', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Patient / Form Reference
            |--------------------------------------------------------------------------
            */

            // Keep this if you already have the form_demographics table.
            // If not, remove this foreignId block before migrating.
            $table->foreignId('form_demographic_id')
                ->nullable()
                ->constrained('form_demographics')
                ->nullOnDelete();

            $table->string('health_facility_registration_no', 100)->nullable();

            $table->string('patient_health_facility_id_no', 50)
                ->nullable()
                ->index();

            /*
            |--------------------------------------------------------------------------
            | Multiple Primary Cancer Site Logic
            |--------------------------------------------------------------------------
            */

            $table->boolean('has_multiple_active_primary_cancer_sites')
                ->default(false);

            // Always 1.
            // Example:
            // 1 row = 1 cancer site
            // 3 rows = 1 + 1 + 1 = 3 cancer sites
            $table->unsignedTinyInteger('primary_cancer_site_number')
                ->default(1);

            /*
            |--------------------------------------------------------------------------
            | Date / Age at Diagnosis
            |--------------------------------------------------------------------------
            */

            $table->date('diagnosis_date')->nullable();

            $table->unsignedTinyInteger('age_at_diagnosis_years')->nullable();

            $table->unsignedTinyInteger('age_at_diagnosis_months')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Basis for Diagnosis
            |--------------------------------------------------------------------------
            */

            $table->string('basis_for_diagnosis')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Diagnosis - International Classification of Childhood Cancer
            |--------------------------------------------------------------------------
            */

            // Stored as string because ICCC has many long options and may be updated.
            $table->string('iccc_specific_classification')->nullable();

            $table->string('iccc_parent_classification')->nullable();

            $table->string('iccc_general_classification')->nullable();

            /*
            |--------------------------------------------------------------------------
            | GICC 6 Indexed Cancers
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_gicc_indexed_cancer')
                ->default(false);

            $table->string('gicc_indexed_cancer_type')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Topography / Laterality / Metastasis
            |--------------------------------------------------------------------------
            */

            // Example: Liver, Lung, Ureter, Brain, etc.
            $table->string('topography')->nullable();

            $table->string('topography_other')->nullable();

            $table->string('laterality')->nullable();

            $table->string('metastasis_status')->nullable();

            // Multiple sites.
            // Example: ["bone", "lung", "lymph_node"]
            $table->json('metastasis_sites')->nullable();

            $table->string('metastasis_other_site')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Details for Diagnosis / Codes
            |--------------------------------------------------------------------------
            */

            $table->text('details_for_diagnosis')->nullable();

            // JSON because the user may input/select multiple codes.
            // Example: ["C91.0", "C91.1"]
            $table->string('icd10_code')->nullable();

            // JSON because the user may input/select multiple codes.
            // Example: ["C70.1", "C42.1"]
            $table->string('icdo3_topography_code')->nullable();

            // JSON because the user may input/select multiple codes.
            // Example: ["9821/3", "9835/3"]
            $table->string('icdo3_morphology_code')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Clinical Staging
            |--------------------------------------------------------------------------
            */

            $table->string('clinical_stage')->nullable();

            $table->string('clinical_stage_other')->nullable();

            // Multiple staging systems if applicable.
            // Example: ["ann_arbor", "tnm", "toronto_tier_1"]
            $table->json('staging_used')->nullable();

            $table->text('staging_other_remarks')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Disease Status
            |--------------------------------------------------------------------------
            */

            $table->string('current_status_of_cancer')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Treatment Plan - Multidisciplinary Cancer Team
            |--------------------------------------------------------------------------
            */

            $table->boolean('has_multidisciplinary_cancer_team')
                ->default(false);

            // Multiple disciplines.
            // Example: ["pediatric_oncology", "radiation_oncology", "pathology"]
            $table->json('multidisciplinary_disciplines')->nullable();

            $table->string('multidisciplinary_other_discipline')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Treatment Plan - Surgery
            |--------------------------------------------------------------------------
            */

            $table->boolean('has_surgery')
                ->default(false);

            $table->string('surgery_goal')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Treatment Plan - Anti-Cancer Drug
            |--------------------------------------------------------------------------
            */

            $table->boolean('has_anti_cancer_drug')
                ->default(false);

            $table->string('anti_cancer_drug_purpose')->nullable();

            // Multiple drug types.
            // Example: ["cytotoxic", "immunologic", "targeted"]
            $table->json('anti_cancer_drug_types')->nullable();

            $table->string('anti_cancer_drug_other_type')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Treatment Plan - Radiotherapy
            |--------------------------------------------------------------------------
            */

            $table->boolean('has_radiotherapy')
                ->default(false);

            $table->string('radiotherapy_goal')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Treatment Plan - Theranostics
            |--------------------------------------------------------------------------
            */

            $table->boolean('has_theranostics')
                ->default(false);

            $table->string('theranostics_goal')->nullable();

            // /*
            // |--------------------------------------------------------------------------
            // | Treatment Plan - Pediatric Palliative Care
            // |--------------------------------------------------------------------------
            // |

            $table->boolean('has_palliative_care')
                ->default(false);

            $table->string('palliative_care_provider')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Treatment Plan - Other Cancer-Directed Therapies
            |--------------------------------------------------------------------------
            */

            $table->boolean('has_other_cancer_directed_therapy')
                ->default(false);

            // Multiple selected therapy types.
            // Example: ["transplant", "rai", "chemoembolization"]
            $table->json('other_cancer_directed_therapy_types')->nullable();

            $table->string('other_cancer_directed_therapy_other_type')->nullable();

            $table->string('other_cancer_directed_therapy_goal')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Overall Goal of Therapy
            |--------------------------------------------------------------------------
            */

            $table->string('overall_goal_of_therapy')->nullable();

            $table->softDeletes();

            $table->index([
                'form_demographic_id',
                'patient_health_facility_id_no',
            ], 'pcd_patient_form_index');

            $table->index('diagnosis_date', 'pcd_diagnosis_date_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cancer_diagnoses');
    }
};
