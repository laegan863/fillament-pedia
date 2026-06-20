<?php

namespace App\Filament\Resources\FormDemographics\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;
use Carbon\Carbon;

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

                                            $set('birth_city_municipality', $patient->Birthplace ?? null);
                                            $set('nationality', $patient->Nationality ?? null);

                                            $set('mobile_contact_no', $patient->ContactNo ?? null);
                                            $set('email_address', $patient->Email ?? null);

                                            $set('permanent_barangay', $patient->Barangay ?? null);
                                            $set('permanent_city_municipality', $patient->TownCity ?? null);
                                            $set('permanent_province', $patient->Province ?? null);
                                            $set('permanent_region', $patient->Region ?? null);

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
                        TextInput::make('birth_province')
                            ->label('Place of Birth - Province')
                            ->maxLength(100)
                            ->placeholder('Enter province of birth')
                            ->columnSpan([
                                'md' => 1,
                                'xl' => 6,
                            ])
                            ->required(),

                        TextInput::make('birth_city_municipality')
                            ->label('Place of Birth - City / Municipality')
                            ->maxLength(100)
                            ->placeholder('Enter city or municipality of birth')
                            ->columnSpan([
                                'md' => 1,
                                'xl' => 6,
                            ])
                            ->required(),

                        TextInput::make('nationality')
                            ->label('Nationality')
                            ->default('PHILIPPINES')
                            ->placeholder('Enter nationality')
                            ->columnSpanFull()
                            ->required()
                            ->maxLength(100),

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
                    ->columnSpanFull()
                    ->columns([
                        'md' => 2,
                        'xl' => 12,
                    ])
                    ->schema([
                        Checkbox::make('philhealth_pin_na')
                            ->label('PhilHealth PIN N/A')
                            ->live()
                            ->columnSpan([
                                'md' => 1,
                                'xl' => 3,
                            ]),

                        TextInput::make('philhealth_pin')
                            ->label('PhilHealth Identification No. (PIN)')
                            ->columnSpan([
                                'md' => 1,
                                'xl' => 9,
                            ])
                            ->maxLength(12)
                            ->placeholder('Enter PhilHealth Identification No.')
                            ->disabled(fn (Get $get): bool => (bool) $get('philhealth_pin_na')),

                        TextInput::make('permanent_region')
                            ->label('Permanent Address - Region')
                            ->columnSpan([
                                'md' => 1,
                                'xl' => 3,
                            ])
                            ->placeholder('Enter region of permanent address')
                            ->maxLength(100),

                        TextInput::make('permanent_province')
                            ->label('Permanent Address - Province')
                            ->columnSpan([
                                'md' => 1,
                                'xl' => 3,
                            ])
                            ->placeholder('Enter province of permanent address')
                            ->maxLength(100),

                        TextInput::make('permanent_city_municipality')
                            ->label('Permanent Address - City / Municipality')
                            ->columnSpan([
                                'md' => 1,
                                'xl' => 3,
                            ])
                            ->placeholder('Enter city or municipality of permanent address')
                            ->maxLength(100),

                        TextInput::make('permanent_barangay')
                            ->label('Permanent Address - Barangay')
                            ->columnSpan([
                                'md' => 1,
                                'xl' => 3,
                            ])
                            ->placeholder('Enter barangay of permanent address')
                            ->maxLength(100),

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
                            })->columnSpanFull(),

                        TextInput::make('current_region')
                            ->label('Current Address - Region')
                            ->columnSpan([
                                'md' => 1,
                                'xl' => 3,
                            ])
                            ->placeholder('Enter region of current address')
                            ->maxLength(100)
                            ->disabled(fn (Get $get): bool => (bool) $get('same_as_permanent_address')),

                        TextInput::make('current_province')
                            ->label('Current Address - Province')
                            ->columnSpan([
                                'md' => 1,
                                'xl' => 3,
                            ])
                            ->placeholder('Enter province of current address')
                            ->maxLength(100)
                            ->disabled(fn (Get $get): bool => (bool) $get('same_as_permanent_address')),

                        TextInput::make('current_city_municipality')
                            ->label('Current Address - City / Municipality')
                            ->columnSpan([
                                'md' => 1,
                                'xl' => 3,
                            ])
                            ->placeholder('Enter city or municipality of current address')
                            ->maxLength(100)
                            ->disabled(fn (Get $get): bool => (bool) $get('same_as_permanent_address')),

                        TextInput::make('current_barangay')
                            ->label('Current Address - Barangay')
                            ->columnSpan([
                                'md' => 1,
                                'xl' => 3,
                            ])
                            ->placeholder('Enter barangay of current address')
                            ->maxLength(100)
                            ->disabled(fn (Get $get): bool => (bool) $get('same_as_permanent_address')),
                    ]),

                Section::make('Contact Details')
                    ->columnSpanFull()
                    ->columns([
                        'md' => 2,
                    ])
                    ->schema([
                        Group::make([
                            Checkbox::make('mobile_contact_na')
                                ->label('Mobile Contact No. N/A')
                                ->live(),

                            TextInput::make('mobile_contact_no')
                                ->label('Mobile Contact No.')
                                ->tel()
                                ->placeholder('Enter mobile contact number')
                                ->maxLength(20)
                                ->hidden(fn (Get $get): bool => (bool) $get('mobile_contact_na')),
                        ])->columns(1),

                        Group::make([
                            Checkbox::make('email_na')
                                ->label('Email Address N/A')
                                ->live(),

                            TextInput::make('email_address')
                                ->label('Email Address')
                                ->email()
                                ->placeholder('Enter email address')
                                ->maxLength(255)
                                ->hidden(fn (Get $get): bool => (bool) $get('email_na')),
                        ])->columns(1),
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
                            ->live()
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('relationship_other_specify')
                            ->label('Other, Specify')
                            ->maxLength(100)
                            ->visible(fn (Get $get): bool => $get('relationship_to_patient') === 'other')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
