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
        Schema::create('patient_follow_up_form_surgeries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_follow_up_form_id')
                ->constrained(indexName: 'pfuf_surgeries_fk')
                ->cascadeOnDelete();
            $table->date('surgery_date');
            $table->string('surgery_rvs_code', 100)->nullable();
            $table->string('surgery_description');
            $table->string('surgery_goal', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_follow_up_form_surgeries');
    }
};
