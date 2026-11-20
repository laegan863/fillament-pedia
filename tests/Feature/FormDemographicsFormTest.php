<?php

use App\Filament\Resources\FormDemographics\Pages\CreateFormDemographics;
use App\Filament\Resources\FormDemographics\Schemas\FormDemographicsForm;
use App\Models\FormDemographics;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $user = User::factory()->create();

    $user->givePermissionTo(
        Permission::findOrCreate('ViewAny:FormDemographics', 'web'),
        Permission::findOrCreate('Create:FormDemographics', 'web'),
    );

    $this->actingAs($user);
});

/**
 * @return array<string, string>
 */
function requiredDemographicsFormData(): array
{
    return [
        'first_encounter_date' => '2026-01-15',
        'health_facility_id_no' => '1234567890',
        'patient_first_name' => 'Juan',
        'patient_surname' => 'Dela Cruz',
        'sex_at_birth' => 'male',
        'date_of_birth' => '2020-05-01',
        'birth_province' => 'Negros Occidental',
        'birth_city_municipality' => 'Cauayan',
        'guardian_first_name' => 'Maria',
        'guardian_surname' => 'Dela Cruz',
        'relationship_to_patient' => 'mother',
    ];
}

test('n/a fields are saved empty and skip validation even when the hidden input still holds a value', function (): void {
    Livewire::test(CreateFormDemographics::class)
        ->fillForm([
            ...requiredDemographicsFormData(),
            'philhealth_pin_na' => true,
            'philhealth_pin' => '12-345678901-2',
            'mobile_contact_na' => true,
            'mobile_contact_no' => 'not a phone number!',
            'email_na' => true,
            'email_address' => 'dasdsafsafa',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(FormDemographics::query()->sole())
        ->philhealth_pin_na->toBeTrue()
        ->philhealth_pin->toBeNull()
        ->mobile_contact_na->toBeTrue()
        ->mobile_contact_no->toBeNull()
        ->email_na->toBeTrue()
        ->email_address->toBeNull();
});

test('contact fields are still validated and saved when n/a is not ticked', function (): void {
    Livewire::test(CreateFormDemographics::class)
        ->fillForm([
            ...requiredDemographicsFormData(),
            'mobile_contact_no' => 'not a phone number!',
            'email_address' => 'dasdsafsafa',
        ])
        ->call('create')
        ->assertHasFormErrors(['mobile_contact_no', 'email_address']);

    Livewire::test(CreateFormDemographics::class)
        ->fillForm([
            ...requiredDemographicsFormData(),
            'philhealth_pin' => '12-345678901-2',
            'mobile_contact_no' => '09171234567',
            'email_address' => 'guardian@example.com',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(FormDemographics::query()->sole())
        ->philhealth_pin->toBe('12-345678901-2')
        ->mobile_contact_no->toBe('09171234567')
        ->email_address->toBe('guardian@example.com');
});

test('n/a checkboxes are clearly labelled', function (): void {
    Livewire::test(CreateFormDemographics::class)
        ->assertSee('No PhilHealth PIN (N/A)')
        ->assertSee('No Mobile Contact No. (N/A)')
        ->assertSee('No Email Address (N/A)');
});

test('nationality is saved as the country and keeps legacy values', function (): void {
    Livewire::test(CreateFormDemographics::class)
        ->assertSchemaStateSet(['nationality' => 'PHILIPPINES'])
        ->assertSee('Philippines (Filipino)')
        ->fillForm([
            ...requiredDemographicsFormData(),
            'nationality' => 'JAPAN',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(FormDemographics::query()->sole()->nationality)->toBe('JAPAN');

    Livewire::test(CreateFormDemographics::class)
        ->fillForm([
            ...requiredDemographicsFormData(),
            'health_facility_id_no' => '2234567890',
            'nationality' => 'SOME LEGACY VALUE',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(FormDemographics::query()->where('health_facility_id_no', '2234567890')->sole()->nationality)
        ->toBe('SOME LEGACY VALUE');
});

test('hospital system nationalities are converted to the stored country', function (): void {
    expect(FormDemographicsForm::countryFromNationality('Filipino'))->toBe('PHILIPPINES')
        ->and(FormDemographicsForm::countryFromNationality(' JAPANESE '))->toBe('JAPAN')
        ->and(FormDemographicsForm::countryFromNationality('Martian'))->toBe('MARTIAN')
        ->and(FormDemographicsForm::countryFromNationality(null))->toBeNull();
});
