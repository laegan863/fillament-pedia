<?php

use App\Filament\Pages\Reports;
use App\Models\CancerDiagnose;
use App\Models\FormDemographics;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('form 1b report aggregates gicc index categories by sex', function () {
    $maleDemographic = FormDemographics::query()->create([
        'sex_at_birth' => 'male',
    ]);

    $femaleDemographic = FormDemographics::query()->create([
        'sex_at_birth' => 'female',
    ]);

    CancerDiagnose::query()->create([
        'form_demographic_id' => $maleDemographic->id,
        'health_facility_registration_no' => '6045',
        'iccc_specific_classification' => 'Retinoblastoma',
        'diagnosis_date' => '2024-02-10',
        'current_status_of_cancer' => 'Newly diagnosed',
    ]);

    CancerDiagnose::query()->create([
        'form_demographic_id' => $femaleDemographic->id,
        'health_facility_registration_no' => '6045',
        'iccc_specific_classification' => 'Hodgkin lymphoma',
        'diagnosis_date' => '2024-02-10',
        'current_status_of_cancer' => 'New case to center, no treatment',
    ]);

    // Excluded: wrong status.
    CancerDiagnose::query()->create([
        'form_demographic_id' => $femaleDemographic->id,
        'health_facility_registration_no' => '6045',
        'iccc_specific_classification' => 'Wilms tumor',
        'diagnosis_date' => '2024-02-10',
        'current_status_of_cancer' => 'Old case to center, returning',
    ]);

    // Excluded: outside date range.
    CancerDiagnose::query()->create([
        'form_demographic_id' => $maleDemographic->id,
        'health_facility_registration_no' => '6045',
        'iccc_specific_classification' => 'Burkitt lymphoma',
        'diagnosis_date' => '2024-05-05',
        'current_status_of_cancer' => 'Newly diagnosed',
    ]);

    $page = app(Reports::class);

    $method = new ReflectionMethod(Reports::class, 'buildForm1BReport');
    $method->setAccessible(true);

    $report = $method->invoke($page, [
        'from_date' => '2024-01-01',
        'to_date' => '2024-03-31',
        'facility_name' => 'National Children\'s Hospital',
        'facility_registration_no' => '6045',
    ]);

    $rows = collect($report['rows']);

    $retinoblastoma = $rows->firstWhere('label', 'Retinoblastoma');
    $hodgkin = $rows->firstWhere('label', 'Hodgkin Lymphoma');
    $burkitt = $rows->firstWhere('label', 'Burkitt Lymphoma');

    expect($retinoblastoma)->not->toBeNull();
    expect($retinoblastoma['male'])->toBe(1);
    expect($retinoblastoma['female'])->toBe(0);
    expect($retinoblastoma['total'])->toBe(1);

    expect($hodgkin)->not->toBeNull();
    expect($hodgkin['male'])->toBe(0);
    expect($hodgkin['female'])->toBe(1);
    expect($hodgkin['total'])->toBe(1);

    expect($burkitt)->not->toBeNull();
    expect($burkitt['total'])->toBe(0);
});
