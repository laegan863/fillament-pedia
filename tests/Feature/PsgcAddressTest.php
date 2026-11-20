<?php

use App\Filament\Resources\FormDemographics\Pages\CreateFormDemographics;
use App\Models\Barangay;
use App\Models\CityMunicipality;
use App\Models\FormDemographics;
use App\Models\Province;
use App\Models\Region;
use App\Models\User;
use App\Services\PsgcAddressLookup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

test('psgc import stores regions, provinces, cities and barangays', function (): void {
    Http::fake([
        '*/regions/' => Http::response([
            ['code' => '060000000', 'name' => 'Western Visayas', 'regionName' => 'Region VI'],
            ['code' => '130000000', 'name' => 'National Capital Region', 'regionName' => 'NCR'],
        ]),
        '*/provinces/' => Http::response([
            ['code' => '064500000', 'name' => 'Negros Occidental', 'regionCode' => '060000000'],
        ]),
        '*/cities-municipalities/' => Http::response([
            ['code' => '064507000', 'name' => 'Cauayan', 'isCity' => false, 'provinceCode' => '064500000', 'regionCode' => '060000000'],
            ['code' => '133900000', 'name' => 'City of Manila', 'isCity' => true, 'provinceCode' => false, 'regionCode' => '130000000'],
        ]),
        '*/barangays/' => Http::response([
            ['code' => '064507001', 'name' => 'Poblacion', 'cityCode' => false, 'municipalityCode' => '064507000'],
            ['code' => '133901001', 'name' => 'Barangay 1', 'cityCode' => '133900000', 'municipalityCode' => false],
        ]),
    ]);

    Region::factory()->create(['code' => '999999999', 'name' => 'Stale Region']);

    $this->artisan('psgc:import')->assertSuccessful();

    expect(Region::query()->orderBy('code')->pluck('name')->all())
        ->toBe(['Region VI (Western Visayas)', 'NCR (National Capital Region)'])
        ->and(Province::query()->sole()->region_code)->toBe('060000000')
        ->and(CityMunicipality::query()->find('133900000'))
        ->province_code->toBeNull()
        ->is_city->toBeTrue()
        ->and(Barangay::query()->find('064507001')->city_municipality_code)->toBe('064507000')
        ->and(Barangay::query()->find('133901001')->city_municipality_code)->toBe('133900000');
});

test('psgc import fails without touching existing data when the source is unavailable', function (): void {
    Http::fake(['*' => Http::response(null, 500)]);

    Region::factory()->create(['name' => 'Existing Region']);

    $this->artisan('psgc:import')->assertFailed();

    expect(Region::query()->pluck('name')->all())->toBe(['Existing Region']);
});

test('psgc import fails cleanly when the source cannot be reached', function (): void {
    Http::fake(fn () => throw new ConnectionException('cURL error 60'));

    $this->artisan('psgc:import')
        ->expectsOutputToContain('Unable to download')
        ->assertFailed();
});

test('psgc import reads json files from a local folder', function (): void {
    Http::preventStrayRequests();

    $folder = storage_path('framework/testing/psgc');
    File::ensureDirectoryExists($folder);
    File::put("{$folder}/regions.json", json_encode([['code' => '060000000', 'name' => 'Western Visayas', 'regionName' => 'Region VI']]));
    File::put("{$folder}/provinces.json", json_encode([['code' => '064500000', 'name' => 'Negros Occidental', 'regionCode' => '060000000']]));
    File::put("{$folder}/cities-municipalities.json", json_encode([['code' => '064507000', 'name' => 'Cauayan', 'isCity' => false, 'provinceCode' => '064500000', 'regionCode' => '060000000']]));
    File::put("{$folder}/barangays.json", json_encode([['code' => '064507001', 'name' => 'Poblacion', 'cityCode' => false, 'municipalityCode' => '064507000']]));

    $this->artisan('psgc:import', ['--path' => $folder])->assertSuccessful();

    File::deleteDirectory($folder);

    expect(Barangay::query()->sole()->name)->toBe('Poblacion');
});

