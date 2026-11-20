<?php

use App\Filament\Pages\Reports;
use App\Filament\Resources\Signatories\Pages\CreateSignatory;
use App\Filament\Resources\Signatories\Pages\ListSignatories;
use App\Models\Signatory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

function signatoryReportData(array $overrides = []): array
{
    return [
        'facility_name' => 'Corazon Locsin Montelibano Memorial Regional Hospital',
        'facility_registration_no' => '4299',
        'from_date' => '2026-01-01',
        'to_date' => '2026-03-31',
        ...$overrides,
    ];
}

function invokeReportBuilder(string $method, array $data): array
{
    $reflection = new ReflectionMethod(Reports::class, $method);

    return $reflection->invoke(app(Reports::class), $data);
}

test('every report includes the selected prepared by and noted by signatories', function (string $builder): void {
    $doctor = Signatory::factory()->create([
        'name' => 'Dr. Ana Santos',
        'position' => 'Cancer Center Head',
    ]);

    $report = invokeReportBuilder($builder, signatoryReportData([
        'prepared_by_name' => ' Maria Reyes ',
        'prepared_by_position' => 'Cancer Registrar',
        'noted_by_signatory_id' => $doctor->id,
    ]));

    expect($report)
        ->prepared_by_name->toBe('Maria Reyes')
        ->prepared_by_position->toBe('Cancer Registrar')
        ->noted_by_name->toBe('Dr. Ana Santos')
        ->noted_by_position->toBe('Cancer Center Head');
})->with([
    'buildForm1AReport',
    'buildForm1BReport',
    'buildForm1CReport',
    'buildForm2AReport',
    'buildForm2BReport',
    'buildForm2CReport',
]);

test('noted by falls back to the default position when no signatory is selected', function (): void {
    $report = invokeReportBuilder('buildForm1BReport', signatoryReportData());

    expect($report)
        ->noted_by_name->toBe('')
        ->noted_by_position->toBe('CANCER COORDINATOR/ CANCER CENTER HEAD');
});

test('the signature block renders name above the line and position below it', function (string $partial): void {
    $html = view("filament.pages.partials.{$partial}", [
        'report' => [
            'prepared_by_name' => 'Maria Reyes',
            'prepared_by_position' => 'Cancer Registrar',
            'noted_by_name' => 'Dr. Ana Santos',
            'noted_by_position' => 'Cancer Center Head',
        ],
        'forPdf' => true,
    ])->render();

    expect($html)->toContain('Prepared by:', 'Noted by:');
    expect(strpos($html, 'Dr. Ana Santos'))->toBeLessThan(strpos($html, 'Cancer Center Head'));
    expect(strpos($html, 'Maria Reyes'))->toBeLessThan(strpos($html, 'Cancer Registrar'));
})->with([
    'form-1a-report',
    'form-1b-report',
    'form-1c-report',
    'form-2a-report',
    'form-2b-report',
    'form-2c-report',
]);

test('only active signatories are offered in the noted by scope', function (): void {
    $active = Signatory::factory()->create();
    Signatory::factory()->inactive()->create();

    expect(Signatory::query()->active()->pluck('id')->all())->toBe([$active->id]);
});

test('authorized users can list and create noted by signatories', function (): void {
    $user = User::factory()->create();

    foreach (['ViewAny:Signatory', 'Create:Signatory'] as $permission) {
        $user->givePermissionTo(Permission::findOrCreate($permission, 'web'));
    }

    $this->actingAs($user);

    $existing = Signatory::factory()->create();

    Livewire::test(ListSignatories::class)
        ->assertCanSeeTableRecords([$existing]);

    Livewire::test(CreateSignatory::class)
        ->fillForm([
            'name' => 'Dr. Jose Rizal',
            'position' => 'Medical Center Chief',
            'is_active' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Signatory::query()->where('name', 'Dr. Jose Rizal')->where('position', 'Medical Center Chief')->exists())->toBeTrue();
});
