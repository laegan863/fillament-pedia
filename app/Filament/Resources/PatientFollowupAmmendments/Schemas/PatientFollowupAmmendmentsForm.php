<?php

namespace App\Filament\Resources\PatientFollowupAmmendments\Schemas;

use App\Filament\Resources\CancerDiagnoses\Schemas\CancerDiagnoseForm;
use App\Models\SpecificClassification;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;

class PatientFollowupAmmendmentsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(self::schema());
    }

    public static function schema(): array
    {
        return [
            View::make('filament.forms.components.loading-overlay')
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
                        ->placeholder('Select date of diagnosis'),

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
                        ->searchable(),

                    Select::make('specific_classification_id')
                        ->label('Specific Classification')
                        ->options(CancerDiagnoseForm::icccDiagnosisOptions())
                        ->native(false)
                        ->searchable()
                        ->live()
                        ->afterStateHydrated(fn (mixed $state, Get $get, Set $set): null => CancerDiagnoseForm::hydrateIcccClassifications($state, $get, $set))
                        ->afterStateUpdated(fn (mixed $state, Set $set): null => CancerDiagnoseForm::fillIcccClassifications($state, $set))
                        ->exists(SpecificClassification::class, 'id'),

                    Hidden::make('iccc_specific_classification'),

                    TextInput::make('iccc_parent_classification')
                        ->label('Parent Classification')
                        ->placeholder('Auto-filled from specific classification')
                        ->formatStateUsing(fn (Get $get, ?string $state): ?string => $state ?: CancerDiagnoseForm::icccClassificationValue(
                            $get('specific_classification_id'),
                            'parent',
                        ))
                        ->readOnly(),

                    TextInput::make('iccc_general_classification')
                        ->label('General Classification')
                        ->placeholder('Auto-filled from specific classification')
                        ->formatStateUsing(fn (Get $get, ?string $state): ?string => $state ?: CancerDiagnoseForm::icccClassificationValue(
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
                        ->options(CancerDiagnoseForm::topographyOptions())
                        ->native(false)
                        ->searchable(),

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
                        ->columnSpanFull(),

                    TextInput::make('icd10_code')
                        ->label('ICD-10 Code No.')
                        ->placeholder('Example: C75.3'),

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
                        ->searchable(),

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

        ];
    }
}
