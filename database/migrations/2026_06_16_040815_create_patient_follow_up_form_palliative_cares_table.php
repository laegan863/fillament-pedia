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
        Schema::create('patient_follow_up_form_palliative_cares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_follow_up_form_id')
                ->constrained(indexName: 'pfuf_palliative_cares_fk')
                ->cascadeOnDelete();
            $table->date('date_started');
            $table->date('date_last_palliative_care');
            $table->json('reasons');
            $table->string('reason_specifics')->nullable();
            $table->string('type_of_care_integration', 100);
            $table->string('type_of_care_integration_other')->nullable();
            $table->string('goal_of_care', 100);
            $table->string('goal_of_care_other')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_follow_up_form_palliative_cares');
    }
};
