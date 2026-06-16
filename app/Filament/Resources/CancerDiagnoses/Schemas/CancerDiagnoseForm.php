<?php

namespace App\Filament\Resources\CancerDiagnoses\Schemas;

use App\Models\FormDemographics;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Livewire\Component as Livewire;

class CancerDiagnoseForm
{

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Section::make('Patient / Record Link')
                //     ->description('Connect this cancer diagnosis profile to the patient demographics record.')
                //     ->columnSpanFull()
                //     ->columns([
                //         'default' => 1,
                //         'md' => 2,
                //     ])
                //     ->schema([
                //         Hidden::make('form_demographic_id')
                //             ->default(fn (): ?int => request()->integer('formId'))
                //             ->required()
                //             ->rule('integer')
                //             ->exists(FormDemographics::class, 'id'),

                //         Hidden::make('has_multiple_active_primary_cancer_sites')
                //             ->label('More than 1 Active Primary Cancer Site?')
                //             ->helperText('Enable this if the patient has multiple   active primary cancer sites.')
                //             ->live(debounce: 500)
                //             ->default(false),

                        
                //         Hidden::make('primary_cancer_site_number')
                //             ->label('Primary Cancer Site Number')
                //             ->numeric()
                //             ->default(1)
                //             ->required()
                //     ]),

                View::make('filament.forms.components.treatment-plan-loading-overlay')
                ->viewData([
                    'target' => [
                        'data.has_surgery',
                        'data.has_anti_cancer_drug',
                        'data.has_radiotherapy',
                        'data.has_theranostics',
                        'data.has_palliative_care',
                        'data.has_multidisciplinary_cancer_team',
                        'data.is_gicc_indexed_cancer',
                        'data.iccc_specific_classification',
                        'data.has_other_cancer_directed_therapy'
                    ],
                    ])
                    ->liberatedFromContainerGrid(),

