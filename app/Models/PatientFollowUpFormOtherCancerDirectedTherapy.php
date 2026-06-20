<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientFollowUpFormOtherCancerDirectedTherapy extends Model
{
    protected $fillable = [
        'patient_follow_up_form_id',
        'date_of_therapy',
        'type_of_cancer_directed_therapy',
        'type_of_cancer_directed_therapy_other',
        'goal_of_cancer_directed_therapy',
        'goal_of_cancer_directed_therapy_other',
    ];

    protected $casts = [
        'date_of_therapy' => 'date',
    ];

    public function patientFollowUpForm(): BelongsTo
    {
        return $this->belongsTo(PatientFollowUpForm::class);
    }
}
