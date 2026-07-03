<?php

use App\Filament\Pages\Reports;
use App\Models\CancerDiagnose;
use App\Models\FormDemographics;
use App\Models\GeneralClassification;
use App\Models\ParentClassification;
use App\Models\SpecificClassification;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('form 1a report aggregates newly diagnosed cases by stage and sex', function () {
    $general = GeneralClassification::query()->create([
        'name' => 'Leukemias, Myeloproliferative Diseases, Myelodysplastic Disease',
    ]);

    $parent = ParentClassification::query()->create([
        'name' => 'Lymphoid Leukemia',
    ]);

    $specific = SpecificClassification::query()->create([
        'name' => 'Precursor cell leukemia',
        'general_classification_id' => $general->id,
        'parent_classification_id' => $parent->id,
    ]);

    $maleDemographic = FormDemographics::query()->create([
        'sex_at_birth' => 'male',
    ]);

    $femaleDemographic = FormDemographics::query()->create([
        'sex_at_birth' => 'female',
    ]);

    CancerDiagnose::query()->create([
        'form_demographic_id' => $maleDemographic->id,
        'health_facility_registration_no' => '6045',
        'iccc_general_classification' => $general->name,
        'iccc_parent_classification' => $parent->name,
        'iccc_specific_classification' => $specific->name,
        'diagnosis_date' => '2024-02-10',
        'current_status_of_cancer' => 'New case to center, no treatment',
        'clinical_stage' => 'Stage I',
    ]);

    CancerDiagnose::query()->create([
        'form_demographic_id' => $femaleDemographic->id,
        'health_facility_registration_no' => '6045',
        'iccc_general_classification' => $general->name,
        'iccc_parent_classification' => $parent->name,
        'iccc_specific_classification' => $specific->name,
        'diagnosis_date' => '2024-03-05',
        'current_status_of_cancer' => 'Newly diagnosed',
        'clinical_stage' => 'Stage II',
    ]);

    // Excluded: wrong status.
    CancerDiagnose::query()->create([
        'form_demographic_id' => $femaleDemographic->id,
        'health_facility_registration_no' => '6045',
        'iccc_general_classification' => $general->name,
        'iccc_parent_classification' => $parent->name,
        'iccc_specific_classification' => $specific->name,
        'diagnosis_date' => '2024-03-07',
        'current_status_of_cancer' => 'Old case to center, returning',
        'clinical_stage' => 'Stage II',
    ]);

    // Excluded: outside selected quarter.
    CancerDiagnose::query()->create([
        'form_demographic_id' => $maleDemographic->id,
        'health_facility_registration_no' => '6045',
        'iccc_general_classification' => $general->name,
        'iccc_parent_classification' => $parent->name,
        'iccc_specific_classification' => $specific->name,
        'diagnosis_date' => '2024-05-01',
        'current_status_of_cancer' => 'Newly diagnosed',
        'clinical_stage' => 'Stage III',
    ]);

    $page = app(Reports::class);

    $method = new ReflectionMethod(Reports::class, 'buildForm1AReport');
    $method->setAccessible(true);

    $report = $method->invoke($page, [
        'from_date' => '2024-01-01',
        'to_date' => '2024-03-31',
        'facility_name' => 'National Children\'s Hospital',
        'facility_registration_no' => '6045',
    ]);

    $group = collect($report['groups'])
        ->firstWhere('name', 'Leukemias, Myeloproliferative Diseases, Myelodysplastic Disease');

    expect($group)->not->toBeNull();

    $row = collect($group['rows'])->firstWhere('name', 'Precursor cell leukemia');

    expect($row)->not->toBeNull();
    expect($row['totals']['stages']['Stage I']['M'])->toBe(1);
    expect($row['totals']['stages']['Stage II']['F'])->toBe(1);
    expect($row['totals']['stages']['Stage III']['T'])->toBe(0);
    expect($row['totals']['total_cases'])->toBe(2);
});
