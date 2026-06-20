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
        Schema::create('patient_follow_up_form_radiotherapies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_follow_up_form_id')
                ->constrained(indexName: 'pfuf_radiotherapies_fk')
                ->cascadeOnDelete();
            $table->date('date_started');
            $table->date('date_ended');
            $table->decimal('total_planned_dose', 8, 2);
            $table->unsignedSmallInteger('total_delivered_fraction');
            $table->decimal('dose_per_fraction', 8, 2);
            $table->unsignedSmallInteger('total_number_of_days');
            $table->string('radiotherapy_type', 100);
            $table->string('radiotherapy_type_specifics')->nullable();
            $table->string('target_site', 100);
            $table->string('target_site_specifics')->nullable();
            $table->string('goal_of_radiotherapy', 100);
            $table->string('goal_other')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_follow_up_form_radiotherapies');
    }
};
