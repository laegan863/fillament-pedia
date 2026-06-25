<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CancerDiagnose extends Model
{
    protected $fillable = [
        /*
       |--------------------------------------------------------------------------
       | Patient / Form Reference
       |--------------------------------------------------------------------------
       */

        'form_demographic_id',
        'health_facility_registration_no',
        'patient_health_facility_id_no',

        /*
       |--------------------------------------------------------------------------
       | Multiple Primary Cancer Site Logic
       |--------------------------------------------------------------------------
       */

        'has_multiple_active_primary_cancer_sites',
        'primary_cancer_site_number',

        /*
       |--------------------------------------------------------------------------
       | Date / Age at Diagnosis
       |--------------------------------------------------------------------------
       */

        'diagnosis_date',
        'age_at_diagnosis_years',
        'age_at_diagnosis_months',

        /*
       |--------------------------------------------------------------------------
       | Basis for Diagnosis
       |--------------------------------------------------------------------------
       */

        'basis_for_diagnosis',

        /*
       |--------------------------------------------------------------------------
       | Diagnosis - International Classification of Childhood Cancer
       |--------------------------------------------------------------------------
       */

        'specific_classification_id',
        'iccc_specific_classification',
        'iccc_parent_classification',
        'iccc_general_classification',

        /*
       |--------------------------------------------------------------------------
       | GICC 6 Indexed Cancers
       |--------------------------------------------------------------------------
       */

        'is_gicc_indexed_cancer',
        'gicc_indexed_cancer_type',

        /*
       |--------------------------------------------------------------------------
       | Topography / Laterality / Metastasis
       |--------------------------------------------------------------------------
       */

        'topography',
        'topography_other',
        'laterality',
        'metastasis_status',
        'metastasis_sites',
        'metastasis_other_site',

        /*
       |--------------------------------------------------------------------------
       | Details for Diagnosis / Codes
       |--------------------------------------------------------------------------
       */

        'details_for_diagnosis',
        'icd10_code',
        'icdo3_topography_code',
        'icdo3_morphology_code',

        /*
       |--------------------------------------------------------------------------
       | Clinical Staging
       |--------------------------------------------------------------------------
       */

        'clinical_stage',
        'clinical_stage_other',
        'staging_used',
        'staging_other_remarks',

        /*
       |--------------------------------------------------------------------------
       | Disease Status
       |--------------------------------------------------------------------------
       */

        'current_status_of_cancer',

        /*
       |--------------------------------------------------------------------------
       | Treatment Plan - Multidisciplinary Cancer Team
       |--------------------------------------------------------------------------
       */

        'has_multidisciplinary_cancer_team',
        'multidisciplinary_disciplines',
        'multidisciplinary_other_discipline',

        /*
       |--------------------------------------------------------------------------
       | Treatment Plan - Surgery
       |--------------------------------------------------------------------------
       */

        'has_surgery',
        'surgery_goal',

        /*
       |--------------------------------------------------------------------------
       | Treatment Plan - Anti-Cancer Drug
       |--------------------------------------------------------------------------
       */

        'has_anti_cancer_drug',
        'anti_cancer_drug_purpose',
        'anti_cancer_drug_types',
        'anti_cancer_drug_other_type',

        /*
       |--------------------------------------------------------------------------
       | Treatment Plan - Radiotherapy
       |--------------------------------------------------------------------------
       */

        'has_radiotherapy',
        'radiotherapy_goal',

        /*
       |--------------------------------------------------------------------------
       | Treatment Plan - Theranostics
       |--------------------------------------------------------------------------
       */

        'has_theranostics',
        'theranostics_goal',

        /*
       |--------------------------------------------------------------------------
       | Treatment Plan - Pediatric Palliative Care
       |--------------------------------------------------------------------------
       */

        'has_palliative_care',
        'palliative_care_provider',

        /*
       |--------------------------------------------------------------------------
       | Treatment Plan - Other Cancer-Directed Therapies
       |--------------------------------------------------------------------------
       */

        'has_other_cancer_directed_therapy',
        'other_cancer_directed_therapy_types',
        'other_cancer_directed_therapy_other_type',
        'other_cancer_directed_therapy_goal',

        /*
       |--------------------------------------------------------------------------
       | Overall Goal of Therapy
       |--------------------------------------------------------------------------
       */

        'overall_goal_of_therapy',
    ];

    protected $casts = [
        /*
        |--------------------------------------------------------------------------
        | Boolean Fields
        |--------------------------------------------------------------------------
        */

        'has_multiple_active_primary_cancer_sites' => 'boolean',
        'is_gicc_indexed_cancer' => 'boolean',
        'has_multidisciplinary_cancer_team' => 'boolean',
        'has_surgery' => 'boolean',
        'has_anti_cancer_drug' => 'boolean',
        'has_radiotherapy' => 'boolean',
        'has_theranostics' => 'boolean',
        'has_palliative_care' => 'boolean',
        'has_other_cancer_directed_therapy' => 'boolean',

        /*
        |--------------------------------------------------------------------------
        | Number Fields
        |--------------------------------------------------------------------------
        */

        'form_demographic_id' => 'integer',
        'specific_classification_id' => 'integer',
        'primary_cancer_site_number' => 'integer',
        'age_at_diagnosis_years' => 'integer',
        'age_at_diagnosis_months' => 'integer',

        /*
        |--------------------------------------------------------------------------
        | Date Fields
        |--------------------------------------------------------------------------
        */

        'diagnosis_date' => 'date',

        /*
        |--------------------------------------------------------------------------
        | JSON Fields
        |--------------------------------------------------------------------------
        */

        'metastasis_sites' => 'array',
        'staging_used' => 'array',
        'multidisciplinary_disciplines' => 'array',
        'anti_cancer_drug_types' => 'array',
        'other_cancer_directed_therapy_types' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function formDemographic(): BelongsTo
    {
        return $this->belongsTo(FormDemographics::class, 'form_demographic_id');
    }

    public function specificClassification(): BelongsTo
    {
        return $this->belongsTo(SpecificClassification::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeForPatient(Builder $query, string $patientHealthFacilityIdNo): Builder
    {
        return $query->where('patient_health_facility_id_no', $patientHealthFacilityIdNo);
    }

    public function scopeForFormDemographic(Builder $query, int $formDemographicId): Builder
    {
        return $query->where('form_demographic_id', $formDemographicId);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    public static function totalCancerSitesForPatient(string $patientHealthFacilityIdNo): int
    {
        return (int) static::where('patient_health_facility_id_no', $patientHealthFacilityIdNo)
            ->sum('primary_cancer_site_number');
    }

    public static function totalCancerSitesForFormDemographic(int $formDemographicId): int
    {
        return (int) static::where('form_demographic_id', $formDemographicId)
            ->sum('primary_cancer_site_number');
    }
}
