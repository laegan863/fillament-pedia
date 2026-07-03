<?php

use App\Filament\Pages\Reports;
use App\Models\FormDemographics;
use App\Models\PatientFollowUpForm;
use App\Models\PatientFollowUpFormAntiCancerDrugTherapy;
use App\Models\PatientFollowUpFormPalliativeCare;
use App\Models\PatientFollowUpFormSurgery;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('form 2a report aggregates treatments by patient count and frequency', function () {
    $maleDemographic = FormDemographics::query()->create([
        'sex_at_birth' => 'male',
    ]);

    $femaleDemographic = FormDemographics::query()->create([
        'sex_at_birth' => 'female',
    ]);

    $followUpA = PatientFollowUpForm::query()->create([
        'form_demographic_id' => $maleDemographic->id,
        'patient_health_facility_id_no' => '6045',
        'follow_up_last_encounter_date' => '2024-02-10',
    ]);

    $followUpB = PatientFollowUpForm::query()->create([
        'form_demographic_id' => $femaleDemographic->id,
        'patient_health_facility_id_no' => '6045',
        'follow_up_last_encounter_date' => '2024-02-11',
    ]);

    PatientFollowUpFormSurgery::query()->create([
        'patient_follow_up_form_id' => $followUpA->id,
        'surgery_date' => '2024-02-10',
        'surgery_rvs_code' => null,
        'surgery_description' => 'Procedure A',
        'surgery_goal' => 'Definitive',
    ]);

    PatientFollowUpFormAntiCancerDrugTherapy::query()->create([
        'patient_follow_up_form_id' => $followUpA->id,
        'date_started' => '2024-02-10',
        'date_last_anti_cancer_drug_therapy' => '2024-02-11',
        'drugs_given' => 'Drug A',
        'drug_types' => ['Cytotoxic'],
        'drug_type_other' => null,
        'treatment_phase' => 'Induction',
        'treatment_phase_other' => null,
        'cycle_no' => 1,
        'goal_of_anti_cancer_drug_therapy' => 'Curative',
        'goal_other' => null,
    ]);

    PatientFollowUpFormAntiCancerDrugTherapy::query()->create([
        'patient_follow_up_form_id' => $followUpA->id,
        'date_started' => '2024-02-12',
        'date_last_anti_cancer_drug_therapy' => '2024-02-12',
        'drugs_given' => 'Drug B',
        'drug_types' => ['Cytotoxic'],
        'drug_type_other' => null,
        'treatment_phase' => 'Consolidation',
        'treatment_phase_other' => null,
        'cycle_no' => 2,
        'goal_of_anti_cancer_drug_therapy' => 'Curative',
        'goal_other' => null,
    ]);

    PatientFollowUpFormPalliativeCare::query()->create([
        'patient_follow_up_form_id' => $followUpB->id,
        'date_started' => '2024-02-11',
        'date_last_palliative_care' => '2024-02-11',
        'reasons' => ['Pain Control'],
        'reason_specifics' => null,
        'type_of_care_integration' => 'Concurrent-Curative',
        'type_of_care_integration_other' => null,
        'goal_of_care' => 'Palliative',
        'goal_of_care_other' => null,
    ]);

    $page = app(Reports::class);

    $method = new ReflectionMethod(Reports::class, 'buildForm2AReport');
    $method->setAccessible(true);

    $report = $method->invoke($page, [
        'from_date' => '2024-01-01',
        'to_date' => '2024-03-31',
        'facility_name' => 'National Children\'s Hospital',
        'facility_registration_no' => '6045',
    ]);

    $rows = collect($report['rows']);

    $surgery = $rows->firstWhere('label', 'Surgery');
    $cytotoxic = $rows->firstWhere('label', 'Anti-Cancer Drug Therapy: Cytotoxic');
    $palliative = $rows->firstWhere('label', 'Palliative Care: Concurrent-Curative');
    $nonReceipt = $rows->firstWhere('label', 'Non-receipt of Treatment');

    expect($surgery)->not->toBeNull();
    expect($surgery['patients'])->toBe(1);
    expect($surgery['frequency'])->toBe(1);

    expect($cytotoxic)->not->toBeNull();
    expect($cytotoxic['patients'])->toBe(1);
    expect($cytotoxic['frequency'])->toBe(2);

    expect($palliative)->not->toBeNull();
    expect($palliative['patients'])->toBe(1);
    expect($palliative['frequency'])->toBe(1);

    expect($nonReceipt)->not->toBeNull();
    expect($nonReceipt['patients'])->toBe(0);
});
