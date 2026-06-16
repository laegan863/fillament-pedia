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
        Schema::create('patient_follow_up_forms', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            /*
            |--------------------------------------------------------------------------
            | Patient Reference
            |--------------------------------------------------------------------------
            */
            $table->foreignId('form_demographic_id')
                ->nullable()
                ->constrained('form_demographics')
                ->nullOnDelete();

            $table->string('patient_health_facility_id_no', 50)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Follow-up Information
            |--------------------------------------------------------------------------
            */
            $table->date('follow_up_last_encounter_date')->nullable();

            $table->boolean('has_change_in_diagnosis')->default(false);

            $table->boolean('has_more_than_one_primary_site')->default(false);

            // Multiple primary sites can be selected
            $table->json('primary_sites_being_treated')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Treatment - Procedures Administered
            |--------------------------------------------------------------------------
            */
            // Example: ["Surgery", "Anti-cancer drug therapy"]
            $table->json('procedures_administered')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Medical Evaluation / Treatment Outcomes
            |--------------------------------------------------------------------------
            */
            $table->string('treatment_status')->nullable();
            /*
                Ongoing
                Completed
                Stopped/Interrupted
                Unknown
                Not Initiated
                Abandonment
            */

            $table->string('disease_outcome')->nullable();
            /*
                Stable Disease
                Complete Remission
                Partial Response
                Progressive/Refractory
                Recurrent Disease
                Undetermined
                Death (Cancer-Related)
                Death (Treatment-Related)
                Death (Other Cause/Non-Cancer Related)
                Death (Pending Evaluation)
            */

            $table->date('disease_outcome_date')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Cause of Death
            |--------------------------------------------------------------------------
            */
            $table->text('immediate_cause_of_death')->nullable();
            $table->text('antecedent_cause_of_death')->nullable();
            $table->text('underlying_cause_of_death')->nullable();
            $table->text('other_significant_condition_related_to_death')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Treatment Plan
            |--------------------------------------------------------------------------
            */
            // Example: ["New Chemotherapy Regimen", "Refer to Surgery"]
            $table->json('treatment_plan')->nullable();

            $table->text('treatment_plan_others')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Change in Treatment Plan
            |--------------------------------------------------------------------------
            */
            // Example: ["Surgery", "Anti-cancer drug therapy"]
            $table->json('change_in_treatment_plan_procedures')->nullable();

            // Store reasons per procedure/therapy
            $table->json('change_in_treatment_plan_reasons')->nullable();
            /*
                Example:
                {
                    "Surgery": "Reason here",
                    "Anti-cancer drug therapy": "ALL in relapse",
                    "Radiotherapy": null,
                    "Theranostics": null,
                    "Other Treatments": null
                }
            */

            /*
            |--------------------------------------------------------------------------
            | Financial Support Mechanisms
            |--------------------------------------------------------------------------
            */
            $table->boolean('availed_financial_support')->default(false);

            // Multiple selections
            $table->json('financial_support_mechanisms')->nullable();

            $table->text('financial_support_others')->nullable();

            $table->index('patient_health_facility_id_no');
            $table->index('follow_up_last_encounter_date');
            $table->index('treatment_status');
            $table->index('disease_outcome');

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_follow_up_forms');
    }
};
