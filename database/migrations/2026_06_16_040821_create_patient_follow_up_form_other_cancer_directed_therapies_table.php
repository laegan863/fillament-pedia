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
        Schema::create('patient_follow_up_form_other_cancer_directed_therapies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_follow_up_form_id')
                ->constrained(indexName: 'pfuf_other_therapies_fk')
                ->cascadeOnDelete();
            $table->date('date_of_therapy');
            $table->string('type_of_cancer_directed_therapy', 100);
            $table->string('type_of_cancer_directed_therapy_other')->nullable();
            $table->string('goal_of_cancer_directed_therapy', 100);
            $table->string('goal_of_cancer_directed_therapy_other')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_follow_up_form_other_cancer_directed_therapies');
    }
};
