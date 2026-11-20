<?php

use App\Filament\Resources\PatientFollowUpForms\Pages\EditPatientFollowUpForm;
use App\Models\PatientFollowUpForm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $user = User::factory()->create();

    $user->givePermissionTo(
        Permission::findOrCreate('ViewAny:PatientFollowUpForm', 'web'),
        Permission::findOrCreate('View:PatientFollowUpForm', 'web'),
        Permission::findOrCreate('Update:PatientFollowUpForm', 'web'),
    );

    $this->actingAs($user);
});

test('editing a follow up form with "No" boolean answers saves without null constraint errors', function (): void {
    $followUpForm = PatientFollowUpForm::create([
        'patient_health_facility_id_no' => '0000000001',
        'follow_up_last_encounter_date' => '2026-06-16',
        'treatment_status' => 'Ongoing',
        'has_change_in_diagnosis' => false,
        'has_more_than_one_primary_site' => false,
        'availed_financial_support' => false,
    ]);

    Livewire::test(EditPatientFollowUpForm::class, ['record' => $followUpForm->getRouteKey()])
        ->assertSchemaStateSet([
            'has_change_in_diagnosis' => '0',
            'has_more_than_one_primary_site' => '0',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $followUpForm->refresh();

    expect($followUpForm->has_change_in_diagnosis)->toBeFalse()
        ->and($followUpForm->has_more_than_one_primary_site)->toBeFalse()
        ->and($followUpForm->availed_financial_support)->toBeFalse();
});

test('editing a follow up form keeps "Yes" answer for more than one primary site', function (): void {
    $followUpForm = PatientFollowUpForm::create([
        'patient_health_facility_id_no' => '0000000002',
        'follow_up_last_encounter_date' => '2026-06-16',
        'treatment_status' => 'Ongoing',
        'has_more_than_one_primary_site' => true,
    ]);

    Livewire::test(EditPatientFollowUpForm::class, ['record' => $followUpForm->getRouteKey()])
        ->assertSchemaStateSet(['has_more_than_one_primary_site' => '1'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($followUpForm->refresh()->has_more_than_one_primary_site)->toBeTrue();
});
