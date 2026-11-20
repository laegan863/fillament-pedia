<?php

use App\Filament\Resources\FormDemographics\Pages\CreateFormDemographics;
use App\Models\User;
use App\Services\GuidedTour;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

/**
 * @param  list<string>  $permissions
 */
function guidedTourUser(array $permissions = []): User
{
    $user = User::factory()->create();

    foreach ($permissions as $permission) {
        $user->givePermissionTo(Permission::findOrCreate($permission, 'web'));
    }

    return $user;
}

test('routes outside the administrator panel have no tour', function (?string $routeName): void {
    expect(app(GuidedTour::class)->forRoute($routeName))->toBeNull();
})->with([null, '', 'form-details', 'livewire.update']);

test('each panel page gets its own versioned tour', function (string $routeName, string $title): void {
    $tour = app(GuidedTour::class)->forRoute("filament.administrator.{$routeName}");

    expect($tour)
        ->id->toBe($routeName.'.v'.GuidedTour::VERSION)
        ->title->toBe($title)
        ->and($tour['steps'])->not->toBeEmpty()
        ->each->toHaveKeys(['target', 'match', 'title', 'body']);
})->with([
    ['pages.dashboard', 'Welcome tour'],
    ['pages.patient-forms', 'Patient forms tour'],
    ['pages.reports', 'Reports tour'],
    ['resources.form-demographics.create', 'Patient record tour'],
    ['resources.form-demographics.edit', 'Patient record tour'],
    ['resources.users.index', 'List tour'],
    ['resources.cancer-diagnoses.create', 'Form tour'],
    ['resources.activity-logs.view', 'Page tour'],
]);

test('the patient record tour spotlights the address fields sourced from the PSA', function (): void {
    $steps = collect(app(GuidedTour::class)->forRoute('filament.administrator.resources.form-demographics.create')['steps']);

    expect($steps->pluck('target')->all())->toContain(
        '[data-tour="psgc-birthplace"]',
        '[data-tour="psgc-permanent-address"]',
        '[data-tour="psgc-current-address"]',
    );

    expect($steps->firstWhere('target', '[data-tour="psgc-permanent-address"]')['body'])->toContain('PSA');
});

test('the patient record form marks every PSGC address dropdown as coming from the PSA', function (): void {
    $this->actingAs(guidedTourUser(['ViewAny:FormDemographics', 'Create:FormDemographics']));

    $html = Livewire::test(CreateFormDemographics::class)->html();

    expect($html)->not->toContain('Source: PSA')
        ->and($html)->not->toContain('These options come from the Philippine Standard Geographic Code')
        ->and(substr_count($html, 'data-source="psa-psgc"'))->toBe(10)
        ->and(substr_count($html, 'data-tour="psgc-birthplace"'))->toBe(2)
        ->and(substr_count($html, 'data-tour="psgc-permanent-address"'))->toBe(4)
        ->and(substr_count($html, 'data-tour="psgc-current-address"'))->toBe(4)
        ->and($html)->toContain(
            'data-tour="health-facility-id"',
            'data-tour="same-as-permanent"',
            'data-tour="not-applicable"',
            'Philippine Statistics Authority (PSA)',
        );
});

test('panel pages render the tour button and the tour for the current page', function (): void {
    // User does not implement FilamentUser, so Filament only opens the panel in the local environment.
    config(['app.env' => 'local']);

    $this->actingAs(guidedTourUser(['View:PatientForms']));

    $this->get(route('filament.administrator.pages.patient-forms'))
        ->assertSuccessful()
        ->assertSee('data-tour="tour-button"', escape: false)
        ->assertSee('id="hbcr-tour-data"', escape: false)
        ->assertSee('pages.patient-forms.v'.GuidedTour::VERSION, escape: false)
        ->assertSee('data-tour="patient-forms-search"', escape: false)
        ->assertSee('window.HbcrTour', escape: false);
});
