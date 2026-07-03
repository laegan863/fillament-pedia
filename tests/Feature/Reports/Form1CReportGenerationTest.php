<?php

use App\Filament\Pages\Reports;
use App\Models\CancerDiagnose;
use App\Models\FormDemographics;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('form 1c report aggregates disease status without facility and status filtering conditions', function () {
    $maleDemographic = FormDemographics::query()->create([
        'sex_at_birth' => 'male',
    ]);

    $femaleDemographic = FormDemographics::query()->create([
        'sex_at_birth' => 'female',
    ]);

    // Same status, different facility code: both must be included.
    CancerDiagnose::query()->create([
        'form_demographic_id' => $maleDemographic->id,
        'health_facility_registration_no' => '1111',
        'current_status_of_cancer' => 'Newly diagnosed',
        'diagnosis_date' => '2024-02-10',
    ]);

    CancerDiagnose::query()->create([
        'form_demographic_id' => $femaleDemographic->id,
        'health_facility_registration_no' => '9999',
        'current_status_of_cancer' => 'Newly diagnosed',
        'diagnosis_date' => '2024-02-11',
    ]);

    // Old case status must also be counted in Form 1C.
    CancerDiagnose::query()->create([
        'form_demographic_id' => $femaleDemographic->id,
        'health_facility_registration_no' => '9999',
        'current_status_of_cancer' => 'Old case to center, relapse/refractory',
        'diagnosis_date' => '2024-03-10',
    ]);

    // Excluded: outside selected date range.
    CancerDiagnose::query()->create([
        'form_demographic_id' => $maleDemographic->id,
        'health_facility_registration_no' => '1111',
        'current_status_of_cancer' => 'New case to center, no treatment',
        'diagnosis_date' => '2024-05-01',
    ]);

    $page = app(Reports::class);

    $method = new ReflectionMethod(Reports::class, 'buildForm1CReport');
    $method->setAccessible(true);

    $report = $method->invoke($page, [
        'from_date' => '2024-01-01',
        'to_date' => '2024-03-31',
        'facility_name' => 'Any Facility',
        'facility_registration_no' => '6045',
    ]);

    $rows = collect($report['rows']);

    $newlyDiagnosed = $rows->firstWhere('label', 'Newly diagnosed');
    $relapse = $rows->firstWhere('label', 'Old case to center, relapse/refractory');
    $noTreatment = $rows->firstWhere('label', 'New case to center, no treatment');

    expect($newlyDiagnosed)->not->toBeNull();
    expect($newlyDiagnosed['male'])->toBe(1);
    expect($newlyDiagnosed['female'])->toBe(1);
    expect($newlyDiagnosed['total'])->toBe(2);

    expect($relapse)->not->toBeNull();
    expect($relapse['male'])->toBe(0);
    expect($relapse['female'])->toBe(1);
    expect($relapse['total'])->toBe(1);

    expect($noTreatment)->not->toBeNull();
    expect($noTreatment['total'])->toBe(0);
});
