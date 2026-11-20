<?php

use App\Filament\Pages\PatientForms;
use App\Models\CancerDiagnose;
use App\Models\FormDemographics;
use App\Models\PatientFollowUpForm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->juan = FormDemographics::query()->create([
        'health_facility_id_no' => '0000012345',
        'patient_first_name' => 'Juan',
        'patient_middle_name' => 'Santos',
        'patient_surname' => 'Dela Cruz',
        'sex_at_birth' => 'male',
    ]);

    $this->maria = FormDemographics::query()->create([
        'health_facility_id_no' => '0000067890',
        'patient_first_name' => 'Maria',
        'patient_surname' => 'Reyes',
        'sex_at_birth' => 'female',
    ]);
});

function patientFormsUser(array $permissions = ['View:PatientForms', 'View:CancerDiagnose', 'View:PatientFollowUpForm']): User
{
    $user = User::factory()->create();

    foreach ($permissions as $permission) {
        $user->givePermissionTo(Permission::findOrCreate($permission, 'web'));
    }

    return $user;
}

test('the patient forms page requires its permission', function (): void {
    $this->actingAs(User::factory()->create());

    expect(PatientForms::canAccess())->toBeFalse();

    $this->actingAs(patientFormsUser());

    expect(PatientForms::canAccess())->toBeTrue();

    Livewire::test(PatientForms::class)
        ->assertSuccessful()
        ->assertSee('Find a Patient');
});

test('patients can be searched by health facility id number', function (): void {
    $this->actingAs(patientFormsUser());

    Livewire::test(PatientForms::class)
        ->set('search', '12345')
        ->assertSee('Juan Santos Dela Cruz')
        ->assertDontSee('Maria Reyes');
});

test('patients can be searched by full name in any order', function (): void {
    $this->actingAs(patientFormsUser());

    Livewire::test(PatientForms::class)
        ->set('search', 'cruz juan')
        ->assertSee('0000012345')
        ->assertDontSee('0000067890');
});

test('patients can be searched by the hospital id stored on their diagnosis', function (): void {
    CancerDiagnose::query()->create([
        'form_demographic_id' => $this->maria->id,
        'patient_health_facility_id_no' => 'HOSP-778',
    ]);

    $this->actingAs(patientFormsUser());

    Livewire::test(PatientForms::class)
        ->set('search', 'hosp-778')
        ->assertSee('Maria Reyes')
        ->assertDontSee('Juan Santos Dela Cruz');
});

test('short or unmatched searches return no patients', function (): void {
    $this->actingAs(patientFormsUser());

    Livewire::test(PatientForms::class)
        ->set('search', 'J')
        ->assertSee('Keep typing')
        ->assertDontSee('Santos')
        ->set('search', 'Nobody Here')
        ->assertSee('No patient found');
});

test('form 1 and form 2 can be previewed for a selected patient', function (): void {
    $diagnosis = CancerDiagnose::query()->create([
        'form_demographic_id' => $this->juan->id,
    ]);

    $followUp = PatientFollowUpForm::query()->create([
        'form_demographic_id' => $this->juan->id,
        'follow_up_last_encounter_date' => '2026-06-16',
    ]);

    $this->actingAs(patientFormsUser());

    Livewire::test(PatientForms::class)
        ->set('search', 'Juan')
        ->call('previewForm', $this->juan->id, PatientForms::FORM_ONE)
        ->assertSet('activeForm', PatientForms::FORM_ONE)
        ->assertSet('selectedDiagnosisId', $diagnosis->id)
        ->assertSee(route('form-details', $diagnosis), escape: false)
        ->call('showForm', PatientForms::FORM_TWO)
        ->assertSet('activeForm', PatientForms::FORM_TWO)
        ->assertSee(route('follow-up-form-details', $followUp), escape: false)
        ->call('clearSelection')
        ->assertSet('selectedPatientId', null)
        ->assertSet('activeForm', null);
});

test('a missing form shows an empty state instead of a preview', function (): void {
    $this->actingAs(patientFormsUser());

    Livewire::test(PatientForms::class)
        ->call('previewForm', $this->maria->id, PatientForms::FORM_TWO)
        ->assertSee('No Form 2 (follow-up) has been recorded for this patient yet.');
});

test('selecting a diagnosis that belongs to another patient is ignored', function (): void {
    CancerDiagnose::query()->create(['form_demographic_id' => $this->juan->id]);
    $otherDiagnosis = CancerDiagnose::query()->create(['form_demographic_id' => $this->maria->id]);

    $this->actingAs(patientFormsUser());

    Livewire::test(PatientForms::class)
        ->call('previewForm', $this->juan->id, PatientForms::FORM_ONE)
        ->set('selectedDiagnosisId', $otherDiagnosis->id)
        ->assertSet('selectedDiagnosisId', null);
});

test('the form 2 print view renders the follow up details', function (): void {
    $followUp = PatientFollowUpForm::query()->create([
        'form_demographic_id' => $this->juan->id,
        'patient_health_facility_id_no' => '0000012345',
        'follow_up_last_encounter_date' => '2026-06-16',
        'procedures_administered' => ['Surgery'],
        'treatment_status' => 'Ongoing',
        'disease_outcome' => 'Stable Disease',
    ]);

    $followUp->syncTreatmentRecords([
        'procedures_administered' => ['Surgery'],
        'surgeries' => [[
            'surgery_date' => '2026-06-01',
            'surgery_rvs_code' => '12345',
            'surgery_description' => 'Enucleation of the Right Eye',
            'surgery_goal' => 'Definitive',
        ]],
    ]);

    $this->actingAs(patientFormsUser(['View:PatientFollowUpForm']))
        ->get(route('follow-up-form-details', $followUp))
        ->assertSuccessful()
        ->assertSee('FORM 2A: PATIENT FOLLOW-UP FORM')
        ->assertSee('Juan Santos Dela Cruz')
        ->assertSee('2026-06-16')
        ->assertSee('Enucleation of the Right Eye');
});

test('the form 2 print view requires permission', function (): void {
    $followUp = PatientFollowUpForm::query()->create([
        'form_demographic_id' => $this->juan->id,
    ]);

    $this->get(route('follow-up-form-details', $followUp))
        ->assertRedirect(route('filament.administrator.auth.login'));

    $this->actingAs(User::factory()->create())
        ->get(route('follow-up-form-details', $followUp))
        ->assertForbidden();
});