test('address options cascade from region down to barangay', function (): void {
    seedPsgcAddresses();

    $lookup = app(PsgcAddressLookup::class);

    expect($lookup->provinceOptions('Region VI (Western Visayas)'))->toBe(['Negros Occidental' => 'Negros Occidental'])
        ->and($lookup->cityMunicipalityOptions('Region VI (Western Visayas)', 'Negros Occidental'))
        ->toBe(['Cauayan' => 'Cauayan', 'City of Bacolod' => 'City of Bacolod'])
        ->and($lookup->barangayOptions('Region VI (Western Visayas)', 'Negros Occidental', 'Cauayan'))
        ->toBe(['Poblacion' => 'Poblacion'])
        ->and($lookup->provinceOptions('NCR (National Capital Region)'))->toBe([])
        ->and($lookup->cityMunicipalityOptions('NCR (National Capital Region)', null))
        ->toBe(['City of Manila' => 'City of Manila'])
        ->and($lookup->provinceOptions(null))->toBe([]);
});

test('hospital record addresses are matched to psgc names and unmatched parts are kept', function (): void {
    seedPsgcAddresses();

    $lookup = app(PsgcAddressLookup::class);

    expect($lookup->matchAddress('REGION VI (WESTERN VISAYAS)', 'NEGROS OCCIDENTAL', 'CAUAYAN', 'POBLACION'))
        ->toBe([
            'region' => 'Region VI (Western Visayas)',
            'province' => 'Negros Occidental',
            'city_municipality' => 'Cauayan',
            'barangay' => 'Poblacion',
        ])
        ->and($lookup->matchAddress('REGION VI (WESTERN VISAYAS)', 'NEGROS OCCIDENTAL', 'BACOLOD CITY', 'UNKNOWN BRGY'))
        ->toBe([
            'region' => 'Region VI (Western Visayas)',
            'province' => 'Negros Occidental',
            'city_municipality' => 'City of Bacolod',
            'barangay' => 'UNKNOWN BRGY',
        ])
        ->and($lookup->matchAddress(null, null, null, null))
        ->toBe(['region' => null, 'province' => null, 'city_municipality' => null, 'barangay' => null]);
});

test('birthplace options list provinces with metro manila and the cities of the chosen province', function (): void {
    seedPsgcAddresses();
    CityMunicipality::factory()->create(['code' => '097332000', 'region_code' => '060000000', 'province_code' => null, 'name' => 'Independent City']);

    $lookup = app(PsgcAddressLookup::class);

    expect($lookup->birthProvinceOptions())->toBe([
        'Metro Manila' => 'Metro Manila',
        'Negros Occidental' => 'Negros Occidental',
    ])
        ->and($lookup->birthCityMunicipalityOptions('Negros Occidental'))->toBe([
            'Cauayan' => 'Cauayan',
            'City of Bacolod' => 'City of Bacolod',
            'Independent City' => 'Independent City',
        ])
        ->and($lookup->birthCityMunicipalityOptions('Metro Manila'))->toBe(['City of Manila' => 'City of Manila'])
        ->and($lookup->birthCityMunicipalityOptions(null))->toBe([]);
});

test('hospital birthplace is matched to its city and province', function (): void {
    seedPsgcAddresses();
    CityMunicipality::factory()->create(['code' => '103501000', 'region_code' => '060000000', 'province_code' => null, 'name' => 'Cauayan']);

    $lookup = app(PsgcAddressLookup::class);

    expect($lookup->matchBirthplace('BACOLOD CITY'))->toBe(['province' => 'Negros Occidental', 'city_municipality' => 'City of Bacolod'])
        ->and($lookup->matchBirthplace('MANILA CITY'))->toBe(['province' => 'Metro Manila', 'city_municipality' => 'City of Manila'])
        ->and($lookup->matchBirthplace('CAUAYAN'))->toBe(['province' => null, 'city_municipality' => 'CAUAYAN'])
        ->and($lookup->matchBirthplace('SOMEWHERE ABROAD'))->toBe(['province' => null, 'city_municipality' => 'SOMEWHERE ABROAD'])
        ->and($lookup->matchBirthplace(null))->toBe(['province' => null, 'city_municipality' => null]);
});

