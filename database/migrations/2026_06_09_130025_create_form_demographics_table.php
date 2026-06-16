<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_demographics', function (Blueprint $table) {
            $table->id();

            // Patient Demographics
            $table->date('first_encounter_date')->nullable();
            $table->string('health_facility_id_no')->nullable();

            $table->string('patient_first_name')->nullable();
            $table->string('patient_middle_name')->nullable();
            $table->string('patient_surname')->nullable();
            $table->string('patient_suffix')->nullable();

            $table->string('sex_at_birth')->nullable();

            $table->date('date_of_birth')->nullable();
            $table->string('birth_province')->nullable();
            $table->string('birth_city_municipality')->nullable();
            $table->string('nationality')->nullable();

            // Guardian / Nearest Relative
            $table->string('guardian_first_name')->nullable();
            $table->string('guardian_middle_name')->nullable();
            $table->string('guardian_surname')->nullable();
            $table->string('guardian_suffix')->nullable();

            $table->boolean('philhealth_pin_na')->default(false);
            $table->string('philhealth_pin')->nullable();

            // Permanent Address
            $table->string('permanent_region')->nullable();
            $table->string('permanent_province')->nullable();
            $table->string('permanent_city_municipality')->nullable();
            $table->string('permanent_barangay')->nullable();

            // Current Address
            $table->boolean('same_as_permanent_address')->default(false);
            $table->string('current_region')->nullable();
            $table->string('current_province')->nullable();
            $table->string('current_city_municipality')->nullable();
            $table->string('current_barangay')->nullable();

            // Contact
            $table->boolean('mobile_contact_na')->default(false);
            $table->string('mobile_contact_no')->nullable();

            $table->boolean('email_na')->default(false);
            $table->string('email_address')->nullable();

            // Relationship
            $table->string('relationship_to_patient')->nullable();
            $table->string('relationship_other_specify')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_demographics');
    }
};