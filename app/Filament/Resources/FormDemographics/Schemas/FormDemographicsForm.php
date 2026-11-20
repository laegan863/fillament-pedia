<?php

namespace App\Filament\Resources\FormDemographics\Schemas;

use App\Models\FormDemographics;
use App\Services\GuidedTour;
use App\Services\PsgcAddressLookup;
use Carbon\Carbon;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class FormDemographicsForm
{
    public $conn;

    public function __construct()
    {
        return $this->conn = DB::connection('sqlsrv');
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                View::make('filament.forms.components.loading-overlay')
                    ->viewData([
                        'target' => [
                            'data.philhealth_pin_na',
                            'data.same_as_permanent_address',
                            'data.mobile_contact_na',
                            'data.email_na',
                        ],
                    ])->liberatedFromContainerGrid(),
                Section::make('Encounter and Patient')
                    ->columnSpanFull()
                    ->columns([
                        'md' => 2,
                        'xl' => 12,
                    ])
                    ->schema([
                        DatePicker::make('first_encounter_date')
                            ->label("Date of Patient's First Encounter")
                            ->placeholder('Select date of first encounter')
                            ->columnSpan([
                                'md' => 1,
                                'xl' => 8,
                            ])
                            ->native(false)
                            ->displayFormat('Y-m-d')
                            ->required(),
                        TextInput::make('health_facility_id_no')
                            ->label("Patient's Health Facility ID No.")
                            ->placeholder('Enter Health Facility ID No.')
                            ->columnSpan([
                                'md' => 1,
                                'xl' => 4,
                            ])
                            ->maxLength(50)
                            ->required()
                            ->extraFieldWrapperAttributes(['data-tour' => 'health-facility-id'])
                            ->suffixAction(
                                Action::make('validateHealthFacilityId')
                                    ->label('Validate')
                                    ->button()
                                    ->color('secondary')
                                    ->action(function (Get $get, Set $set): void {
                                        $healthFacilityId = trim((string) $get('health_facility_id_no'));

                                        if (blank($healthFacilityId)) {
                                            Notification::make()
                                                ->title('Please enter Health Facility ID No.')
                                                ->warning()
                                                ->send();

                                            throw ValidationException::withMessages([
                                                'data.health_facility_id_no' => 'Please enter Health Facility ID No.',
                                            ]);
                                        }

                                        if (FormDemographics::where('health_facility_id_no', $healthFacilityId)->exists()) {
                                            Notification::make()
                                                ->title('Health Facility ID No. already exists')
                                                ->body('The Health Facility ID No. you entered is already associated with another patient record.')
                                                ->danger()
                                                ->send();

                                            throw ValidationException::withMessages([
                                                'data.health_facility_id_no' => 'The Health Facility ID No. you entered is already associated with another patient record.',
                                            ]);
                                        }

                                        try {
                                            $paddedHealthFacilityId = str_pad($healthFacilityId, 10, '0', STR_PAD_LEFT);

                                            $patient = DB::connection('sqlsrv')
                                                ->table('Patient')
                                                ->select([
                                                    'PatientID',
                                                    'HospitalID',
                                                    'LastName',
                                                    'FirstName',
                                                    'MiddleName',
                                                    'Suffix',
                                                    'Gender',
                                                    'Birthdate',
                                                    'Birthplace',
                                                    'Nationality',
                                                    'ContactNo',
                                                    'Email',
                                                    'Address',
                                                    'Barangay',
                                                    'TownCity',
                                                    'Province',
                                                    'Region',
                                                    'ZipCode',
                                                    'CivilStatus',
                                                    'BloodType',
                                                    'isActive',
                                                    'isDead',
                                                ])
                                                ->where('HospitalID', $paddedHealthFacilityId)
                                                ->first();

                                            // dump($patient);

                                            if (! $patient) {
                                                Notification::make()
                                                    ->title('Patient not found')
                                                    ->body('No patient record found for this Health Facility ID No.')
                                                    ->danger()
                                                    ->send();

                                                return;
                                            }

                                            $gender = match (strtoupper(trim((string) ($patient->Gender ?? '')))) {
                                                'MALE', 'M' => 'male',
                                                'FEMALE', 'F' => 'female',
                                                'OTHERS', 'O' => 'others',
                                                'UNKNOWN', 'U' => 'unknown',
                                                default => null,
                                            };

                                            $set('patient_id', $patient->PatientID ?? null);
                                            $set('hospital_id', $patient->HospitalID ?? null);

                                            $set('patient_first_name', $patient->FirstName ?? null);
                                            $set('patient_middle_name', $patient->MiddleName ?? null);
                                            $set('patient_surname', $patient->LastName ?? null);
                                            $set('patient_suffix', $patient->Suffix ?? null);

                                            $set('sex_at_birth', $gender);

                                            $set(
                                                'date_of_birth',
                                                filled($patient->Birthdate ?? null)
                                                    ? Carbon::parse($patient->Birthdate)->format('Y-m-d')
                                                    : null
                                            );

                                            $birthplace = app(PsgcAddressLookup::class)->matchBirthplace($patient->Birthplace ?? null);

                                            $set('birth_province', $birthplace['province']);
                                            $set('birth_city_municipality', $birthplace['city_municipality']);
                                            $set('nationality', self::countryFromNationality($patient->Nationality ?? null));

                                            $set('mobile_contact_no', $patient->ContactNo ?? null);
                                            $set('email_address', $patient->Email ?? null);

                                            $permanentAddress = app(PsgcAddressLookup::class)->matchAddress(
                                                region: $patient->Region ?? null,
                                                province: $patient->Province ?? null,
                                                cityMunicipality: $patient->TownCity ?? null,
                                                barangay: $patient->Barangay ?? null,
                                            );

                                            $set('permanent_region', $permanentAddress['region']);
                                            $set('permanent_province', $permanentAddress['province']);
                                            $set('permanent_city_municipality', $permanentAddress['city_municipality']);
                                            $set('permanent_barangay', $permanentAddress['barangay']);

                                            $set('civil_status', $patient->CivilStatus ?? null);
                                            $set('blood_type', $patient->BloodType ?? null);

                                            Notification::make()
                                                ->title('Patient validated')
                                                ->body('Patient data has been loaded successfully.')
                                                ->success()
                                                ->send();
                                        } catch (Throwable $e) {
                                            report($e);

                                            Notification::make()
                                                ->title('Validation failed')
                                                ->body('Something went wrong while checking the patient record.')
                                                ->danger()
                                                ->send();
                                        }
                                    })
                            ),

                        TextInput::make('patient_first_name')
                            ->label('First Name')
                            ->columnSpan([
                                'md' => 1,
                                'xl' => 3,
                            ])
                            ->placeholder('Enter patient\'s first name')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('patient_middle_name')
                            ->label('Middle Name')
                            ->columnSpan([
                                'md' => 1,
                                'xl' => 3,
                            ])
                            ->placeholder('Enter patient\'s middle name')
                            ->maxLength(100),

                        TextInput::make('patient_surname')
                            ->label('Surname')
                            ->columnSpan([
                                'md' => 1,
                                'xl' => 3,
                            ])
                            ->placeholder('Enter patient\'s surname')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('patient_suffix')
                            ->label('Suffix')
                            ->columnSpan([
                                'md' => 1,
                                'xl' => 3,
                            ])
                            ->placeholder('Enter patient\'s suffix')
                            ->maxLength(20),

                        Radio::make('sex_at_birth')
                            ->label('Sex at Birth')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                                'others' => 'Others',
                                'unknown' => 'Unknown',
                            ])
                            ->inline()
                            ->inlineLabel(false)
                            ->required()
                            ->columnSpan([
                                'md' => 1,
                                'xl' => 8,
                            ]),

                        DatePicker::make('date_of_birth')
                            ->label('Date of Birth')
                            ->native(false)
                            ->displayFormat('Y-m-d')
                            ->columnSpan([
                                'md' => 1,
                                'xl' => 4,
                            ])
                            ->placeholder('Select date of birth')
                            ->required(),
                    ]),

                Section::make('Birth and Guardian Details')
                    ->columnSpanFull()
                    ->columns([
                        'md' => 2,
                        'xl' => 12,
                    ])
                    ->schema([
                        self::fromPsa(Select::make('birth_province'), tourAnchor: 'psgc-birthplace')
                            ->label('Place of Birth - Province')
                            ->placeholder('Select province of birth')
                            ->searchable()
                            ->options(fn (?string $state): array => app(PsgcAddressLookup::class)->withCurrentValue(
                                app(PsgcAddressLookup::class)->birthProvinceOptions(),
                                $state,
                            ))
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('birth_city_municipality', null))
                            ->columnSpan([
                                'md' => 1,
                                'xl' => 6,
                            ])
                            ->required(),

                        self::fromPsa(Select::make('birth_city_municipality'), tourAnchor: 'psgc-birthplace')
                            ->label('Place of Birth - City / Municipality')
                            ->placeholder('Select city or municipality of birth')
                            ->searchable()
                            ->options(fn (Get $get, ?string $state): array => app(PsgcAddressLookup::class)->withCurrentValue(
                                app(PsgcAddressLookup::class)->birthCityMunicipalityOptions($get('birth_province')),
                                $state,
                            ))
                            ->columnSpan([
                                'md' => 1,
                                'xl' => 6,
                            ])
                            ->required(),

                        Select::make('nationality')
                            ->label('Nationality')
                            ->default('PHILIPPINES')
                            ->placeholder('Select country')
                            ->options(fn (?string $state): array => self::nationalityOptions($state))
                            ->searchable()
                            ->columnSpanFull()
                            ->required(),

                        TextInput::make('guardian_first_name')
                            ->label('Guardian First Name')
                            ->required()
                            ->columnSpan([
                                'md' => 1,
                                'xl' => 3,
                            ])
                            ->placeholder('Enter guardian\'s first name')
                            ->maxLength(100),

                        TextInput::make('guardian_middle_name')
                            ->label('Guardian Middle Name')
                            ->columnSpan([
                                'md' => 1,
                                'xl' => 3,
                            ])
                            ->placeholder('Enter guardian\'s middle name')
                            ->maxLength(100),

                        TextInput::make('guardian_surname')
                            ->label('Guardian Surname')
                            ->required()
                            ->columnSpan([
                                'md' => 1,
                                'xl' => 3,
                            ])
                            ->placeholder('Enter guardian\'s surname')
                            ->maxLength(100),

                        TextInput::make('guardian_suffix')
                            ->label('Guardian Suffix')
                            ->columnSpan([
                                'md' => 1,
                                'xl' => 3,
                            ])
                            ->placeholder('Enter guardian\'s suffix')
                            ->maxLength(20),
                    ]),

                Section::make('PhilHealth and Address')
                    ->description('Region, Province, City / Municipality and Barangay options come from the Philippine Standard Geographic Code (PSGC) of the Philippine Statistics Authority (PSA).')
                    ->columnSpanFull()
                    ->columns([
                        'md' => 2,
                        'xl' => 12,
                    ])
                    ->schema([

                        Group::make(self::notApplicableField(
                            notApplicableField: 'philhealth_pin_na',
                            notApplicableLabel: 'No PhilHealth PIN (N/A)',
                            input: TextInput::make('philhealth_pin')
                                ->label('PhilHealth PIN')
                                ->placeholder('Enter PhilHealth PIN')
                                ->maxLength(20),
                        ))->columnSpanFull(),

                        ...self::addressSelects(
                            prefix: 'permanent',
                            labelPrefix: 'Permanent Address',
                        ),

                        Checkbox::make('same_as_permanent_address')
                            ->label('Current Address is same as Permanent Address')
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, ?bool $state) {
                                if ($state) {
                                    $set('current_region', $get('permanent_region'));
                                    $set('current_province', $get('permanent_province'));
                                    $set('current_city_municipality', $get('permanent_city_municipality'));
                                    $set('current_barangay', $get('permanent_barangay'));
                                } else {
                                    $set('current_region', null);
                                    $set('current_province', null);
                                    $set('current_city_municipality', null);
                                    $set('current_barangay', null);
                                }
                            })
                            ->extraFieldWrapperAttributes(['data-tour' => 'same-as-permanent'])
                            ->columnSpanFull(),

                        ...self::addressSelects(
                            prefix: 'current',
                            labelPrefix: 'Current Address',
                            isDisabled: fn (Get $get): bool => (bool) $get('same_as_permanent_address'),
                        ),
                    ]),

                Section::make('Contact Details')
                    ->columnSpanFull()
                    ->columns([
                        'md' => 2,
                    ])
                    ->schema([
                        Group::make(self::notApplicableField(
                            notApplicableField: 'mobile_contact_na',
                            notApplicableLabel: 'No Mobile Contact No. (N/A)',
                            input: TextInput::make('mobile_contact_no')
                                ->label('Mobile Contact No.')
                                ->tel(fn (Get $get): bool => ! $get('mobile_contact_na'))
                                ->placeholder('Enter mobile contact number')
                                ->maxLength(20),
                        ))->columns(1),

                        Group::make(self::notApplicableField(
                            notApplicableField: 'email_na',
                            notApplicableLabel: 'No Email Address (N/A)',
                            input: TextInput::make('email_address')
                                ->label('Email Address')
                                ->email(fn (Get $get): bool => ! $get('email_na'))
                                ->placeholder('Enter email address')
                                ->maxLength(255),
                        ))->columns(1),
                    ]),

                Section::make('Relationship')
                    ->columnSpanFull()
                    ->columns([
                        'md' => 2,
                        'xl' => 12,
                    ])
                    ->schema([
                        Radio::make('relationship_to_patient')
                            ->label('Relationship to Patient')
                            ->options([
                                'mother' => 'Mother',
                                'father' => 'Father',
                                'other' => 'Other',
                            ])
                            ->inline()
                            ->inlineLabel(false)
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('relationship_other_specify')
                            ->label('Other, Specify')
                            ->maxLength(100)
                            ->visibleJs(<<<'JS'
                                $get('relationship_to_patient') === 'other'
                            JS)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    /**
     * Country options labelled with their nationality (e.g. "Philippines (Filipino)"), keyed by the
     * upper-cased country that is stored. A saved value not in the list is kept as its own option.
     *
     * @return array<string, string>
     */
    private static function nationalityOptions(?string $currentValue): array
    {
        $options = collect(config('nationalities'))
            ->mapWithKeys(fn (string $country, string $nationality): array => [
                strtoupper($country) => $country.' ('.str($nationality)->before(' (')->lower()->title().')',
            ])
            ->sort()
            ->all();

        if (filled($currentValue) && ! array_key_exists($currentValue, $options)) {
            $options[$currentValue] = $currentValue;
        }

        return $options;
    }

    /**
     * Converts a nationality from the hospital system (e.g. "Filipino") to the upper-cased country that
     * is stored (e.g. "PHILIPPINES"). Values that are not a known nationality are kept, upper-cased.
     */
    public static function countryFromNationality(?string $nationality): ?string
    {
        if (blank($nationality)) {
            return null;
        }

        $nationality = strtoupper(trim($nationality));
        $country = config("nationalities.{$nationality}");

        return $country === null ? $nationality : strtoupper($country);
    }

    /**
     * Pairs an "N/A" checkbox with the input it applies to. Ticking N/A clears and hides the input
     * in the browser, and the input is never validated or saved while N/A is ticked.
     *
     * @return array{0: Checkbox, 1: TextInput}
     */
    private static function notApplicableField(string $notApplicableField, string $notApplicableLabel, TextInput $input): array
    {
        $inputField = $input->getName();
        $isNotApplicable = fn (Get $get): bool => (bool) $get($notApplicableField);

        return [
            Checkbox::make($notApplicableField)
                ->label($notApplicableLabel)
                ->extraFieldWrapperAttributes(['data-tour' => 'not-applicable'])
                ->afterStateUpdatedJs(<<<JS
                    if (\$state) { \$set('{$inputField}', null) }
                JS),

            $input
                ->hiddenJs(<<<JS
                    \$get('{$notApplicableField}')
                JS)
                ->dehydrateStateUsing(fn (?string $state, Get $get): ?string => $isNotApplicable($get) ? null : $state),
        ];
    }

    /**
     * Marks a dropdown as sourced from the PSA's PSGC with a visible hint badge, and tags its wrapper
     * so the guided tour can spotlight it.
     */
    private static function fromPsa(Select $select, string $tourAnchor): Select
    {
        return $select
            ->hint(GuidedTour::PSA_SOURCE_LABEL)
            ->hintIcon(Heroicon::OutlinedBuildingLibrary, tooltip: GuidedTour::PSA_SOURCE_TOOLTIP)
            ->hintColor('info')
            ->extraFieldWrapperAttributes([
                'data-tour' => $tourAnchor,
                'data-source' => 'psa-psgc',
            ], merge: true);
    }

    /**
     * Builds the dependent Region → Province → City/Municipality → Barangay dropdowns sourced from the PSGC.
     * Values are stored as names; a value not found in the PSGC is kept as its own option.
     *
     * @return list<Select>
     */
    private static function addressSelects(string $prefix, string $labelPrefix, ?Closure $isDisabled = null): array
    {
        $regionField = "{$prefix}_region";
        $provinceField = "{$prefix}_province";
        $cityMunicipalityField = "{$prefix}_city_municipality";
        $barangayField = "{$prefix}_barangay";

        $lookup = fn (): PsgcAddressLookup => app(PsgcAddressLookup::class);

        $configure = fn (Select $select): Select => self::fromPsa($select, tourAnchor: "psgc-{$prefix}-address")
            ->columnSpan([
                'md' => 1,
                'xl' => 3,
            ])
            ->searchable()
            ->disabled($isDisabled ?? false)
            ->dehydrated();

        return [
            $configure(Select::make($regionField))
                ->label("{$labelPrefix} - Region")
                ->placeholder('Select region')
                ->options(fn (?string $state): array => $lookup()->withCurrentValue($lookup()->regionOptions(), $state))
                ->live()
                ->afterStateUpdated(function (Set $set) use ($provinceField, $cityMunicipalityField, $barangayField): void {
                    $set($provinceField, null);
                    $set($cityMunicipalityField, null);
                    $set($barangayField, null);
                }),

            $configure(Select::make($provinceField))
                ->label("{$labelPrefix} - Province")
                ->placeholder('Select province')
                ->options(fn (Get $get, ?string $state): array => $lookup()->withCurrentValue(
                    $lookup()->provinceOptions($get($regionField)),
                    $state,
                ))
                ->live()
                ->afterStateUpdated(function (Set $set) use ($cityMunicipalityField, $barangayField): void {
                    $set($cityMunicipalityField, null);
                    $set($barangayField, null);
                }),

            $configure(Select::make($cityMunicipalityField))
                ->label("{$labelPrefix} - City / Municipality")
                ->placeholder('Select city or municipality')
                ->options(fn (Get $get, ?string $state): array => $lookup()->withCurrentValue(
                    $lookup()->cityMunicipalityOptions($get($regionField), $get($provinceField)),
                    $state,
                ))
                ->live()
                ->afterStateUpdated(function (Set $set) use ($barangayField): void {
                    $set($barangayField, null);
                }),

            $configure(Select::make($barangayField))
                ->label("{$labelPrefix} - Barangay")
                ->placeholder('Select barangay')
                ->options(fn (Get $get, ?string $state): array => $lookup()->withCurrentValue(
                    $lookup()->barangayOptions($get($regionField), $get($provinceField), $get($cityMunicipalityField)),
                    $state,
                )),
        ];
    }
}
