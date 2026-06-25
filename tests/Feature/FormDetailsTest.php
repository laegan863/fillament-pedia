<?php

use App\Models\CancerDiagnose;
use App\Models\FormDemographics;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('form details render when staging used is stored as a legacy single value', function (): void {
    $cancerDiagnose = cancerDiagnoseForForm([
        'staging_used' => 'Ann Arbor',
    ]);

    $this->get(route('form-details', $cancerDiagnose))
        ->assertSuccessful()
        ->assertSee('Ann Arbor Staging System');
});

test('form details render when staging used stores multiple values', function (): void {
    $cancerDiagnose = cancerDiagnoseForForm([
        'staging_used' => ['Ann Arbor', 'Other'],
    ]);

    $this->get(route('form-details', $cancerDiagnose))
        ->assertSuccessful()
        ->assertSee('Ann Arbor Staging System')
        ->assertSee('Other');
});

function cancerDiagnoseForForm(array $attributes = []): CancerDiagnose
{
    $formDetails = FormDemographics::query()->create([
        'health_facility_id_no' => '1234567890',
        'patient_first_name' => 'Test',
        'patient_surname' => 'Patient',
    ]);

    return CancerDiagnose::query()->create([
        'form_demographic_id' => $formDetails->id,
        ...$attributes,
    ]);
}
