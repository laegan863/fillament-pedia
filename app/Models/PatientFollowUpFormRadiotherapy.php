<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientFollowUpFormRadiotherapy extends Model
{
    protected $fillable = [
        'patient_follow_up_form_id',
        'date_started',
        'date_ended',
        'total_planned_dose',
        'total_delivered_fraction',
        'dose_per_fraction',
        'total_number_of_days',
        'radiotherapy_type',
        'radiotherapy_type_specifics',
        'target_site',
        'target_site_specifics',
        'goal_of_radiotherapy',
        'goal_other',
    ];

    protected $casts = [
        'date_started' => 'date',
        'date_ended' => 'date',
        'total_planned_dose' => 'decimal:2',
        'total_delivered_fraction' => 'integer',
        'dose_per_fraction' => 'decimal:2',
        'total_number_of_days' => 'integer',
    ];

    public function patientFollowUpForm(): BelongsTo
    {
        return $this->belongsTo(PatientFollowUpForm::class);
    }
}
