<?php

use App\Filament\Pages\Reports;
use App\Models\FormDemographics;
use App\Models\PatientFollowUpForm;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('form 2c report aggregates latest treatment status by sex', function () {
    $maleDemographic = FormDemographics::query()->create([
        'sex_at_birth' => 'male',
    ]);

    $femaleDemographic = FormDemographics::query()->create([
        'sex_at_birth' => 'female',
    ]);

    // Older entry for same male patient in range; newer one should be used.
    PatientFollowUpForm::query()->create([
        'form_demographic_id' => $maleDemographic->id,
        'patient_health_facility_id_no' => '6045',
        'follow_up_last_encounter_date' => '2024-02-01',
        'treatment_status' => 'Completed',
    ]);

    PatientFollowUpForm::query()->create([
        'form_demographic_id' => $maleDemographic->id,
        'patient_health_facility_id_no' => '6045',
        'follow_up_last_encounter_date' => '2024-03-01',
        'treatment_status' => 'Ongoing',
    ]);

    // Different facility: excluded by Form 2C facility scope.
    PatientFollowUpForm::query()->create([
        'form_demographic_id' => $femaleDemographic->id,
        'patient_health_facility_id_no' => '7777',
        'follow_up_last_encounter_date' => '2024-03-03',
        'treatment_status' => 'Abandonment',
    ]);

    // Excluded: outside selected range.
    PatientFollowUpForm::query()->create([
        'form_demographic_id' => $femaleDemographic->id,
        'patient_health_facility_id_no' => '7777',
        'follow_up_last_encounter_date' => '2024-05-03',
        'treatment_status' => 'Completed',
    ]);

    $page = app(Reports::class);

    $method = new ReflectionMethod(Reports::class, 'buildForm2CReport');
    $method->setAccessible(true);

    $report = $method->invoke($page, [
        'from_date' => '2024-01-01',
        'to_date' => '2024-03-31',
        'facility_name' => 'National Children\'s Hospital',
        'facility_registration_no' => '6045',
    ]);

    $rows = collect($report['rows']);

    $ongoing = $rows->firstWhere('label', 'Ongoing');
    $abandonment = $rows->firstWhere('label', 'Abandonment');
    $completed = $rows->firstWhere('label', 'Completed');

    expect($ongoing)->not->toBeNull();
    expect($ongoing['male'])->toBe(1);
    expect($ongoing['female'])->toBe(0);
    expect($ongoing['total'])->toBe(1);

    expect($abandonment)->not->toBeNull();
    expect($abandonment['male'])->toBe(0);
    expect($abandonment['female'])->toBe(0);
    expect($abandonment['total'])->toBe(0);

    expect($completed)->not->toBeNull();
    expect($completed['total'])->toBe(0);
});