                Section::make('Profile of Cancer Diagnosis')
                    ->description('Basic diagnosis information for this primary cancer site.')
                    ->columnSpanFull()
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                        'xl' => 4,
                    ])
                    ->schema([
                        DatePicker::make('diagnosis_date')
                            ->label('Date of Diagnosis')
                            ->native(false)
                            ->displayFormat('Y-m-d')
                            ->columnSpan(2)
                            ->placeholder('Select date of diagnosis')
                            ->required(),

                        TextInput::make('age_at_diagnosis_years')
                            ->label('Age at Diagnosis - Years')
                            ->numeric()
                            ->minValue(0)
                            ->placeholder('Enter age at diagnosis in years')
                            ->maxValue(120)
                            ->nullable(),

                        TextInput::make('age_at_diagnosis_months')
                            ->label('Age at Diagnosis - Months')
                            ->numeric()
                            ->minValue(0)
                            ->placeholder('Enter age at diagnosis in months')
                            ->maxValue(11)
                            ->nullable(),
                    ]),

                Section::make('Primary Cancer Diagnosis')
                    ->columnSpanFull()
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                        'xl' => 3,
                    ])
                    ->schema([
                        Select::make('basis_for_diagnosis')
                            ->label('Basis for Diagnosis')
                            ->options([
                                'Clinical Only' => 'Clinical Only',
                                'Clinical Investigation' => 'Clinical Investigation',
                                'Specific Tumour Markers' => 'Specific Tumour Markers',
                                'Cytology / Hematology' => 'Cytology / Hematology',
                                'Histology of Metastasis' => 'Histology of Metastasis',
                                'Histology of Primary' => 'Histology of Primary',
                                'Unknown' => 'Unknown',
                            ])
                            ->columnSpan(2)
                            ->native(false)
                            ->searchable()
                            ->required(),

                        Select::make('iccc_specific_classification')
                            ->label('Specific Classification')
                            ->options(self::icccDiagnosisOptions())
                            ->native(false)
                            ->searchable()
                            ->live(debounce: 500)
                            ->afterStateUpdated(fn (?string $state, Set $set): null => self::fillIcccClassifications($state, $set))
                            ->required(),

                        TextInput::make('iccc_parent_classification')
                            ->label('Parent Classification')
                            ->placeholder('Auto-filled from specific classification')
                            ->formatStateUsing(fn (Get $get, ?string $state): ?string => $state ?: self::icccClassificationValue(
                                $get('iccc_specific_classification'),
                                'parent',
                            ))
                            ->readOnly(),

                        TextInput::make('iccc_general_classification')
                            ->label('General Classification')
                            ->placeholder('Auto-filled from specific classification')
                            ->formatStateUsing(fn (Get $get, ?string $state): ?string => $state ?: self::icccClassificationValue(
                                $get('iccc_specific_classification'),
                                'general',
                            ))
                            ->columnSpan(2)
                            ->readOnly(),

                        

                        Flex::make([
                            Toggle::make('is_gicc_indexed_cancer')
                                ->label('Diagnosis is one of the GICC 6 Indexed Cancers?')
                                ->live(debounce: 500)
                                ->columnSpan(1)
                                ->default(false),

                            Select::make('gicc_indexed_cancer_type')
                                ->label('If Yes, Specify GICC Indexed Cancer')
                                ->options([
                                    'Acute Lymphoblastic Leukemia' => 'Acute Lymphoblastic Leukemia',
                                    'Burkitt Lymphoma' => 'Burkitt Lymphoma',
                                    'Hodgkin Lymphoma' => 'Hodgkin Lymphoma',
                                    'Low Grade Glioma' => 'Low Grade Glioma',
                                    'Retinoblastoma' => 'Retinoblastoma',
                                    'Wilms Tumor' => 'Wilms Tumor',
                                ])
                                ->native(false)
                                ->columnSpan(1)
                                ->searchable()
                                ->required(fn (Get $get): bool => (bool) $get('is_gicc_indexed_cancer'))
                                ->visible(fn (Get $get): bool => (bool) $get('is_gicc_indexed_cancer')),
                        ])->columnSpanFull(),

                        Select::make('topography')
                            ->label('Topography')
                            ->options(self::topographyOptions())
                            ->native(false)
                            ->searchable()
                            ->live(debounce: 500)
                            ->required(),

                        TextInput::make('topography_other')
                            ->label('Other Topography')
                            ->maxLength(255)
                            ->required(fn (Get $get): bool => $get('topography') === 'Others')
                            ->visible(fn (Get $get): bool => $get('topography') === 'Others')
                            ->placeholder('Please specify other topography if "Others" is selected.')
                            ->live(debounce: 500),

                        Select::make('laterality')
                            ->label('Cancer Laterality')
                            ->options([
                                'Left' => 'Left',
                                'Right' => 'Right',
                                'Bilateral' => 'Bilateral',
                                'Not Applicable' => 'Not Applicable',
                                'Unknown' => 'Unknown',
                            ])
                            ->native(false)
                            ->searchable()
                            ->nullable(),

                        CheckboxList::make('metastasis_sites')
                            ->label('Metastasis Site/s')
                            ->options([
                                'Bone' => 'Bone',
                                'Bone Marrow' => 'Bone Marrow',
                                'Brain' => 'Brain',
                                'Cerebrospinal Fluid' => 'Cerebrospinal Fluid',
                                'Liver' => 'Liver',
                                'Lung' => 'Lung',
                                'Lymph Node' => 'Lymph Node',
                                'Spine' => 'Spine',
                                'Testes' => 'Testes',
                                'Other' => 'Other, specify',
                            ])
                            ->columns([
                                'default' => 1,
                                'md' => 2,
                                'xl' => 3,
                            ])
                            ->bulkToggleable()
                            ->searchable()
                            ->live(debounce: 500)
                            ->columnSpanFull(),

                        TextInput::make('metastasis_other_site')
                            ->label('Other Metastasis Site')
                            ->maxLength(255)
                            ->required(fn (Get $get): bool => in_array('Other', $get('metastasis_sites') ?? [], true))
                            ->visible(fn (Get $get): bool => in_array('Other', $get('metastasis_sites') ?? [], true)),

                        Textarea::make('details_for_diagnosis')
                            ->label('Details for Diagnosis')
                            ->rows(4)
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('icd10_code')
                            ->label('ICD-10 Code No.')
                            ->placeholder('Example: C75.3')
                            ->required(),

                        TextInput::make('icdo3_topography_code')
                            ->label('ICD-O-3 Topography Code')
                            ->placeholder('Example: C75.3')
                            ->nullable(),

                        TextInput::make('icdo3_morphology_code')
                            ->label('ICD-O-3 Morphology Code')
                            ->placeholder('Example: 9394/3')
                            ->nullable(),
                    ]),

                Section::make('Clinical Staging')
                    ->columnSpanFull()
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                        'xl' => 3,
                    ])
                    ->schema([
                        Select::make('clinical_stage')
                            ->label('Clinical Stage')
                            ->options([
                                'Stage 0' => 'Stage 0',
                                'Stage I' => 'Stage I',
                                'Stage II' => 'Stage II',
                                'Stage III' => 'Stage III',
                                'Stage IV' => 'Stage IV',
                                'Stage V' => 'Stage V',
                                'Not Applicable' => 'Not Applicable',
                                'Unknown' => 'Unknown',
                            ])
                            ->native(false)
                            ->searchable()
                            ->required(),

                        Select::make('staging_used')
                            ->label('Staging Used')
                            ->options([
                                'Ann Arbor' => 'Ann Arbor - Hodgkin Lymphoma',
                                'St. Jude / Murphy' => 'St. Jude / Murphy - NHL',
                                'MTS / AJCC TNM' => 'Musculoskeletal Tumor Society / AJCC TNM',
                                'Intraocular Retinoblastoma' => 'International Classification for Intraocular Retinoblastoma',
                                'COG / PRETEXT' => 'COG / PRETEXT Staging for Hepatoblastoma',
                                'NWTS / COG / SIOP' => 'NWTS / COG / SIOP Staging Wilms Tumor',
                                'INSS / INRG' => 'INSS / INRG Staging for Neuroblastoma',
                                'COG / CCLG Germ Cell' => 'COG / CCLG Staging for Germ Cell Tumors',
                                'Toronto Tier-1' => 'Toronto Tier-1 Staging',
                                'Other' => 'Other',
                            ])
                            ->native(false)
                            ->searchable()
                            ->multiple()
                            ->nullable()
                            ->live(debounce: 500),

                        Textarea::make('staging_other_remarks')
                            ->label('Other Remarks')
                            ->rows(3)
                            ->required(fn (Get $get): bool => in_array('Other', $get('staging_used') ?? [], true))
                            ->visible(fn (Get $get): bool => in_array('Other', $get('staging_used') ?? [], true))
                            ->live(debounce: 500)
                            ->columnSpanFull(),
                    ]),

                Section::make('Disease Status')
                    ->columnSpanFull()
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ])
                    ->schema([
                        Select::make('current_status_of_cancer')
                            ->label('Current Status of Cancer')
                            ->options([
                                'Newly diagnosed' => 'Newly diagnosed',
                                'New case to center, no treatment' => 'New case to center, no treatment',
                                'New case to center, received treatment elsewhere' => 'New case to center, received treatment elsewhere',
                                'Old case to center, relapse/refractory' => 'Old case to center, relapse/refractory',
                                'Old case to center, returning' => 'Old case to center, returning',
                                'Old case, secondary malignancy' => 'Old case, secondary malignancy',
                            ])
                            ->native(false)
                            ->searchable()
                            ->required(),
                    ]),

                Section::make('Multidisciplinary Cancer Team Approach')
                    ->columnSpanFull()
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ])
                    ->schema([
                        Toggle::make('has_multidisciplinary_cancer_team')
                            ->label('Multidisciplinary Cancer Team Approach Practiced?')
                            ->live(debounce: 500)
                            ->default(false),

                        CheckboxList::make('multidisciplinary_disciplines')
                            ->label('Disciplines Involved')
                            ->options([
                                'Anesthesia / Pain' => 'Anesthesia / Pain',
                                'Child Life Specialist' => 'Child Life Specialist',
                                'Complementary and Alternative' => 'Complementary and Alternative',
                                'Gynecologic Oncology' => 'Gynecologic Oncology',
                                'Pathology' => 'Pathology',
                                'Pediatric Hematology' => 'Pediatric Hematology',
                                'Pediatric Oncology' => 'Pediatric Oncology',
                                'Radiation Oncology' => 'Radiation Oncology',
                                'Rehabilitation Medicine' => 'Rehabilitation Medicine',
                                'Supportive / Palliative Care' => 'Supportive / Palliative Care',
                                'Other' => 'Others, specify',
                            ])
                            ->columns([
                                'default' => 1,
                                'md' => 2,
                                'xl' => 3,
                            ])
                            ->searchable()
                            ->bulkToggleable()
                            ->live(debounce: 500)
                            ->required(fn (Get $get): bool => (bool) $get('has_multidisciplinary_cancer_team'))
                            ->visible(fn (Get $get): bool => (bool) $get('has_multidisciplinary_cancer_team'))
                            ->columnSpanFull(),

                        TextInput::make('multidisciplinary_other_discipline')
                            ->label('Other Discipline')
                            ->maxLength(255)
                            ->required(fn (Get $get): bool => in_array('Other', $get('multidisciplinary_disciplines') ?? [], true))
                            ->visible(fn (Get $get): bool => in_array('Other', $get('multidisciplinary_disciplines') ?? [], true)),
                    ]),

                Section::make('Treatment Plan')
                    ->columnSpanFull()
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                        'xl' => 3,
                    ])
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                Grid::make()
                                    ->schema([
                                        Toggle::make('has_surgery')
                                            ->label('Surgery?')
                                            ->live()
                                            ->columnSpan(3)
                                            ->default(false)
                                            ->afterStateUpdated(function (Set $set, ?bool $state): void {
                                                if (! $state) {
                                                    $set('surgery_goal', null);
                                                }
                                            }),

                                        Select::make('surgery_goal')
                                            ->label('Surgery Goal')
                                            ->options([
                                                'Definitive' => 'Definitive',
                                                'Debulking' => 'Debulking',
                                                'Diagnostic' => 'Diagnostic',
                                                'Reconstructive' => 'Reconstructive',
                                            ])
                                            ->native(false)
                                            ->columnSpan(2)
                                            ->required(fn (Get $get): bool => (bool) $get('has_surgery'))
                                            ->visible(fn (Get $get): bool => (bool) $get('has_surgery'))
                                            ->dehydrated(fn (Get $get): bool => (bool) $get('has_surgery')),
                                    ])->columnSpanFull(),

                                Grid::make()
                                ->schema([
                                    Toggle::make('has_anti_cancer_drug')
                                        ->label('Anti-Cancer Drug?')
                                        ->live(debounce: 500)
                                        ->default(false),

                                    Select::make('anti_cancer_drug_purpose')
                                        ->label('Purpose of Drug Administration')
                                        ->options([
                                            'Curative' => 'Curative',
                                            'Palliative' => 'Palliative',
                                        ])->columnSpanFull()
                                        ->native(false)
                                        ->required(fn (Get $get): bool => (bool) $get('has_anti_cancer_drug'))
                                        ->visible(fn (Get $get): bool => (bool) $get('has_anti_cancer_drug')),

                                    CheckboxList::make('anti_cancer_drug_types')
                                        ->label('Drug Type/s')
                                        ->options([
                                            'Cytotoxic' => 'Cytotoxic',
                                            'Hormonal' => 'Hormonal',
                                            'Immunologic' => 'Immunologic',
                                            'Targeted' => 'Targeted',
                                            'Other' => 'Other, specify',
                                        ])
                                        ->live(debounce: 500)
                                        ->required(fn (Get $get): bool => (bool) $get('has_anti_cancer_drug'))
                                        ->visible(fn (Get $get): bool => (bool) $get('has_anti_cancer_drug')),

                                    TextInput::make('anti_cancer_drug_other_type')
                                        ->label('Other Drug Type')
                                        ->maxLength(255)
                                        ->columnSpanFull()
                                        ->required(fn (Get $get): bool => in_array('Other', $get('anti_cancer_drug_types') ?? [], true))
                                        ->visible(fn (Get $get): bool => in_array('Other', $get('anti_cancer_drug_types') ?? [], true)),
                                ])->columnSpanFull(),

                            Grid::make()
                                ->schema([
                                    Toggle::make('has_radiotherapy')
                                        ->label('Radiotherapy?')
                                        ->live(debounce: 500)
                                        ->default(false),

                                    Select::make('radiotherapy_goal')
                                        ->label('Goal of Radiotherapy')
                                        ->options([
                                            'Curative' => 'Curative',
                                            'Palliative' => 'Palliative',
                                        ])->columnSpanFull()
                                        ->native(false)
                                        ->required(fn (Get $get): bool => (bool) $get('has_radiotherapy'))
                                        ->visible(fn (Get $get): bool => (bool) $get('has_radiotherapy')),
                                ])->columnSpanFull(),

                            Grid::make()
                                ->schema([
                                    Toggle::make('has_theranostics')
                                        ->label('Theranostics?')
                                        ->live(debounce: 500)
                                        ->default(false),

                                    Select::make('theranostics_goal')
                                        ->label('Goal of Theranostic Therapy')
                                        ->options([
                                            'Curative' => 'Curative',
                                            'Palliative' => 'Palliative',
                                        ])
                                        ->columnSpanFull()
                                        ->native(false)
                                        ->required(fn (Get $get): bool => (bool) $get('has_theranostics'))
                                        ->visible(fn (Get $get): bool => (bool) $get('has_theranostics')),
                                ])->columnSpanFull(),

                            Grid::make()
                                ->schema([
                                    Toggle::make('has_palliative_care')
                                        ->label('Pediatric Palliative Care?')
                                        ->live(debounce: 500)
                                        ->default(false),

                                    Select::make('palliative_care_provider')
                                        ->label('Main Palliative Care Provider')
                                        ->options([
                                            'Palliative Care Physician' => 'Palliative Care Physician',
                                            'Pediatric Oncologist' => 'Pediatric Oncologist',
                                            'Palliative Care Nurse' => 'Palliative Care Nurse',
                                            'Pain Management Specialist' => 'Pain Management Specialist',
                                            'Community Based Palliative Care' => 'Community Based Palliative Care',
                                        ])
                                        ->native(false)
                                        ->searchable()
                                        ->columnSpanFull()
                                        ->required(fn (Get $get): bool => (bool) $get('has_palliative_care'))
                                        ->visible(fn (Get $get): bool => (bool) $get('has_palliative_care')),
                                ])->columnSpanFull(),
                            ])
                            ->columnSpanFull()
                            ->extraAttributes([
                                'wire:loading.attr' => 'inert',
                                'wire:target' => 'data.has_surgery',
                            ]),
                    ]),

                Section::make('Other Cancer Directed Therapies')
                    ->columnSpanFull()
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ])
                    ->schema([
                        Toggle::make('has_other_cancer_directed_therapy')
                            ->label('Other Cancer Directed Therapies?')
                            ->live(debounce: 500)
                            ->default(false),

                        CheckboxList::make('other_cancer_directed_therapy_types')
                            ->label('Therapy Type/s')
                            ->options([
                                'Transplant' => 'Transplant',
                                'RAI' => 'RAI',
                                'Chemoembolization' => 'Chemoembolization',
                                'Other' => 'Others, specify',
                            ])
                            ->columns([
                                'default' => 1,
                                'md' => 2,
                            ])
                            ->columnSpanFull()
                            ->live(debounce: 500)
                            ->required(fn (Get $get): bool => (bool) $get('has_other_cancer_directed_therapy'))
                            ->visible(fn (Get $get): bool => (bool) $get('has_other_cancer_directed_therapy')),

                        TextInput::make('other_cancer_directed_therapy_other_type')
                            ->label('Other Cancer Directed Therapy')
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->required(fn (Get $get): bool => in_array('Other', $get('other_cancer_directed_therapy_types') ?? [], true))
                            ->visible(fn (Get $get): bool => in_array('Other', $get('other_cancer_directed_therapy_types') ?? [], true)),

                        Select::make('overall_goal_of_therapy')
                            ->label('Overall Goal of Therapy')
                            ->options([
                                'Curative' => 'Curative',
                                'Palliative' => 'Palliative',
                            ])
                            ->native(false)
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected static function icccDiagnosisOptions(): array
    {
        $specificClassifications = array_keys(self::icccClassifications());

        return array_combine($specificClassifications, $specificClassifications);
    }

    protected static function fillIcccClassifications(?string $specificClassification, Set $set): null
    {
        $set('iccc_parent_classification', self::icccClassificationValue($specificClassification, 'parent'));
        $set('iccc_general_classification', self::icccClassificationValue($specificClassification, 'general'));

        return null;
    }

    protected static function icccClassificationValue(?string $specificClassification, string $classificationLevel): ?string
    {
        if (blank($specificClassification)) {
            return null;
        }

        return self::icccClassifications()[$specificClassification][$classificationLevel] ?? null;
    }

    /**
     * @return array<string, array{parent: ?string, general: string}>
     */
    protected static function icccClassifications(): array
    {
        $classifications = [];

        $add = static function (string $general, ?string $parent, array $specificClassifications) use (&$classifications): void {
            foreach ($specificClassifications as $specificClassification) {
                $classifications[$specificClassification] = [
                    'parent' => $parent,
                    'general' => $general,
                ];
            }
        };

        $leukemias = 'Leukemias, myeloproliferative diseases, myelodysplastic disease';
        $add($leukemias, null, [$leukemias]);
        $add($leukemias, 'Lymphoid leukemia', [
            'Lymphoid leukemia',
            'Precursor cell leukemia',
            'Mature B cell leukemia',
            'Mature T cell and NK cell leukemia',
        ]);
        $add($leukemias, 'Acute Myeloid leukemia', ['Acute Myeloid leukemia']);
        $add($leukemias, 'Chronic myeloproliferative disease', ['Chronic myeloproliferative disease']);
        $add($leukemias, 'Myelodysplastic syndrome and other myeloproliferative diseases', ['Myelodysplastic syndrome and other myeloproliferative diseases']);
        $add($leukemias, 'Unspecified and other specified leukemias', ['Unspecified and other specified leukemias']);

        $lymphomas = 'Lymphoma and reticuloendothelial neoplasms';
        $add($lymphomas, null, [$lymphomas]);
        $add($lymphomas, 'Hodgkin lymphoma', ['Hodgkin lymphoma']);
        $add($lymphomas, 'Non-Hodgkin lymphoma', [
            'Non-Hodgkin lymphoma',
            'Precursor cell lymphoma',
            'Mature B-cell lymphomas',
            'Mature T-cell and NK-cell lymphoma',
            'Burkitt lymphoma',
        ]);

        $cns = 'CNS and miscellaneous intracranial and intraspinal neoplasms';
        $add($cns, null, [$cns]);
        $add($cns, 'Ependymoma and choroid plexus tumors', ['Ependymoma and choroid plexus tumors']);
        $add($cns, 'Astrocytomas', ['Astrocytomas']);
        $add($cns, 'Medulloblastoma', ['Medulloblastoma']);
        $add($cns, 'Primitive neuroectodermal tumor', ['Primitive neuroectodermal tumor']);
        $add($cns, 'Atypical teratoid / rhabdoid tumor', ['Atypical teratoid / rhabdoid tumor']);
        $add($cns, 'Oligodendroglioma', ['Oligodendroglioma']);

        $neuroblastoma = 'Neuroblastoma and other peripheral nervous cell tumors';
        $add($neuroblastoma, null, [$neuroblastoma]);
        $add($neuroblastoma, 'Retinoblastoma', ['Retinoblastoma']);

        $renalTumors = 'Renal tumors';
        $add($renalTumors, null, [$renalTumors]);
        $add($renalTumors, 'Nephroblastoma / Wilms Tumor', ['Nephroblastoma / Wilms Tumor']);
        $add($renalTumors, 'Rhabdoid renal tumor', ['Rhabdoid renal tumor']);
        $add($renalTumors, 'Kidney sarcomas', ['Kidney sarcomas']);
        $add($renalTumors, 'Renal carcinomas', ['Renal carcinomas']);

        $hepaticTumors = 'Hepatic tumors';
        $add($hepaticTumors, null, [$hepaticTumors]);
        $add($hepaticTumors, 'Hepatoblastoma', ['Hepatoblastoma']);
        $add($hepaticTumors, 'Rhabdoid hepatic tumor', ['Rhabdoid hepatic tumor']);
        $add($hepaticTumors, 'Embryonal sarcoma of the liver', ['Embryonal sarcoma of the liver']);
        $add($hepaticTumors, 'Hepatic carcinoma', ['Hepatic carcinoma']);

        $boneTumors = 'Malignant bone tumors';
        $add($boneTumors, null, [$boneTumors]);
        $add($boneTumors, 'Osteosarcoma', ['Osteosarcoma']);
        $add($boneTumors, 'Chondrosarcoma', ['Chondrosarcoma']);
        $add($boneTumors, 'Ewing sarcoma', ['Ewing sarcoma']);

        $softTissueSarcomas = 'Soft tissue and other extraosseous sarcomas';
        $add($softTissueSarcomas, null, [$softTissueSarcomas]);
        $add($softTissueSarcomas, 'Rhabdomyosarcoma', ['Rhabdomyosarcoma']);
        $add($softTissueSarcomas, 'Fibrosarcoma', ['Fibrosarcoma']);
        $add($softTissueSarcomas, 'Kaposi sarcoma', ['Kaposi sarcoma']);
        $add($softTissueSarcomas, 'Liposarcoma', ['Liposarcoma']);
        $add($softTissueSarcomas, 'Leiomyosarcoma', ['Leiomyosarcoma']);
        $add($softTissueSarcomas, 'Synovial sarcoma', ['Synovial sarcoma']);

        $germCellTumors = 'Germ cell tumors, trophoblastic tumors and neoplasms of gonads';
        $add($germCellTumors, null, [$germCellTumors]);
        $add($germCellTumors, 'Intracranial and intraspinal germ cell tumors', ['Intracranial and intraspinal germ cell tumors']);
        $add($germCellTumors, 'Malignant extracranial and extragonadal germ cell tumors', ['Malignant extracranial and extragonadal germ cell tumors']);
        $add($germCellTumors, 'Malignant gonadal germ cell tumors', ['Malignant gonadal germ cell tumors']);

        $epithelialNeoplasms = 'Other malignant epithelial neoplasms and malignant melanomas';
        $add($epithelialNeoplasms, null, [$epithelialNeoplasms]);
        $add($epithelialNeoplasms, 'Thyroid carcinoma', ['Thyroid carcinoma']);
        $add($epithelialNeoplasms, 'Nasopharyngeal carcinoma', ['Nasopharyngeal carcinoma']);
        $add($epithelialNeoplasms, 'Malignant melanoma', ['Malignant melanoma']);
        $add($epithelialNeoplasms, 'Skin carcinoma', ['Skin carcinoma']);

        $otherNeoplasms = 'Other and unspecified malignant neoplasms';
        $add($otherNeoplasms, null, [$otherNeoplasms]);
        $add($otherNeoplasms, 'Malignant gastrointestinal stromal tumor', ['Malignant gastrointestinal stromal tumor']);
        $add($otherNeoplasms, 'Pancreatoblastoma', ['Pancreatoblastoma']);
        $add($otherNeoplasms, 'Pulmonary blastoma and pleuropulmonary blastoma', ['Pulmonary blastoma and pleuropulmonary blastoma']);
        $add($otherNeoplasms, 'Mesothelioma', ['Mesothelioma']);
        $add($otherNeoplasms, 'Other unspecified malignant tumors', ['Other unspecified malignant tumors']);

        return $classifications;
    }

    protected static function topographyOptions(): array
    {
        return [
            'Lips' => 'Lips',
            'Base of Tongue' => 'Tongue - Base of Tongue',
            'Other unspecified parts of tongue' => 'Other unspecified parts of tongue',
            'Gums' => 'Gums',
            'Floor of Mouth' => 'Floor of Mouth',
            'Palate' => 'Palate',
            'Parotid Gland' => 'Parotid Gland',
            'Major Salivary Glands' => 'Other unspecified major salivary glands',
            'Tonsil' => 'Tonsil',
            'Oropharynx' => 'Oropharynx',
            'Nasopharynx' => 'Nasopharynx',
            'Pyriform Sinus' => 'Pyriform Sinus',
            'Hypopharynx' => 'Hypopharynx',
            'Esophagus' => 'Esophagus',
            'Stomach' => 'Stomach',
            'Small Intestine' => 'Small Intestine',
            'Colon' => 'Colon',
            'Rectosigmoid Junction' => 'Rectosigmoid Junction',
            'Rectum' => 'Rectum',
            'Anus and Anal Canal' => 'Anus and Anal Canal',
            'Liver and Intrahepatic Bile Ducts' => 'Liver and Intrahepatic Bile Ducts',
            'Gallbladder' => 'Gallbladder',
            'Biliary Tract' => 'Other / Unspecified parts of Biliary Tract',
            'Pancreas' => 'Pancreas',
            'Nasal Cavity and Middle Ear' => 'Nasal Cavity and Middle Ear',
            'Accessory Sinuses' => 'Accessory Sinuses',
            'Larynx' => 'Larynx',
            'Trachea' => 'Trachea',
            'Bronchus and Lung' => 'Bronchus and Lung',
            'Thymus' => 'Thymus',
            'Heart, mediastinum, or pleura' => 'Heart, mediastinum, or pleura',
            'Bones, Joints, and Articular Cartilage of Limbs' => 'Bones, Joints, and Articular Cartilage of Limbs',
            'Bones, Joints, and Articular Cartilage of Other Sites' => 'Bones, Joints, and Articular Cartilage of Other / Unspecified Sites',
            'Hematopoietic / Reticuloendothelial Systems' => 'Hematopoietic / Reticuloendothelial Systems',
            'Skin' => 'Skin',
            'Peripheral Nerves / Autonomic Nervous System' => 'Peripheral Nerves / Autonomic Nervous System',
            'Retroperitoneum and Peritoneum' => 'Retroperitoneum and Peritoneum',
            'Connective, subcutaneous, and other soft tissues' => 'Connective, subcutaneous, and other soft tissues',
            'Breast' => 'Breast',
            'Vulva' => 'Vulva',
            'Vagina' => 'Vagina',
            'Cervix Uteri' => 'Cervix Uteri',
            'Corpus Uteri' => 'Corpus Uteri',
            'Uterus' => 'Uterus - Non-specific',
            'Ovary' => 'Ovary',
            'Placenta' => 'Placenta',
            'Penis' => 'Penis',
            'Prostate Gland' => 'Prostate Gland',
            'Testis' => 'Testis',
            'Kidney' => 'Kidney',
            'Renal Pelvis' => 'Renal Pelvis',
            'Ureter' => 'Ureter',
            'Bladder' => 'Bladder',
            'Eye and Adnexa' => 'Eye and Adnexa',
            'Meninges' => 'Meninges',
            'Brain' => 'Brain',
            'Spinal Cord, Cranial Nerves, and other parts of CNS' => 'Spinal Cord, Cranial Nerves, and other parts of CNS',
            'Thyroid gland' => 'Thyroid gland',
            'Adrenal gland' => 'Adrenal gland',
            'Other endocrine gland and related structures' => 'Other endocrine gland and related structures',
            'Lymph nodes' => 'Lymph nodes',
            'Unknown primary sites' => 'Unknown primary sites',
            'Other / Ill-defined sites' => 'Other / Ill-defined sites',
            'Others' => 'Others Please Specify:'
        ];
    }
}
