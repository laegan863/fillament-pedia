<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientFollowUpForm extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'form_demographic_id',
        'patient_health_facility_id_no',
        'follow_up_last_encounter_date',
        'has_change_in_diagnosis',
        'has_more_than_one_primary_site',
        'primary_sites_being_treated',
        'procedures_administered',
        'radiotherapies',
        'treatment_status',
        'disease_outcome',
        'disease_outcome_date',
        'immediate_cause_of_death',
        'antecedent_cause_of_death',
        'underlying_cause_of_death',
        'other_significant_condition_related_to_death',
        'treatment_plan',
        'treatment_plan_others',
        'change_in_treatment_plan_procedures',
        'change_in_treatment_plan_reasons',
        'availed_financial_support',
        'financial_support_mechanisms',
        'financial_support_others',
    ];

    protected $casts = [
        'follow_up_last_encounter_date' => 'date',
        'disease_outcome_date' => 'date',

        'has_change_in_diagnosis' => 'boolean',
        'has_more_than_one_primary_site' => 'boolean',
        'availed_financial_support' => 'boolean',

        'primary_sites_being_treated' => 'array',
        'procedures_administered' => 'array',
        'radiotherapies' => 'array',
        'treatment_plan' => 'array',
        'change_in_treatment_plan_procedures' => 'array',
        'change_in_treatment_plan_reasons' => 'array',
        'financial_support_mechanisms' => 'array',
    ];

    public function formDemographic()
    {
        return $this->belongsTo(FormDemographics::class, 'form_demographic_id');
    }
}
