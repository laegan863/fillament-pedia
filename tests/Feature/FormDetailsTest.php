<?php

use App\Models\CancerDiagnose;
use App\Models\FormDemographics;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

test('form details render when staging used is stored as a legacy single value', function (): void {
    $cancerDiagnose = cancerDiagnoseForForm([
        'staging_used' => 'Ann Arbor',
    ]);

    $this->actingAs(userWhoCanViewCancerDiagnoses())
        ->get(route('form-details', $cancerDiagnose))
        ->assertSuccessful()
        ->assertSee('Ann Arbor Staging System');
});

test('form details render when staging used stores multiple values', function (): void {
    $cancerDiagnose = cancerDiagnoseForForm([
        'staging_used' => ['Ann Arbor', 'Other'],
    ]);

    $this->actingAs(userWhoCanViewCancerDiagnoses())
        ->get(route('form-details', $cancerDiagnose))
        ->assertSuccessful()
        ->assertSee('Ann Arbor Staging System')
        ->assertSee('Other');
});

test('guests are redirected to the login page instead of seeing form details', function (): void {
    $cancerDiagnose = cancerDiagnoseForForm();

    $this->get(route('form-details', $cancerDiagnose))
        ->assertRedirect(route('filament.administrator.auth.login'));
});

test('users without permission to view cancer diagnoses cannot see form details', function (): void {
    $cancerDiagnose = cancerDiagnoseForForm();

    $this->actingAs(User::factory()->create())
        ->get(route('form-details', $cancerDiagnose))
        ->assertForbidden();
});

function userWhoCanViewCancerDiagnoses(): User
{
    $user = User::factory()->create();

    $user->givePermissionTo(Permission::findOrCreate('View:CancerDiagnose', 'web'));

    return $user;
}

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
