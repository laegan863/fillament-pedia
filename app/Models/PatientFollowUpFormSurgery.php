<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientFollowUpFormSurgery extends Model
{
    protected $fillable = [
        'patient_follow_up_form_id',
        'surgery_date',
        'surgery_rvs_code',
        'surgery_description',
        'surgery_goal',
    ];

    protected $casts = [
        'surgery_date' => 'date',
    ];

    public function patientFollowUpForm(): BelongsTo
    {
        return $this->belongsTo(PatientFollowUpForm::class);
    }
}
