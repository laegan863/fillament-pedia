<?php

use App\Models\PatientFollowUpForm;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('patient follow up treatment records are saved to child tables', function (): void {
    $followUpForm = PatientFollowUpForm::create([
        'patient_health_facility_id_no' => '0000000001',
        'follow_up_last_encounter_date' => '2026-06-16',
        'procedures_administered' => [
            'Surgery',
            'Anti-cancer drug therapy',
            'Radiotherapy',
            'Theranostics',
            'Palliative Care',
            'Other Treatments',
        ],
    ]);

    $followUpForm->syncTreatmentRecords([
        'surgeries' => [[
            'surgery_date' => '2026-06-01',
            'surgery_rvs_code' => '12345',
            'surgery_description' => 'Enucleation of the Right Eye',
            'surgery_goal' => 'Definitive',
        ]],
        'anti_cancer_drug_therapies' => [[
            'date_started' => '2026-06-02',
            'date_last_anti_cancer_drug_therapy' => '2026-06-10',
            'drugs_given' => 'Methotrexate, Vincristine',
            'drug_types' => ['Cytotoxic', 'Others'],
            'drug_type_other' => 'Protocol specific',
            'treatment_phase' => 'Induction',
            'treatment_phase_other' => null,
            'cycle_no' => 1,
            'goal_of_anti_cancer_drug_therapy' => 'Curative',
            'goal_other' => null,
        ]],
        'radiotherapies' => [[
            'date_started' => '2026-06-03',
            'date_ended' => '2026-06-12',
            'total_planned_dose' => 50.5,
            'total_delivered_fraction' => 25,
            'dose_per_fraction' => 2.02,
            'total_number_of_days' => 10,
            'radiotherapy_type' => 'External beam radiotherapy',
            'radiotherapy_type_specifics' => null,
            'target_site' => 'Primary site',
            'target_site_specifics' => null,
            'goal_of_radiotherapy' => 'Curative',
            'goal_other' => null,
        ]],
        'theranostics' => [[
            'date_started' => '2026-06-04',
            'date_last_anti_cancer_drug_therapy' => '2026-06-11',
            'drugs_given' => 'Targeted radioligand',
            'drug_types' => ['Targeted Therapy'],
            'drug_type_other' => null,
            'treatment_phase' => 'Palliative',
            'treatment_phase_other' => null,
            'cycle_no' => 2,
            'goal_of_anti_cancer_drug_therapy' => 'Control',
            'goal_other' => null,
        ]],
        'palliative_cares' => [[
            'date_started' => '2026-06-05',
            'date_last_palliative_care' => '2026-06-13',
            'reasons' => ['Pain Control', 'Others'],
            'reason_specifics' => 'Family support',
            'type_of_care_integration' => 'Concurrent-Palliative',
            'type_of_care_integration_other' => null,
            'goal_of_care' => 'Supportive',
            'goal_of_care_other' => null,
        ]],
        'other_cancer_directed_therapies' => [[
            'date_of_therapy' => '2026-06-06',
            'type_of_cancer_directed_therapy' => 'Immunotherapy',
            'type_of_cancer_directed_therapy_other' => null,
            'goal_of_cancer_directed_therapy' => 'Control',
            'goal_of_cancer_directed_therapy_other' => null,
        ]],
    ]);

    $followUpForm->refresh()->load([
        'surgeryRecords',
        'antiCancerDrugTherapyRecords',
        'radiotherapyRecords',
        'theranosticRecords',
        'palliativeCareRecords',
        'otherCancerDirectedTherapyRecords',
    ]);

    expect($followUpForm->surgeryRecords)->toHaveCount(1)
        ->and($followUpForm->surgeryRecords->first()->patient_follow_up_form_id)->toBe($followUpForm->id)
        ->and($followUpForm->surgeryRecords->first()->surgery_description)->toBe('Enucleation of the Right Eye')
        ->and($followUpForm->antiCancerDrugTherapyRecords)->toHaveCount(1)
        ->and($followUpForm->antiCancerDrugTherapyRecords->first()->drug_types)->toBe(['Cytotoxic', 'Others'])
        ->and($followUpForm->radiotherapyRecords)->toHaveCount(1)
        ->and($followUpForm->radiotherapyRecords->first()->total_delivered_fraction)->toBe(25)
        ->and($followUpForm->theranosticRecords)->toHaveCount(1)
        ->and($followUpForm->theranosticRecords->first()->cycle_no)->toBe(2)
        ->and($followUpForm->palliativeCareRecords)->toHaveCount(1)
        ->and($followUpForm->palliativeCareRecords->first()->reasons)->toBe(['Pain Control', 'Others'])
        ->and($followUpForm->otherCancerDirectedTherapyRecords)->toHaveCount(1)
        ->and($followUpForm->otherCancerDirectedTherapyRecords->first()->goal_of_cancer_directed_therapy)->toBe('Control');
});

test('patient follow up treatment records are replaced on sync', function (): void {
    $followUpForm = PatientFollowUpForm::create([
        'patient_health_facility_id_no' => '0000000002',
    ]);

    $followUpForm->syncTreatmentRecords([
        'surgeries' => [[
            'surgery_date' => '2026-06-01',
            'surgery_rvs_code' => null,
            'surgery_description' => 'Initial surgery',
            'surgery_goal' => 'Diagnostic',
        ]],
    ]);

    $followUpForm->syncTreatmentRecords([
        'surgeries' => [[
            'surgery_date' => '2026-06-15',
            'surgery_rvs_code' => null,
            'surgery_description' => 'Updated surgery',
            'surgery_goal' => 'Definitive',
        ]],
    ]);

    $followUpForm->refresh()->load('surgeryRecords');

    expect($followUpForm->surgeryRecords)->toHaveCount(1)
        ->and($followUpForm->surgeryRecords->first()->surgery_description)->toBe('Updated surgery');
});
