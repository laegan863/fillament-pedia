<?php

use App\Filament\Pages\Reports;
use App\Models\FormDemographics;
use App\Models\PatientFollowUpForm;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('form 2b report aggregates latest disease outcome by sex', function () {
    $maleDemographic = FormDemographics::query()->create([
        'sex_at_birth' => 'male',
    ]);

    $femaleDemographic = FormDemographics::query()->create([
        'sex_at_birth' => 'female',
    ]);

    // Older entry for same male patient within range, should be replaced by later entry.
    PatientFollowUpForm::query()->create([
        'form_demographic_id' => $maleDemographic->id,
        'patient_health_facility_id_no' => '6045',
        'follow_up_last_encounter_date' => '2024-02-01',
        'disease_outcome' => 'Stable Disease',
    ]);

    PatientFollowUpForm::query()->create([
        'form_demographic_id' => $maleDemographic->id,
        'patient_health_facility_id_no' => '6045',
        'follow_up_last_encounter_date' => '2024-03-01',
        'disease_outcome' => 'Progressive/Refractory',
    ]);

    PatientFollowUpForm::query()->create([
        'form_demographic_id' => $femaleDemographic->id,
        'patient_health_facility_id_no' => '6045',
        'follow_up_last_encounter_date' => '2024-03-03',
        'disease_outcome' => 'Death (Treatment-Related)',
    ]);

    // Excluded: different facility.
    PatientFollowUpForm::query()->create([
        'form_demographic_id' => $femaleDemographic->id,
        'patient_health_facility_id_no' => '7777',
        'follow_up_last_encounter_date' => '2024-03-04',
        'disease_outcome' => 'Stable Disease',
    ]);

    $page = app(Reports::class);

    $method = new ReflectionMethod(Reports::class, 'buildForm2BReport');
    $method->setAccessible(true);

    $report = $method->invoke($page, [
        'from_date' => '2024-01-01',
        'to_date' => '2024-03-31',
        'facility_name' => 'National Children\'s Hospital',
        'facility_registration_no' => '6045',
    ]);

    $rows = collect($report['rows']);

    $progression = $rows->firstWhere('label', 'Disease Progression');
    $treatmentDeath = $rows->firstWhere('label', 'Death (Treatment related)');
    $stableDisease = $rows->firstWhere('label', 'Stable Disease');

    expect($progression)->not->toBeNull();
    expect($progression['male'])->toBe(1);
    expect($progression['female'])->toBe(0);
    expect($progression['total'])->toBe(1);

    expect($treatmentDeath)->not->toBeNull();
    expect($treatmentDeath['male'])->toBe(0);
    expect($treatmentDeath['female'])->toBe(1);
    expect($treatmentDeath['total'])->toBe(1);

    expect($stableDisease)->not->toBeNull();
    expect($stableDisease['total'])->toBe(0);
});