test('changing the birth province clears the birth city on the create form', function (): void {
    seedPsgcAddresses();

    $this->actingAs(userWhoCanCreateFormDemographics());

    Livewire::test(CreateFormDemographics::class)
        ->fillForm([
            'birth_province' => 'Negros Occidental',
            'birth_city_municipality' => 'Cauayan',
        ])
        ->fillForm(['birth_province' => 'Metro Manila'])
        ->assertSchemaStateSet(['birth_city_municipality' => null]);
});

test('changing the region clears the lower address fields on the create form', function (): void {
    seedPsgcAddresses();

    $this->actingAs(userWhoCanCreateFormDemographics());

    Livewire::test(CreateFormDemographics::class)
        ->fillForm([
            'permanent_region' => 'Region VI (Western Visayas)',
            'permanent_province' => 'Negros Occidental',
            'permanent_city_municipality' => 'Cauayan',
            'permanent_barangay' => 'Poblacion',
        ])
        ->fillForm(['permanent_region' => 'NCR (National Capital Region)'])
        ->assertSchemaStateSet([
            'permanent_province' => null,
            'permanent_city_municipality' => null,
            'permanent_barangay' => null,
        ]);
});

test('create form saves psgc addresses, legacy free-text values and the copied current address', function (): void {
    seedPsgcAddresses();

    $this->actingAs(userWhoCanCreateFormDemographics());

    Livewire::test(CreateFormDemographics::class)
        ->fillForm([
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
            'permanent_region' => 'Region VI (Western Visayas)',
            'permanent_province' => 'Negros Occidental',
            'permanent_city_municipality' => 'Cauayan',
            'permanent_barangay' => 'BRGY NOT IN PSGC',
        ])
        ->set('data.same_as_permanent_address', true)
        ->call('create')
        ->assertHasNoFormErrors();

    expect(FormDemographics::query()->sole())
        ->permanent_city_municipality->toBe('Cauayan')
        ->permanent_barangay->toBe('BRGY NOT IN PSGC')
        ->current_region->toBe('Region VI (Western Visayas)')
        ->current_barangay->toBe('BRGY NOT IN PSGC');
});

function seedPsgcAddresses(): void
{
    Region::factory()->create(['code' => '060000000', 'name' => 'Region VI (Western Visayas)']);
    Region::factory()->create(['code' => '130000000', 'name' => 'NCR (National Capital Region)']);

    Province::factory()->create(['code' => '064500000', 'region_code' => '060000000', 'name' => 'Negros Occidental']);

    CityMunicipality::factory()->create(['code' => '064507000', 'region_code' => '060000000', 'province_code' => '064500000', 'name' => 'Cauayan']);
    CityMunicipality::factory()->create(['code' => '064501000', 'region_code' => '060000000', 'province_code' => '064500000', 'name' => 'City of Bacolod', 'is_city' => true]);
    CityMunicipality::factory()->create(['code' => '133900000', 'region_code' => '130000000', 'province_code' => null, 'name' => 'City of Manila', 'is_city' => true]);

    Barangay::factory()->create(['code' => '064507001', 'city_municipality_code' => '064507000', 'name' => 'Poblacion']);
}

function userWhoCanCreateFormDemographics(): User
{
    $user = User::factory()->create();

    $user->givePermissionTo(
        Permission::findOrCreate('ViewAny:FormDemographics', 'web'),
        Permission::findOrCreate('Create:FormDemographics', 'web'),
    );

    return $user;
}
