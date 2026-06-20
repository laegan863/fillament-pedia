<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientFollowUpFormAntiCancerDrugTherapy extends Model
{
    protected $fillable = [
        'patient_follow_up_form_id',
        'date_started',
        'date_last_anti_cancer_drug_therapy',
        'drugs_given',
        'drug_types',
        'drug_type_other',
        'treatment_phase',
        'treatment_phase_other',
        'cycle_no',
        'goal_of_anti_cancer_drug_therapy',
        'goal_other',
    ];

    protected $casts = [
        'date_started' => 'date',
        'date_last_anti_cancer_drug_therapy' => 'date',
        'drug_types' => 'array',
        'cycle_no' => 'integer',
    ];

    public function patientFollowUpForm(): BelongsTo
    {
        return $this->belongsTo(PatientFollowUpForm::class);
    }
}
