<?php

namespace App\Filament\Resources\CancerDiagnoses\Schemas;

use App\Models\SpecificClassification;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;

class CancerDiagnoseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                View::make('filament.forms.components.loading-overlay')
                    ->viewData([
                        'target' => [
                            'data.has_surgery',
                            'data.has_anti_cancer_drug',
                            'data.has_radiotherapy',
                            'data.has_theranostics',
                            'data.has_palliative_care',
                            'data.has_multidisciplinary_cancer_team',
                            'data.is_gicc_indexed_cancer',
                            'data.specific_classification_id',
                            'data.has_other_cancer_directed_therapy',
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

                        Select::make('specific_classification_id')
                            ->label('Specific Classification')
                            ->options(self::icccDiagnosisOptions())
                            ->native(false)
                            ->searchable()
                            ->live(debounce: 500)
                            ->afterStateHydrated(fn (mixed $state, Get $get, Set $set): null => self::hydrateIcccClassifications($state, $get, $set))
                            ->afterStateUpdated(fn (mixed $state, Set $set): null => self::fillIcccClassifications($state, $set))
                            ->exists(SpecificClassification::class, 'id')
                            ->required(),

                        Hidden::make('iccc_specific_classification'),

                        TextInput::make('iccc_parent_classification')
                            ->label('Parent Classification')
                            ->placeholder('Auto-filled from specific classification')
                            ->formatStateUsing(fn (Get $get, ?string $state): ?string => $state ?: self::icccClassificationValue(
                                $get('specific_classification_id'),
                                'parent',
                            ))
                            ->readOnly(),

                        TextInput::make('iccc_general_classification')
                            ->label('General Classification')
                            ->placeholder('Auto-filled from specific classification')
                            ->formatStateUsing(fn (Get $get, ?string $state): ?string => $state ?: self::icccClassificationValue(
                                $get('specific_classification_id'),
                                'general',
                            ))
                            ->columnSpan(2)
                            ->readOnly(),

                        Group::make([
                            Toggle::make('is_gicc_indexed_cancer')
                                ->label('Diagnosis is one of the GICC 6 Indexed Cancers?')
                                ->columnSpanFull()
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
                                ->columnSpanFull()
                                ->searchable()
                                ->required(fn (Get $get): bool => (bool) ($get('is_gicc_indexed_cancer') ?? false))
                                ->visibleJs(<<<'JS'
                                    $get('is_gicc_indexed_cancer')
                                JS),
                        ])->columnSpanFull(),

                        Select::make('topography')
                            ->label('Topography')
                            ->options(self::topographyOptions())
                            ->native(false)
                            ->searchable()
                            ->required(),

                        TextInput::make('topography_other')
                            ->label('Other Topography')
                            ->maxLength(255)
                            ->required(fn (Get $get): bool => ($get('topography') ?? '') === 'Others')
                            ->visibleJs(<<<'JS'
                                ($get('topography') ?? '') === 'Others'
                            JS)
                            ->placeholder('Please specify other topography if "Others" is selected.'),

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
                            ->columnSpanFull(),

                        TextInput::make('metastasis_other_site')
                            ->label('Other Metastasis Site')
                            ->maxLength(255)
                            ->required(fn (Get $get): bool => in_array('Other', Arr::wrap($get('metastasis_sites')), true))
                            ->visibleJs(<<<'JS'
                                ($get('metastasis_sites') ?? []).includes('Other')
                            JS),

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
                            ->multiple()
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
                            ->nullable(),

                        Textarea::make('staging_other_remarks')
                            ->label('Other Remarks')
                            ->rows(3)
                            ->required(fn (Get $get): bool => in_array('Other', Arr::wrap($get('staging_used')), true))
                            ->visibleJs(<<<'JS'
                                Array.isArray($get('staging_used')) && $get('staging_used').includes('Other')
                            JS)
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
                            ->required(fn (Get $get): bool => (bool) ($get('has_multidisciplinary_cancer_team') ?? false))
                            ->hiddenJs(<<<'JS'
                                !$get('has_multidisciplinary_cancer_team')
                            JS)
                            ->columnSpanFull(),

                        TextInput::make('multidisciplinary_other_discipline')
                            ->label('Other Discipline')
                            ->maxLength(255)
                            ->required(fn (Get $get): bool => in_array('Other', Arr::wrap($get('multidisciplinary_disciplines')), true))
                            ->visibleJs(<<<'JS'
                                ($get('multidisciplinary_disciplines') ?? []).includes('Other')
                            JS),
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
                                            ->required(fn (Get $get): bool => (
                                                $get('has_surgery') ?? false
                                            ))
                                            ->visibleJs(
                                                <<<'JS'
                                                ($get('has_surgery') ?? false) === true
                                                JS
                                            )
                                            ->dehydrated(),
                                    ])->columnSpanFull(),

                                Grid::make()
                                    ->schema([
                                        Toggle::make('has_anti_cancer_drug')
                                            ->label('Anti-Cancer Drug?')
                                            ->default(false),

                                        Select::make('anti_cancer_drug_purpose')
                                            ->label('Purpose of Drug Administration')
                                            ->options([
                                                'Curative' => 'Curative',
                                                'Palliative' => 'Palliative',
                                            ])->columnSpanFull()
                                            ->native(false)
                                            ->required(fn (Get $get): bool => (
                                                $get('has_anti_cancer_drug') ?? false
                                            ))
                                            ->visibleJs(
                                                <<<'JS'
                                                ($get('has_anti_cancer_drug') ?? false) === true
                                                JS
                                            ),

                                        CheckboxList::make('anti_cancer_drug_types')
                                            ->label('Drug Type/s')
                                            ->options([
                                                'Cytotoxic' => 'Cytotoxic',
                                                'Hormonal' => 'Hormonal',
                                                'Immunologic' => 'Immunologic',
                                                'Targeted' => 'Targeted',
                                                'Other' => 'Other, specify',
                                            ])
                                            ->required(fn (Get $get): bool => (
                                                $get('has_anti_cancer_drug') ?? false
                                            ))
                                            ->visibleJs(
                                                <<<'JS'
                                                ($get('has_anti_cancer_drug') ?? false) === true
                                                JS
                                            ),

                                        TextInput::make('anti_cancer_drug_other_type')
                                            ->label('Other Drug Type')
                                            ->maxLength(255)
                                            ->columnSpanFull()
                                            ->required(fn (Get $get): bool => (
                                                $get('has_anti_cancer_drug') ?? false
                                            ) && in_array('Other', Arr::wrap($get('anti_cancer_drug_types')), true))
                                            ->visibleJs(
                                                <<<'JS'
                                                ($get('anti_cancer_drug_types') ?? []).includes('Other')
                                                JS
                                            ),
                                    ])->columnSpanFull(),

                                Grid::make()
                                    ->schema([
                                        Toggle::make('has_radiotherapy')
                                            ->label('Radiotherapy?')
                                            ->default(false),

                                        Select::make('radiotherapy_goal')
                                            ->label('Goal of Radiotherapy')
                                            ->options([
                                                'Curative' => 'Curative',
                                                'Palliative' => 'Palliative',
                                            ])->columnSpanFull()
                                            ->native(false)
                                            ->required(fn (Get $get): bool => (bool) ($get('has_radiotherapy') ?? false))
                                            ->visibleJs(
                                                <<<'JS'
                                                ($get('has_radiotherapy') ?? false) === true
                                                JS
                                            ),
                                    ])->columnSpanFull(),

                                Grid::make()
                                    ->schema([
                                        Toggle::make('has_theranostics')
                                            ->label('Theranostics?')
                                            ->default(false),

                                        Select::make('theranostics_goal')
                                            ->label('Goal of Theranostic Therapy')
                                            ->options([
                                                'Curative' => 'Curative',
                                                'Palliative' => 'Palliative',
                                            ])
                                            ->columnSpanFull()
                                            ->native(false)
                                            ->required(fn (Get $get): bool => (
                                                $get('has_theranostics') ?? false
                                            ))
                                            ->visibleJs(
                                                <<<'JS'
                                                ($get('has_theranostics') ?? false) === true
                                                JS
                                            ),
                                    ])->columnSpanFull(),

                                Grid::make()
                                    ->schema([
                                        Toggle::make('has_palliative_care')
                                            ->label('Pediatric Palliative Care?')
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
                                            ->required(fn (Get $get): bool => (
                                                $get('has_palliative_care') ?? false
                                            ))
                                            ->visibleJs(
                                                <<<'JS'
                                                ($get('has_palliative_care') ?? false) === true
                                                JS
                                            ),
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
                            ->required(fn (Get $get): bool => (
                                $get('has_other_cancer_directed_therapy') ?? false
                            ))
                            ->visibleJs(
                                <<<'JS'
                                ($get('has_other_cancer_directed_therapy') ?? false) === true
                                JS
                            ),

                        TextInput::make('other_cancer_directed_therapy_other_type')
                            ->label('Other Cancer Directed Therapy')
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->required(fn (Get $get): bool => (
                                $get('has_other_cancer_directed_therapy') ?? false
                            ) && in_array('Other', Arr::wrap($get('other_cancer_directed_therapy_types')), true))
                            ->visibleJs(
                                <<<'JS'
                                ($get('other_cancer_directed_therapy_types') ?? []).includes('Other')
                                JS
                            ),

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

    public static function icccDiagnosisOptions(): array
    {
        return SpecificClassification::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    public static function hydrateIcccClassifications(mixed $specificClassificationId, Get $get, Set $set): null
    {
        if (filled($specificClassificationId)) {
            return self::fillIcccClassifications($specificClassificationId, $set);
        }

        $specificClassification = SpecificClassification::findForFormValue($get('iccc_specific_classification'));

        if ($specificClassification === null) {
            return null;
        }

        $set('specific_classification_id', $specificClassification->id);

        return self::fillIcccClassifications($specificClassification->id, $set);
    }

    public static function fillIcccClassifications(mixed $specificClassification, Set $set): null
    {
        $classification = SpecificClassification::findForFormValue($specificClassification);

        $set('iccc_specific_classification', $classification?->name);
        $set('iccc_parent_classification', $classification?->parentClassification?->name);
        $set('iccc_general_classification', $classification?->generalClassification?->name);

        return null;
    }

    public static function icccClassificationValue(mixed $specificClassification, string $classificationLevel): ?string
    {
        if (blank($specificClassification)) {
            return null;
        }

        $classification = SpecificClassification::findForFormValue($specificClassification);

        return match ($classificationLevel) {
            'specific' => $classification?->name,
            'parent' => $classification?->parentClassification?->name,
            'general' => $classification?->generalClassification?->name,
            default => null,
        };
    }

    public static function topographyOptions(): array
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
            'Others' => 'Others Please Specify:',
        ];
    }
}
