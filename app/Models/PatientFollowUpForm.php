<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

class PatientFollowUpForm extends Model
{
    use SoftDeletes;

    private const SURGERY_RECORD_COLUMNS = [
        'surgery_date',
        'surgery_rvs_code',
        'surgery_description',
        'surgery_goal',
    ];

    private const ANTI_CANCER_DRUG_THERAPY_RECORD_COLUMNS = [
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

    private const RADIOTHERAPY_RECORD_COLUMNS = [
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

    private const PALLIATIVE_CARE_RECORD_COLUMNS = [
        'date_started',
        'date_last_palliative_care',
        'reasons',
        'reason_specifics',
        'type_of_care_integration',
        'type_of_care_integration_other',
        'goal_of_care',
        'goal_of_care_other',
    ];

    private const OTHER_CANCER_DIRECTED_THERAPY_RECORD_COLUMNS = [
        'date_of_therapy',
        'type_of_cancer_directed_therapy',
        'type_of_cancer_directed_therapy_other',
        'goal_of_cancer_directed_therapy',
        'goal_of_cancer_directed_therapy_other',
    ];

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

    public function formDemographic(): BelongsTo
    {
        return $this->belongsTo(FormDemographics::class, 'form_demographic_id');
    }

    public function patientFollowupAmmendment(): HasOne
    {
        return $this->hasOne(PatientFollowupAmmendments::class);
    }

    public function surgeryRecords(): HasMany
    {
        return $this->hasMany(PatientFollowUpFormSurgery::class);
    }

    public function antiCancerDrugTherapyRecords(): HasMany
    {
        return $this->hasMany(PatientFollowUpFormAntiCancerDrugTherapy::class);
    }

    public function radiotherapyRecords(): HasMany
    {
        return $this->hasMany(PatientFollowUpFormRadiotherapy::class);
    }

    public function theranosticRecords(): HasMany
    {
        return $this->hasMany(PatientFollowUpFormTheranostic::class);
    }

    public function palliativeCareRecords(): HasMany
    {
        return $this->hasMany(PatientFollowUpFormPalliativeCare::class);
    }

    public function otherCancerDirectedTherapyRecords(): HasMany
    {
        return $this->hasMany(PatientFollowUpFormOtherCancerDirectedTherapy::class);
    }

    /**
     * @param  array<string, mixed>  $treatmentRecords
     */
    public function syncTreatmentRecords(array $treatmentRecords): void
    {
        $this->syncHasManyRecords(
            $this->surgeryRecords(),
            $this->selectedTreatmentRecords($treatmentRecords, 'surgeries', 'Surgery'),
            self::SURGERY_RECORD_COLUMNS,
        );

        $this->syncHasManyRecords(
            $this->antiCancerDrugTherapyRecords(),
            $this->selectedTreatmentRecords(
                $treatmentRecords,
                'anti_cancer_drug_therapies',
                'Anti-cancer drug therapy',
            ),
            self::ANTI_CANCER_DRUG_THERAPY_RECORD_COLUMNS,
        );

        $this->syncHasManyRecords(
            $this->radiotherapyRecords(),
            $this->selectedTreatmentRecords($treatmentRecords, 'radiotherapies', 'Radiotherapy'),
            self::RADIOTHERAPY_RECORD_COLUMNS,
        );

        $this->syncHasManyRecords(
            $this->theranosticRecords(),
            $this->selectedTreatmentRecords($treatmentRecords, 'theranostics', 'Theranostics'),
            self::ANTI_CANCER_DRUG_THERAPY_RECORD_COLUMNS,
        );

        $this->syncHasManyRecords(
            $this->palliativeCareRecords(),
            $this->selectedTreatmentRecords($treatmentRecords, 'palliative_cares', 'Palliative Care'),
            self::PALLIATIVE_CARE_RECORD_COLUMNS,
        );

        $this->syncHasManyRecords(
            $this->otherCancerDirectedTherapyRecords(),
            $this->selectedTreatmentRecords(
                $treatmentRecords,
                'other_cancer_directed_therapies',
                'Other Treatments',
            ),
            self::OTHER_CANCER_DIRECTED_THERAPY_RECORD_COLUMNS,
        );
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function treatmentRecordsForForm(): array
    {
        $this->loadMissing([
            'surgeryRecords',
            'antiCancerDrugTherapyRecords',
            'radiotherapyRecords',
            'theranosticRecords',
            'palliativeCareRecords',
            'otherCancerDirectedTherapyRecords',
        ]);

        return [
            'surgeries' => $this->recordsForForm($this->surgeryRecords, self::SURGERY_RECORD_COLUMNS),
            'anti_cancer_drug_therapies' => $this->recordsForForm(
                $this->antiCancerDrugTherapyRecords,
                self::ANTI_CANCER_DRUG_THERAPY_RECORD_COLUMNS,
            ),
            'radiotherapies' => $this->recordsForForm($this->radiotherapyRecords, self::RADIOTHERAPY_RECORD_COLUMNS),
            'theranostics' => $this->recordsForForm(
                $this->theranosticRecords,
                self::ANTI_CANCER_DRUG_THERAPY_RECORD_COLUMNS,
            ),
            'palliative_cares' => $this->recordsForForm($this->palliativeCareRecords, self::PALLIATIVE_CARE_RECORD_COLUMNS),
            'other_cancer_directed_therapies' => $this->recordsForForm(
                $this->otherCancerDirectedTherapyRecords,
                self::OTHER_CANCER_DIRECTED_THERAPY_RECORD_COLUMNS,
            ),
        ];
    }

    /**
     * @param  array<string, mixed>  $treatmentRecords
     * @return array<int, array<string, mixed>>
     */
    private function selectedTreatmentRecords(array $treatmentRecords, string $stateKey, string $procedure): array
    {
        if (
            array_key_exists('procedures_administered', $treatmentRecords) &&
            ! in_array($procedure, Arr::wrap($treatmentRecords['procedures_administered']), true)
        ) {
            return [];
        }

        $records = $treatmentRecords[$stateKey] ?? [];

        return is_array($records) ? $records : [];
    }

    /**
     * @param  array<int, array<string, mixed>>  $records
     * @param  array<int, string>  $columns
     */
    private function syncHasManyRecords(HasMany $relationship, array $records, array $columns): void
    {
        $relationship->delete();

        $records = collect($records)
            ->filter(fn (mixed $record): bool => is_array($record))
            ->map(fn (array $record): array => Arr::only($record, $columns))
            ->filter(fn (array $record): bool => collect($record)->contains(fn (mixed $value): bool => filled($value)))
            ->values()
            ->all();

        if ($records === []) {
            return;
        }

        $relationship->createMany($records);
    }

    /**
     * @param  iterable<int, Model>  $records
     * @param  array<int, string>  $columns
     * @return array<int, array<string, mixed>>
     */
    private function recordsForForm(iterable $records, array $columns): array
    {
        return collect($records)
            ->map(fn (Model $record): array => Arr::only($record->attributesToArray(), $columns))
            ->values()
            ->all();
    }
}
