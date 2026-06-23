<?php

namespace App\Filament\Resources\PatientFollowUpForms\Schemas;

use App\Models\FormDemographics;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class PatientFollowUpFormForm
{
    private static function overlay(): array
    {
        return [
            'data.procedures_administered',
            'data.treatment_plan',
            'data.change_in_treatment_plan_procedures',
            'data.availed_financial_support',
            'data.financial_support_mechanisms',
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                View::make('filament.forms.components.loading-overlay')
                    ->viewData([
                        'target' => self::overlay(),
                    ])->liberatedFromContainerGrid(),

                Section::make('Patient Follow-Up Information')
                    ->columnSpanFull()
                    ->schema([
                        Hidden::make('form_demographic_id')
                            ->default(fn () => request()->query('formId'))
                            ->dehydrated(),

                        TextInput::make('patient_health_facility_id_no')
                            ->label('Patient’s Health Facility ID No.')
                            ->default(function () {
                                $formId = request()->query('formId');

                                if (! $formId) {
                                    return null;
                                }

                                $patient = FormDemographics::find($formId);

                                return $patient?->health_facility_id_no
                                    ? str_pad($patient->health_facility_id_no, 10, '0', STR_PAD_LEFT)
                                    : null;
                            })
                            ->disabled()
                            ->dehydrated()
                            ->maxLength(50),

                        DatePicker::make('follow_up_last_encounter_date')
                            ->label('Date of Follow-up / Last Encounter')
                            ->native(false)
                            ->displayFormat('Y-m-d')
                            ->required(),

                        Radio::make('has_change_in_diagnosis')
                            ->label('Is there a change in Diagnosis?')
                            ->options([
                                1 => 'Yes',
                                0 => 'No',
                            ])
                            ->inline()
                            ->default(0)
                            ->live()
                            ->afterStateHydrated(fn (Radio $component, $state) => $component->state((int) $state)),

                        Radio::make('has_more_than_one_primary_site')
                            ->label('Was there more than one primary site being treated in the past quarter?')
                            ->options([
                                1 => 'Yes',
                                0 => 'No',
                            ])
                            ->inline()
                            ->default(0)
                            ->live()
                            ->afterStateHydrated(fn (Radio $component, $state) => $component->state((int) $state)),

                        Select::make('primary_sites_being_treated')
                            ->label('Primary Sites Being Treated')
                            ->helperText('Select multiple if there is more than one primary site being treated.')
                            ->multiple()
                            ->searchable()
                            ->native(false)
                            ->options([
                                'Lymphoid Leukemia, NOS' => 'Lymphoid Leukemia, NOS',
                                'Acute Lymphoblastic Leukemia' => 'Acute Lymphoblastic Leukemia',
                                'Acute Myeloid Leukemia' => 'Acute Myeloid Leukemia',
                                'Hodgkin Lymphoma' => 'Hodgkin Lymphoma',
                                'Non-Hodgkin Lymphoma' => 'Non-Hodgkin Lymphoma',
                                'Brain Tumor' => 'Brain Tumor',
                                'Bone Tumor' => 'Bone Tumor',
                                'Soft Tissue Sarcoma' => 'Soft Tissue Sarcoma',
                                'Neuroblastoma' => 'Neuroblastoma',
                                'Retinoblastoma' => 'Retinoblastoma',
                                'Wilms Tumor' => 'Wilms Tumor',
                                'Germ Cell Tumor' => 'Germ Cell Tumor',
                                'Other' => 'Other',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ]),

                Section::make('Treatment')
                    ->columnSpanFull()
                    ->schema([
                        CheckboxList::make('procedures_administered')
                            ->label('Procedures Administered')
                            ->options([
                                'Surgery' => 'Surgery',
                                'Anti-cancer drug therapy' => 'Anti-cancer drug therapy',
                                'Radiotherapy' => 'Radiotherapy',
                                'Theranostics' => 'Theranostics',
                                'Palliative Care' => 'Palliative Care',
                                'Other Treatments' => 'Other Treatments',
                            ])
                            ->descriptions([
                                'Surgery' => 'Encode the relevant data to Form 2B Surgery',
                                'Anti-cancer drug therapy' => 'Encode the relevant data to Form 2C Anti-Cancer Drug Therapy',
                                'Radiotherapy' => 'Encode the relevant data to Form 2D Radiotherapy',
                                'Theranostics' => 'Encode the relevant data to Form 2E Theranostics',
                                'Palliative Care' => 'Encode the relevant data to Form 2F Palliative Care',
                                'Other Treatments' => 'Encode the relevant data to Form 2G Other Treatments',
                            ])
                            ->columns([
                                'default' => 1,
                                'md' => 2,
                            ])
                            ->live()
                            ->bulkToggleable(),

                        // Actions::make([
                        //     Action::make('treatmentSurgery')
                        //         ->label('Add Surgery')
                        //         ->icon('heroicon-o-plus')
                        //         ->visible(fn (Get $get): bool => in_array('Surgery', $get('procedures_administered') ?? []))
                        //         ->modalHeading('Surgery Details')
                        //         ->modalSubmitActionLabel('Save')
                        //         ->form([
                        //             Section::make('Surgery Data')
                        //                 ->description('Add one or more surgery records.')
                        //                 ->schema(self::surgery()),
                        //         ])
                        //         ->fillForm(fn (Get $get): array => [
                        //             'surgeries' => $get('surgeries') ?? [],
                        //         ])
                        //         ->action(function (array $data, Set $set): void {
                        //             $set('surgeries', $data['surgeries'] ?? []);
                        //         }),

                        //     Action::make('treatmentAntiCancerDrugTherapy')
                        //         ->label('Add Anti-Cancer Drug Therapy')
                        //         ->icon('heroicon-o-plus')
                        //         ->visible(fn (Get $get): bool => in_array('Anti-cancer drug therapy', $get('procedures_administered') ?? []))
                        //         ->modalHeading('FORM 2C: Anti-Cancer Drug Therapy')
                        //         ->modalSubmitActionLabel('Save')
                        //         ->form([
                        //             Section::make('Anti-Cancer Drug Therapy Data')
                        //                 ->description('Add one or more anti-cancer drug therapy records.')
                        //                 ->schema(self::antiCancer()),
                        //         ])
                        //         ->fillForm(fn (Get $get): array => [
                        //             'anti_cancer_drug_therapies' => $get('anti_cancer_drug_therapies') ?? [],
                        //         ])
                        //         ->action(function (array $data, Set $set): void {
                        //             $set('anti_cancer_drug_therapies', $data['anti_cancer_drug_therapies'] ?? []);
                        //         }),

                        //     Action::make('treatmentRadiotherapy')
                        //         ->label('Add Radiotherapy')
                        //         ->icon('heroicon-o-plus')
                        //         ->visible(fn (Get $get): bool => in_array('Radiotherapy', $get('procedures_administered') ?? []))
                        //         ->modalHeading('FORM 2D: Radiotherapy')
                        //         ->modalSubmitActionLabel('Save Radiotherapy')
                        //         ->form([
                        //             Section::make('Radiotherapy Data')
                        //                 ->description('Add one or more radiotherapy records.')
                        //                 ->schema(self::radiotherapyRepeaterSchema()),
                        //         ])
                        //         ->fillForm(fn (Get $get): array => [
                        //             'radiotherapies' => $get('radiotherapies') ?? [],
                        //         ])
                        //         ->action(function (array $data, Set $set): void {
                        //             $set('radiotherapies', $data['radiotherapies'] ?? []);
                        //         }),

                        //     Action::make('treatmentTheranostics')
                        //         ->label('Add Theranostics')
                        //         ->icon('heroicon-o-plus')
                        //         ->visible(fn (Get $get): bool => in_array('Theranostics', $get('procedures_administered') ?? []))
                        //         ->modalHeading('Theranostics Details')
                        //         ->modalSubmitActionLabel('Save')
                        //         ->form([
                        //             Section::make('THERANOSTICS')
                        //                 ->schema(self::theranosticsRepeaterSchema()),
                        //         ])
                        //         ->fillForm(fn (Get $get): array => [
                        //             'theranostics' => $get('theranostics') ?? [],
                        //         ])
                        //         ->action(function (array $data, Set $set): void {
                        //             $set('theranostics', $data['theranostics'] ?? []);
                        //         }),

                        //     Action::make('treatmentPalliativeCare')
                        //         ->label('Add Palliative Care')
                        //         ->icon('heroicon-o-plus')
                        //         ->visible(fn (Get $get): bool => in_array('Palliative Care', $get('procedures_administered') ?? []))
                        //         ->modalHeading('Palliative Care Details')
                        //         ->modalSubmitActionLabel('Save')
                        //         ->form([
                        //             Section::make('Palliative Care')
                        //                 ->description('Palliative Care Details')
                        //                 ->schema(self::palliativeCareSchema())
                        //                 ->columnSpanFull(),
                        //         ])
                        //         ->fillForm(fn (Get $get): array => [
                        //             'palliative_cares' => $get('palliative_cares') ?? [],
                        //         ])
                        //         ->action(function (array $data, Set $set): void {
                        //             $set('palliative_cares', $data['palliative_cares'] ?? []);
                        //         }),

                        //     Action::make('treatmentOtherTreatments')
                        //         ->label('Add Other Treatments')
                        //         ->icon('heroicon-o-plus')
                        //         ->visible(fn (Get $get): bool => in_array('Other Treatments', $get('procedures_administered') ?? []))
                        //         ->modalHeading('Other Treatments Details')
                        //         ->modalSubmitActionLabel('Save')
                        //         ->form([
                        //             Section::make('Other Treatments')
                        //                 ->description('Other Treatments Details')
                        //                 ->schema(self::otherTreatmentsSchema())
                        //                 ->columnSpanFull(),
                        //         ])
                        //         ->fillForm(fn (Get $get): array => [
                        //             'other_cancer_directed_therapies' => $get('other_cancer_directed_therapies') ?? [],
                        //         ])
                        //         ->action(function (array $data, Set $set): void {
                        //             $set('other_cancer_directed_therapies', $data['other_cancer_directed_therapies'] ?? []);
                        //         }),
                        // ])->columnSpanFull(),
                    ]),

                Section::make('Surgery')
                    ->label('Surgery')
                    ->description('Encode the relevant data to Form 2B Surgery')
                    ->schema(self::surgerySchema())
                    ->columnSpanFull()
                    ->visible(fn (Get $get): bool => in_array('Surgery', $get('procedures_administered') ?? [])),

                Section::make('Anti-cancer drug therapy')
                    ->label('Anti-cancer drug therapy')
                    ->description('Encode the relevant data to Form 2B Anti-cancer drug therapy')
                    ->schema(self::antiCancerDrugTherapySchema())
                    ->columnSpanFull()
                    ->visible(fn (Get $get): bool => in_array('Anti-cancer drug therapy', $get('procedures_administered') ?? [])),

                Section::make('Radiation therapy')
                    ->label('Radiation therapy')
                    ->description('Encode the relevant data to Form 2B Radiation therapy')
                    ->schema(self::radiotherapyRepeaterSchema())
                    ->columnSpanFull()
                    ->visible(fn (Get $get): bool => in_array('Radiotherapy', $get('procedures_administered') ?? [])),

                Section::make('Theranostics')
                    ->label('Theranostics')
                    ->description('Encode the relevant data to Form 2B Theranostics')
                    ->schema(self::theranosticsRepeaterSchema())
                    ->columnSpanFull()
                    ->visible(fn (Get $get): bool => in_array('Theranostics', $get('procedures_administered') ?? [])),

                Section::make('Palliative Care')
                    ->label('Palliative Care')
                    ->description('Encode the relevant data to Form 2B Palliative Care')
                    ->schema(self::palliativeCareSchema())
                    ->columnSpanFull()
                    ->visible(fn (Get $get): bool => in_array('Palliative Care', $get('procedures_administered') ?? [])),

                Section::make('Other Treatments')
                    ->label('Other Treatments')
                    ->description('Encode the relevant data to Form 2B Other Treatments')
                    ->schema(self::otherTreatmentsSchema())
                    ->columnSpanFull()
                    ->visible(fn (Get $get): bool => in_array('Other Treatments', $get('procedures_administered') ?? [])),

                Section::make('Medical Evaluation / Treatment Outcomes')
                    ->columnSpanFull()
                    ->schema([
                        Radio::make('treatment_status')
                            ->label('Treatment Status')
                            ->options([
                                'Ongoing' => 'Ongoing',
                                'Completed' => 'Completed',
                                'Stopped/Interrupted' => 'Stopped/Interrupted',
                                'Unknown' => 'Unknown',
                                'Not Initiated' => 'Not Initiated',
                                'Abandonment' => 'Abandonment',
                            ])
                            ->columns([
                                'default' => 1,
                                'md' => 3,
                            ])
                            ->required()
                            ->columnSpanFull(),

                        Radio::make('disease_outcome')
                            ->label('Disease Outcome')
                            ->options([
                                'Stable Disease' => 'Stable Disease',
                                'Complete Remission' => 'Complete Remission',
                                'Partial Response' => 'Partial Response',
                                'Progressive/Refractory' => 'Progressive/Refractory',
                                'Recurrent Disease' => 'Recurrent Disease',
                                'Undetermined' => 'Undetermined',
                                'Death (Cancer-Related)' => 'Death (Cancer-Related)',
                                'Death (Treatment-Related)' => 'Death (Treatment-Related)',
                                'Death (Other Cause/Non-Cancer Related)' => 'Death (Other Cause/Non-Cancer Related)',
                                'Death (Pending Evaluation)' => 'Death (Pending Evaluation)',
                            ])
                            ->columns([
                                'default' => 1,
                                'md' => 2,
                            ])
                            ->live()
                            ->columnSpanFull(),

                        DatePicker::make('disease_outcome_date')
                            ->label('Date of Disease Outcome')
                            ->native(false)
                            ->displayFormat('Y-m-d'),

                        Grid::make([
                            'default' => 1,
                            'md' => 2,
                        ])
                            ->schema([
                                Textarea::make('immediate_cause_of_death')
                                    ->label('Immediate Cause')
                                    ->rows(2),

                                Textarea::make('antecedent_cause_of_death')
                                    ->label('Antecedent Cause')
                                    ->rows(2),

                                Textarea::make('underlying_cause_of_death')
                                    ->label('Underlying Cause')
                                    ->rows(2),

                                Textarea::make('other_significant_condition_related_to_death')
                                    ->label('Other Significant Condition Directly Related to Death')
                                    ->rows(2),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ]),

                Section::make('Treatment Plan')
                    ->columnSpanFull()
                    ->schema([
                        CheckboxList::make('treatment_plan')
                            ->label('Treatment Plan')
                            ->options([
                                'Continue Current Management' => 'Continue Current Management',
                                'New Chemotherapy Regimen' => 'New Chemotherapy Regimen',
                                'Refer to Surgery' => 'Refer to Surgery',
                                'Refer to Radiation Oncology' => 'Refer to Radiation Oncology',
                                'Refer to Theranostics/Nuclear Medicine' => 'Refer to Theranostics/Nuclear Medicine',
                                'Refer to Supportive/Palliative Care Team' => 'Refer to Supportive/Palliative Care Team',
                                'Transfer to Adult Care' => 'Transfer to Adult Care',
                                'Not Applicable' => 'Not Applicable',
                                'Others, specify' => 'Others, specify',
                            ])
                            ->columns([
                                'default' => 1,
                                'md' => 2,
                            ])
                            ->live()
                            ->bulkToggleable()
                            ->columnSpanFull(),

                        Textarea::make('treatment_plan_others')
                            ->label('Others, specify')
                            ->rows(2)
                            ->visible(fn (Get $get): bool => in_array('Others, specify', $get('treatment_plan') ?? [], true))
                            ->columnSpanFull(),

                        CheckboxList::make('change_in_treatment_plan_procedures')
                            ->label('Change in Treatment Plan - Procedure / Therapy')
                            ->options([
                                'Surgery' => 'Surgery',
                                'Anti-cancer drug therapy' => 'Anti-cancer drug therapy',
                                'Radiotherapy' => 'Radiotherapy',
                                'Theranostics' => 'Theranostics',
                                'Other Treatments' => 'Other Treatments',
                            ])
                            ->columns([
                                'default' => 1,
                                'md' => 2,
                            ])
                            ->live()
                            ->columnSpanFull(),

                        Grid::make([
                            'default' => 1,
                            'md' => 2,
                        ])
                            ->schema([
                                Textarea::make('change_in_treatment_plan_reasons.surgery')
                                    ->label('Reason for Change - Surgery')
                                    ->rows(2)
                                    ->visible(fn (Get $get): bool => in_array('Surgery', $get('change_in_treatment_plan_procedures') ?? [], true)),

                                Textarea::make('change_in_treatment_plan_reasons.anti_cancer_drug_therapy')
                                    ->label('Reason for Change - Anti-cancer drug therapy')
                                    ->rows(2)
                                    ->visible(fn (Get $get): bool => in_array('Anti-cancer drug therapy', $get('change_in_treatment_plan_procedures') ?? [], true)),

                                Textarea::make('change_in_treatment_plan_reasons.radiotherapy')
                                    ->label('Reason for Change - Radiotherapy')
                                    ->rows(2)
                                    ->visible(fn (Get $get): bool => in_array('Radiotherapy', $get('change_in_treatment_plan_procedures') ?? [], true)),

                                Textarea::make('change_in_treatment_plan_reasons.theranostics')
                                    ->label('Reason for Change - Theranostics')
                                    ->rows(2)
                                    ->visible(fn (Get $get): bool => in_array('Theranostics', $get('change_in_treatment_plan_procedures') ?? [], true)),

                                Textarea::make('change_in_treatment_plan_reasons.other_treatments')
                                    ->label('Reason for Change - Other Treatments')
                                    ->rows(2)
                                    ->visible(fn (Get $get): bool => in_array('Other Treatments', $get('change_in_treatment_plan_procedures') ?? [], true)),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ]),

                Section::make('Financial Support Mechanisms')
                    ->columnSpanFull()
                    ->schema([
                        Radio::make('availed_financial_support')
                            ->label('Did patient avail of any financial support mechanisms?')
                            ->options([
                                1 => 'Yes',
                                0 => 'No',
                            ])
                            ->inline()
                            ->default(0)
                            ->live()
                            ->afterStateHydrated(fn (Radio $component, $state) => $component->state((int) $state)),

                        CheckboxList::make('financial_support_mechanisms')
                            ->label('Financial Support Mechanisms')
                            ->helperText('Can be multiple.')
                            ->options([
                                'Discounts under Law' => 'Discounts under Law e.g. Senior Citizen, PWD',
                                'PhilHealth' => 'PhilHealth',
                                'HMO' => 'Health Maintenance Organization (HMO)',
                                'PCSO' => 'Philippine Charity Sweepstakes Office (PCSO)',
                                'AICS' => 'Assistance to Individuals in Crisis Situations (AICS)',
                                'CAF' => 'Cancer Assistance Fund (CAF)',
                                'MAIFIP' => 'Medical Assistance for Indigent and Financially-Incapacitated Patients (MAIFIP)',
                                'Hospital Assistance Funds' => 'Hospital Assistance Funds',
                                'CSPMAP' => 'Cancer and Supportive-Palliative Medicines Access Program (CSPMAP)',
                                'PAGCOR' => 'Philippine Amusement and Gaming Corporation (PAGCOR)',
                                'NGO / Civil Society Org.' => 'Non-Government Org. / Civil Society Org.',
                                'Clinical Trial' => 'Clinical Trial',
                                'Charitable Institutions' => 'Charitable Institutions',
                                'Private Sector Assistance Program' => 'Private Sector Assistance Program',
                                'None' => 'None',
                                'Others, specify' => 'Others, specify',
                            ])
                            ->columns([
                                'default' => 1,
                                'md' => 2,
                            ])
                            ->visible(fn (Get $get): bool => (bool) $get('availed_financial_support'))
                            ->live()
                            ->bulkToggleable()
                            ->columnSpanFull(),

                        Textarea::make('financial_support_others')
                            ->label('Others, specify')
                            ->rows(2)
                            ->visible(fn (Get $get): bool => in_array('Others, specify', $get('financial_support_mechanisms') ?? [], true))
                            ->columnSpanFull(),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ]),
            ]);
    }

    /**
     * @return array<int, mixed>
     */
    private static function surgerySchema(): array
    {
        return [
            Repeater::make('surgeries')
                ->label(false)
                ->addActionLabel('Add another surgery')
                ->defaultItems(1)
                ->minItems(1)
                ->columns(4)
                ->schema([
                    DatePicker::make('surgery_date')
                        ->label('Surgery Date')
                        ->native(false)
                        ->displayFormat('Y-m-d')
                        ->required()
                        ->columnSpan([
                            'default' => 1,
                            'md' => 2,
                        ]),

                    TextInput::make('surgery_rvs_code')
                        ->label('Surgery Type / RVS Code')
                        ->maxLength(100)
                        ->columnSpan([
                            'default' => 1,
                            'md' => 2,
                        ]),

                    TextInput::make('surgery_description')
                        ->label('Surgery Description')
                        ->placeholder('Example: Enucleation of the Right Eye')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan([
                            'default' => 1,
                            'md' => 2,
                        ]),

                    Select::make('surgery_goal')
                        ->label('Goal of Surgery')
                        ->options([
                            'Definitive' => 'Definitive',
                            'Debulking' => 'Debulking',
                            'Diagnostic' => 'Diagnostic',
                            'Reconstructive' => 'Reconstructive',
                            'Palliative' => 'Palliative',
                            'Others' => 'Others',
                        ])
                        ->native(false)
                        ->searchable()
                        ->required()
                        ->columnSpan([
                            'default' => 1,
                            'md' => 2,
                        ]),
                ]),

        ];
    }

    private static function antiCancerDrugTherapySchema(): array
    {
        return [

            Repeater::make('anti_cancer_drug_therapies')
                ->label(false)
                ->addActionLabel('Add another therapy')
                ->defaultItems(1)
                ->minItems(1)
                ->columns(2)
                ->schema([
                    DatePicker::make('date_started')
                        ->label('Date Started')
                        ->native(false)
                        ->displayFormat('Y-m-d')
                        ->required()
                        ->columnSpan([
                            'default' => 1,
                            'md' => 1,
                        ]),

                    DatePicker::make('date_last_anti_cancer_drug_therapy')
                        ->label('Date of Last Anti-Cancer Drug Therapy')
                        ->native(false)
                        ->displayFormat('Y-m-d')
                        ->required()
                        ->columnSpan([
                            'default' => 1,
                            'md' => 1,
                        ]),

                    Textarea::make('drugs_given')
                        ->label('Drugs Given')
                        ->placeholder('Example: Methotrexate, Dexamethasone, Peg Asparaginase, Vincristine')
                        ->rows(2)
                        ->required()
                        ->columnSpan([
                            'default' => 1,
                            'md' => 1,
                        ]),

                    Select::make('drug_types')
                        ->label('Drug Type/s')
                        ->multiple()
                        ->options([
                            'Cytotoxic' => 'Cytotoxic',
                            'Hormonal' => 'Hormonal',
                            'Immunotherapy' => 'Immunotherapy',
                            'Targeted Therapy' => 'Targeted Therapy',
                            'Others' => 'Others',
                        ])
                        ->native(false)
                        ->searchable()
                        ->required()
                        ->live()
                        ->columnSpan([
                            'default' => 1,
                            'md' => 1,
                        ]),

                    TextInput::make('drug_type_other')
                        ->label('Drug Type Other Specify')
                        ->maxLength(255)
                        ->visible(fn (Get $get): bool => in_array('Others', $get('drug_types') ?? []))
                        ->required(fn (Get $get): bool => in_array('Others', $get('drug_types') ?? []))
                        ->columnSpan([
                            'default' => 1,
                            'md' => 1,
                        ]),

                    Select::make('treatment_phase')
                        ->label('Treatment Phase')
                        ->options([
                            'Induction' => 'Induction',
                            'Consolidation' => 'Consolidation',
                            'Maintenance' => 'Maintenance',
                            'Neoadjuvant' => 'Neoadjuvant',
                            'Adjuvant' => 'Adjuvant',
                            'Palliative' => 'Palliative',
                            'Others' => 'Others',
                        ])
                        ->native(false)
                        ->searchable()
                        ->required()
                        ->live()
                        ->columnSpan([
                            'default' => 1,
                            'md' => 1,
                        ]),

                    TextInput::make('treatment_phase_other')
                        ->label('Treatment Phase Other Specify')
                        ->maxLength(255)
                        ->visible(fn (Get $get): bool => $get('treatment_phase') === 'Others')
                        ->required(fn (Get $get): bool => $get('treatment_phase') === 'Others')
                        ->columnSpan([
                            'default' => 1,
                            'md' => 1,
                        ]),

                    TextInput::make('cycle_no')
                        ->label('Cycle No')
                        ->numeric()
                        ->minValue(1)
                        ->required()
                        ->columnSpan([
                            'default' => 1,
                            'md' => 1,
                        ]),

                    Select::make('goal_of_anti_cancer_drug_therapy')
                        ->label('Goal of Anti-Cancer Drug Therapy')
                        ->options([
                            'Curative' => 'Curative',
                            'Palliative' => 'Palliative',
                            'Control' => 'Control',
                            'Supportive' => 'Supportive',
                            'Others' => 'Others',
                        ])
                        ->native(false)
                        ->searchable()
                        ->required()
                        ->live()
                        ->columnSpan([
                            'default' => 1,
                            'md' => 1,
                        ]),

                    TextInput::make('goal_other')
                        ->label('Goal Other Specify')
                        ->maxLength(255)
                        ->visible(fn (Get $get): bool => $get('goal_of_anti_cancer_drug_therapy') === 'Others')
                        ->required(fn (Get $get): bool => $get('goal_of_anti_cancer_drug_therapy') === 'Others')
                        ->columnSpan([
                            'default' => 1,
                            'md' => 1,
                        ]),
                ]),
        ];
    }

    private static function radiotherapyRepeaterSchema(): array
    {
        return [

            Repeater::make('radiotherapies')
                ->label(false)
                ->addActionLabel('Add another radiotherapy')
                ->defaultItems(1)
                ->minItems(1)
                ->columns([
                    'default' => 1,
                    'lg' => 4,
                ])
                ->schema([
                    DatePicker::make('date_started')
                        ->label('Date Started')
                        ->native(false)
                        ->displayFormat('Y-m-d')
                        ->required()
                        ->columnSpan([
                            'default' => 1,
                            'lg' => 2,
                        ]),

                    DatePicker::make('date_ended')
                        ->label('Date Ended')
                        ->native(false)
                        ->displayFormat('Y-m-d')
                        ->required()
                        ->columnSpan([
                            'default' => 1,
                            'lg' => 2,
                        ]),

                    TextInput::make('total_planned_dose')
                        ->label('Total Planned Dose')
                        ->numeric()
                        ->minValue(0)
                        ->step(0.01)
                        ->suffix('Gy')
                        ->required()
                        ->columnSpan([
                            'default' => 1,
                            'lg' => 2,
                        ]),

                    TextInput::make('total_delivered_fraction')
                        ->label('Total Delivered Fraction')
                        ->numeric()
                        ->minValue(0)
                        ->step(1)
                        ->required()
                        ->columnSpan([
                            'default' => 1,
                            'lg' => 2,
                        ]),

                    TextInput::make('dose_per_fraction')
                        ->label('Dose Per Fraction')
                        ->numeric()
                        ->minValue(0)
                        ->step(0.01)
                        ->suffix('Gy')
                        ->required()
                        ->columnSpan([
                            'default' => 1,
                            'lg' => 2,
                        ]),

                    TextInput::make('total_number_of_days')
                        ->label('Total Number of Days')
                        ->numeric()
                        ->minValue(0)
                        ->step(1)
                        ->required()
                        ->columnSpan([
                            'default' => 1,
                            'lg' => 2,
                        ]),

                    Select::make('radiotherapy_type')
                        ->label('Type of Radiotherapy')
                        ->options([
                            'External beam radiotherapy' => 'External beam radiotherapy',
                            'Brachytherapy' => 'Brachytherapy',
                            'Systemic radiotherapy' => 'Systemic radiotherapy',
                            'Others' => 'Others',
                        ])
                        ->native(false)
                        ->searchable()
                        ->required()
                        ->live()
                        ->columnSpan([
                            'default' => 1,
                            'lg' => 2,
                        ]),

                    TextInput::make('radiotherapy_type_specifics')
                        ->label('Type Specifics')
                        ->maxLength(255)
                        ->required(fn (Get $get): bool => $get('radiotherapy_type') === 'Others')
                        ->columnSpan([
                            'default' => 1,
                            'lg' => 2,
                        ]),

                    Select::make('target_site')
                        ->label('Target Site of Radiotherapy')
                        ->options([
                            'Primary site' => 'Primary site',
                            'Regional lymph node' => 'Regional lymph node',
                            'Distant metastasis' => 'Distant metastasis',
                            'Central nervous system' => 'Central nervous system',
                            'Bone' => 'Bone',
                            'Others' => 'Others',
                        ])
                        ->native(false)
                        ->searchable()
                        ->required()
                        ->live()
                        ->columnSpan([
                            'default' => 1,
                            'lg' => 2,
                        ]),

                    TextInput::make('target_site_specifics')
                        ->label('Target Site Specifics')
                        ->maxLength(255)
                        ->required(fn (Get $get): bool => $get('target_site') === 'Others')
                        ->columnSpan([
                            'default' => 1,
                            'lg' => 2,
                        ]),

                    Select::make('goal_of_radiotherapy')
                        ->label('Goal of Radiotherapy')
                        ->options([
                            'Curative' => 'Curative',
                            'Palliative' => 'Palliative',
                            'Others' => 'Others',
                        ])
                        ->native(false)
                        ->searchable()
                        ->required()
                        ->live()
                        ->columnSpan([
                            'default' => 1,
                            'lg' => 2,
                        ]),

                    TextInput::make('goal_other')
                        ->label('Goal Other Specify')
                        ->maxLength(255)
                        ->visible(fn (Get $get): bool => $get('goal_of_radiotherapy') === 'Others')
                        ->required(fn (Get $get): bool => $get('goal_of_radiotherapy') === 'Others')
                        ->columnSpan([
                            'default' => 1,
                            'lg' => 2,
                        ]),
                ]),
        ];
    }

    private static function theranosticsRepeaterSchema(): array
    {
        return [
            Repeater::make('theranostics')
                ->label(false)
                ->addActionLabel('Add Anti-Cancer Drug Therapy')
                ->defaultItems(1)
                ->minItems(1)
                ->columns(2)
                ->schema([
                    DatePicker::make('date_started')
                        ->label('Date Started')
                        ->native(false)
                        ->displayFormat('Y-m-d')
                        ->required()
                        ->columnSpan([
                            'default' => 2,
                            'md' => 1,
                        ]),

                    DatePicker::make('date_last_anti_cancer_drug_therapy')
                        ->label('Date of Last Anti-Cancer Drug Therapy')
                        ->native(false)
                        ->displayFormat('Y-m-d')
                        ->required()
                        ->columnSpan([
                            'default' => 2,
                            'md' => 1,
                        ]),

                    Textarea::make('drugs_given')
                        ->label('Drugs Given')
                        ->placeholder('Example: Methotrexate, Dexamethasone, Peg Asparaginase, Vincristine, Idarubicin')
                        ->rows(2)
                        ->required()
                        ->columnSpan([
                            'default' => 2,
                            'md' => 1,
                        ]),

                    Select::make('drug_types')
                        ->label('Drug Type/s')
                        ->multiple()
                        ->options([
                            'Cytotoxic' => 'Cytotoxic',
                            'Hormonal' => 'Hormonal',
                            'Immunotherapy' => 'Immunotherapy',
                            'Targeted Therapy' => 'Targeted Therapy',
                            'Others' => 'Others',
                        ])
                        ->native(false)
                        ->searchable()
                        ->required()
                        ->live()
                        ->columnSpan([
                            'default' => 2,
                            'md' => 1,
                        ]),

                    TextInput::make('drug_type_other')
                        ->label('Drug Type Other Specify')
                        ->maxLength(255)
                        ->visible(fn (Get $get): bool => in_array('Others', $get('drug_types') ?? []))
                        ->required(fn (Get $get): bool => in_array('Others', $get('drug_types') ?? []))
                        ->columnSpan([
                            'default' => 2,
                            'md' => 1,
                        ]),

                    Select::make('treatment_phase')
                        ->label('Treatment Phase')
                        ->options([
                            'Induction' => 'Induction',
                            'Consolidation' => 'Consolidation',
                            'Maintenance' => 'Maintenance',
                            'Neoadjuvant' => 'Neoadjuvant',
                            'Adjuvant' => 'Adjuvant',
                            'Palliative' => 'Palliative',
                            'Others' => 'Others',
                        ])
                        ->native(false)
                        ->searchable()
                        ->required()
                        ->live()
                        ->columnSpan([
                            'default' => 2,
                            'md' => 1,
                        ]),

                    TextInput::make('treatment_phase_other')
                        ->label('Treatment Phase Other Specify')
                        ->maxLength(255)
                        ->visible(fn (Get $get): bool => $get('treatment_phase') === 'Others')
                        ->required(fn (Get $get): bool => $get('treatment_phase') === 'Others')
                        ->columnSpan([
                            'default' => 2,
                            'md' => 1,
                        ]),

                    TextInput::make('cycle_no')
                        ->label('Cycle No')
                        ->numeric()
                        ->minValue(1)
                        ->required()
                        ->columnSpan([
                            'default' => 2,
                            'md' => 1,
                        ]),

                    Select::make('goal_of_anti_cancer_drug_therapy')
                        ->label('Goal of Anti-Cancer Drug Therapy')
                        ->options([
                            'Curative' => 'Curative',
                            'Palliative' => 'Palliative',
                            'Control' => 'Control',
                            'Supportive' => 'Supportive',
                            'Others' => 'Others',
                        ])
                        ->native(false)
                        ->searchable()
                        ->required()
                        ->live()
                        ->columnSpan([
                            'default' => 2,
                            'md' => 1,
                        ]),

                    TextInput::make('goal_other')
                        ->label('Goal Other Specify')
                        ->maxLength(255)
                        ->visible(fn (Get $get): bool => $get('goal_of_anti_cancer_drug_therapy') === 'Others')
                        ->required(fn (Get $get): bool => $get('goal_of_anti_cancer_drug_therapy') === 'Others')
                        ->columnSpan([
                            'default' => 2,
                            'md' => 1,
                        ]),
                ]),
        ];
    }

    private static function palliativeCareSchema(): array
    {
        return [
            Repeater::make('palliative_cares')
                ->label(false)
                ->addActionLabel('Add Palliative Care')
                ->defaultItems(1)
                ->minItems(1)
                ->columns(2)
                ->schema([
                    DatePicker::make('date_started')
                        ->label('Date Started')
                        ->native(false)
                        ->displayFormat('Y-m-d')
                        ->required()
                        ->columnSpan([
                            'default' => 1,
                            'md' => 1,
                        ]),

                    DatePicker::make('date_last_palliative_care')
                        ->label('Date of Last Palliative Care')
                        ->native(false)
                        ->displayFormat('Y-m-d')
                        ->required()
                        ->columnSpan([
                            'default' => 1,
                            'md' => 1,
                        ]),

                    Select::make('reasons')
                        ->label('Reason/s')
                        ->helperText('Can be multiple.')
                        ->multiple()
                        ->options([
                            'Pain Control' => 'Pain Control',
                            'Symptom Control' => 'Symptom Control',
                            'Psychosocial Support' => 'Psychosocial Support',
                            'End-of-Life Care' => 'End-of-Life Care',
                            'Nutritional Support' => 'Nutritional Support',
                            'Spiritual Care' => 'Spiritual Care',
                            'Others' => 'Others',
                        ])
                        ->native(false)
                        ->searchable()
                        ->required()
                        ->live()
                        ->columnSpan([
                            'default' => 1,
                            'md' => 1,
                        ]),

                    TextInput::make('reason_specifics')
                        ->label('Specifics / Others, Specify')
                        ->maxLength(255)
                        ->visible(fn (Get $get): bool => in_array('Others', $get('reasons') ?? []))
                        ->required(fn (Get $get): bool => in_array('Others', $get('reasons') ?? []))
                        ->columnSpan([
                            'default' => 1,
                            'md' => 1,
                        ]),

                    Select::make('type_of_care_integration')
                        ->label('Type of Care Integration')
                        ->options([
                            'Concurrent-Curative' => 'Concurrent-Curative',
                            'Concurrent-Palliative' => 'Concurrent-Palliative',
                            'Palliative Only' => 'Palliative Only',
                            'End-of-Life Care' => 'End-of-Life Care',
                            'Others' => 'Others',
                        ])
                        ->native(false)
                        ->searchable()
                        ->required()
                        ->live()
                        ->columnSpan([
                            'default' => 1,
                            'md' => 1,
                        ]),

                    TextInput::make('type_of_care_integration_other')
                        ->label('Type of Care Integration Other Specify')
                        ->maxLength(255)
                        ->visible(fn (Get $get): bool => $get('type_of_care_integration') === 'Others')
                        ->required(fn (Get $get): bool => $get('type_of_care_integration') === 'Others')
                        ->columnSpan([
                            'default' => 1,
                            'md' => 1,
                        ]),

                    Select::make('goal_of_care')
                        ->label('Goal of Care')
                        ->options([
                            'Curative' => 'Curative',
                            'Palliative' => 'Palliative',
                            'Supportive' => 'Supportive',
                            'Comfort Care' => 'Comfort Care',
                            'Others' => 'Others',
                        ])
                        ->native(false)
                        ->searchable()
                        ->required()
                        ->live()
                        ->columnSpan([
                            'default' => 1,
                            'md' => 1,
                        ]),

                    TextInput::make('goal_of_care_other')
                        ->label('Goal of Care Other Specify')
                        ->maxLength(255)
                        ->visible(fn (Get $get): bool => $get('goal_of_care') === 'Others')
                        ->required(fn (Get $get): bool => $get('goal_of_care') === 'Others')
                        ->columnSpan([
                            'default' => 1,
                            'md' => 1,
                        ]),
                ]),
        ];
    }

    private static function otherTreatmentsSchema(): array
    {
        return [
            Repeater::make('other_cancer_directed_therapies')
                ->label(false)
                ->addActionLabel('Add Other Cancer-Directed Therapy')
                ->defaultItems(1)
                ->minItems(1)
                ->columns(2)
                ->schema([
                    DatePicker::make('date_of_therapy')
                        ->label('Date of Therapy')
                        ->native(false)
                        ->displayFormat('Y-m-d')
                        ->required()
                        ->columnSpan([
                            'default' => 1,
                            'md' => 1,
                        ]),

                    Select::make('type_of_cancer_directed_therapy')
                        ->label('Type of Cancer-Directed Therapy')
                        ->options([
                            'Targeted Therapy' => 'Targeted Therapy',
                            'Immunotherapy' => 'Immunotherapy',
                            'Hormonal Therapy' => 'Hormonal Therapy',
                            'Stem Cell Transplant' => 'Stem Cell Transplant',
                            'Gene Therapy' => 'Gene Therapy',
                            'Ablation Therapy' => 'Ablation Therapy',
                            'Photodynamic Therapy' => 'Photodynamic Therapy',
                            'Others' => 'Others',
                        ])
                        ->native(false)
                        ->searchable()
                        ->required()
                        ->live()
                        ->columnSpan([
                            'default' => 1,
                            'md' => 1,
                        ]),

                    TextInput::make('type_of_cancer_directed_therapy_other')
                        ->label('Other Cancer-Directed Therapy, Specify')
                        ->maxLength(255)
                        ->visible(fn (Get $get): bool => $get('type_of_cancer_directed_therapy') === 'Others')
                        ->required(fn (Get $get): bool => $get('type_of_cancer_directed_therapy') === 'Others')
                        ->columnSpan([
                            'default' => 1,
                            'md' => 1,
                        ]),

                    Select::make('goal_of_cancer_directed_therapy')
                        ->label('Goal of Cancer-Directed Therapy')
                        ->options([
                            'Curative' => 'Curative',
                            'Palliative' => 'Palliative',
                            'Control' => 'Control',
                            'Supportive' => 'Supportive',
                            'Others' => 'Others',
                        ])
                        ->native(false)
                        ->searchable()
                        ->required()
                        ->live()
                        ->columnSpan([
                            'default' => 1,
                            'md' => 1,
                        ]),

                    TextInput::make('goal_of_cancer_directed_therapy_other')
                        ->label('Goal Other Specify')
                        ->maxLength(255)
                        ->visible(fn (Get $get): bool => $get('goal_of_cancer_directed_therapy') === 'Others')
                        ->required(fn (Get $get): bool => $get('goal_of_cancer_directed_therapy') === 'Others')
                        ->columnSpan([
                            'default' => 1,
                            'md' => 1,
                        ]),
                ]),
        ];
    }

    private static function surgeryTable(): array
    {
        return [
            Table::make('surgeries')
                ->label('Surgeries')
                ->columns([
                    Tables\Columns\TextColumn::make('surgery_date')
                        ->label('Surgery Date')
                        ->date('Y-m-d'),

                    Tables\Columns\TextColumn::make('surgery_rvs_code')
                        ->label('Surgery Type / RVS Code'),

                    Tables\Columns\TextColumn::make('surgery_description')
                        ->label('Surgery Description'),

                    Tables\Columns\TextColumn::make('surgery_goal')
                        ->label('Goal of Surgery'),
                ])
                ->filters([
                    //
                ])
                ->headerActions([
                    Tables\Actions\CreateAction::make(),
                ])
                ->rowActions([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                ->bulkActions([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
        ];
    }
}
