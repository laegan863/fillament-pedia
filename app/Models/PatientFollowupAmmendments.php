<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientFollowupAmmendments extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'patient_follow_up_form_id',
        'form_demographic_id',
        'health_facility_registration_no',
        'patient_health_facility_id_no',
        'has_multiple_active_primary_cancer_sites',
        'primary_cancer_site_number',
        'diagnosis_date',
        'age_at_diagnosis_years',
        'age_at_diagnosis_months',
        'basis_for_diagnosis',
        'specific_classification_id',
        'iccc_specific_classification',
        'iccc_parent_classification',
        'iccc_general_classification',
        'is_gicc_indexed_cancer',
        'gicc_indexed_cancer_type',
        'topography',
        'topography_other',
        'laterality',
        'metastasis_status',
        'metastasis_sites',
        'metastasis_other_site',
        'details_for_diagnosis',
        'icd10_code',
        'icdo3_topography_code',
        'icdo3_morphology_code',
        'clinical_stage',
        'clinical_stage_other',
        'staging_used',
        'staging_other_remarks',
        'current_status_of_cancer',
        'has_multidisciplinary_cancer_team',
        'multidisciplinary_disciplines',
        'multidisciplinary_other_discipline',
        'has_surgery',
        'surgery_goal',
        'has_anti_cancer_drug',
        'anti_cancer_drug_purpose',
        'anti_cancer_drug_types',
        'anti_cancer_drug_other_type',
        'has_radiotherapy',
        'radiotherapy_goal',
        'has_theranostics',
        'theranostics_goal',
        'has_palliative_care',
        'palliative_care_provider',
        'has_other_cancer_directed_therapy',
        'other_cancer_directed_therapy_types',
        'other_cancer_directed_therapy_other_type',
        'other_cancer_directed_therapy_goal',
        'overall_goal_of_therapy',
    ];

    protected $casts = [
        'patient_follow_up_form_id' => 'integer',
        'form_demographic_id' => 'integer',
        'has_multiple_active_primary_cancer_sites' => 'boolean',
        'primary_cancer_site_number' => 'integer',
        'diagnosis_date' => 'date',
        'age_at_diagnosis_years' => 'integer',
        'age_at_diagnosis_months' => 'integer',
        'specific_classification_id' => 'integer',
        'is_gicc_indexed_cancer' => 'boolean',
        'metastasis_sites' => 'array',
        'staging_used' => 'array',
        'has_multidisciplinary_cancer_team' => 'boolean',
        'multidisciplinary_disciplines' => 'array',
        'has_surgery' => 'boolean',
        'has_anti_cancer_drug' => 'boolean',
        'anti_cancer_drug_types' => 'array',
        'has_radiotherapy' => 'boolean',
        'has_theranostics' => 'boolean',
        'has_palliative_care' => 'boolean',
        'has_other_cancer_directed_therapy' => 'boolean',
        'other_cancer_directed_therapy_types' => 'array',
    ];

    public function patientFollowUpForm(): BelongsTo
    {
        return $this->belongsTo(PatientFollowUpForm::class);
    }

    public function formDemographic(): BelongsTo
    {
        return $this->belongsTo(FormDemographics::class, 'form_demographic_id');
    }

    public function specificClassification(): BelongsTo
    {
        return $this->belongsTo(SpecificClassification::class);
    }
}
