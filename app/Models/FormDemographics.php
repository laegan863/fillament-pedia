<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormDemographics extends Model
{
    protected $table = 'form_demographics';

    protected $fillable = [
        /*
        |--------------------------------------------------------------------------
        | Patient Demographics
        |--------------------------------------------------------------------------
        */

        'first_encounter_date',
        'health_facility_id_no',

        'patient_first_name',
        'patient_middle_name',
        'patient_surname',
        'patient_suffix',

        'sex_at_birth',

        'date_of_birth',
        'birth_province',
        'birth_city_municipality',
        'nationality',

        /*
        |--------------------------------------------------------------------------
        | Guardian / Nearest Relative
        |--------------------------------------------------------------------------
        */

        'guardian_first_name',
        'guardian_middle_name',
        'guardian_surname',
        'guardian_suffix',

        'philhealth_pin_na',
        'philhealth_pin',

        /*
        |--------------------------------------------------------------------------
        | Permanent Address
        |--------------------------------------------------------------------------
        */

        'permanent_region',
        'permanent_province',
        'permanent_city_municipality',
        'permanent_barangay',

        /*
        |--------------------------------------------------------------------------
        | Current Address
        |--------------------------------------------------------------------------
        */

        'same_as_permanent_address',
        'current_region',
        'current_province',
        'current_city_municipality',
        'current_barangay',

        /*
        |--------------------------------------------------------------------------
        | Contact
        |--------------------------------------------------------------------------
        */

        'mobile_contact_na',
        'mobile_contact_no',

        'email_na',
        'email_address',

        /*
        |--------------------------------------------------------------------------
        | Relationship
        |--------------------------------------------------------------------------
        */

        'relationship_to_patient',
        'relationship_other_specify',
    ];

    protected $casts = [
        'first_encounter_date' => 'date',
        'date_of_birth' => 'date',

        'philhealth_pin_na' => 'boolean',
        'same_as_permanent_address' => 'boolean',
        'mobile_contact_na' => 'boolean',
        'email_na' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function cancerDiagnoses(): HasMany
    {
        return $this->hasMany(CancerDiagnose::class, 'form_demographic_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors / Helpers
    |--------------------------------------------------------------------------
    */

    public function getFullNameAttribute(): string
    {
        return collect([
            $this->patient_first_name,
            $this->patient_middle_name,
            $this->patient_surname,
            $this->patient_suffix,
        ])
            ->filter()
            ->implode(' ');
    }

    public function getGuardianFullNameAttribute(): string
    {
        return collect([
            $this->guardian_first_name,
            $this->guardian_middle_name,
            $this->guardian_surname,
            $this->guardian_suffix,
        ])
            ->filter()
            ->implode(' ');
    }

    public function getPermanentAddressAttribute(): string
    {
        return collect([
            $this->permanent_barangay,
            $this->permanent_city_municipality,
            $this->permanent_province,
            $this->permanent_region,
        ])
            ->filter()
            ->implode(', ');
    }

    public function getCurrentAddressAttribute(): string
    {
        return collect([
            $this->current_barangay,
            $this->current_city_municipality,
            $this->current_province,
            $this->current_region,
        ])
            ->filter()
            ->implode(', ');
    }
}
