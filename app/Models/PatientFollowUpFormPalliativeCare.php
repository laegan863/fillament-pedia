<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientFollowUpFormPalliativeCare extends Model
{
    protected $fillable = [
        'patient_follow_up_form_id',
        'date_started',
        'date_last_palliative_care',
        'reasons',
        'reason_specifics',
        'type_of_care_integration',
        'type_of_care_integration_other',
        'goal_of_care',
        'goal_of_care_other',
    ];

    protected $casts = [
        'date_started' => 'date',
        'date_last_palliative_care' => 'date',
        'reasons' => 'array',
    ];

    public function patientFollowUpForm(): BelongsTo
    {
        return $this->belongsTo(PatientFollowUpForm::class);
    }
}
