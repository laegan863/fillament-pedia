<?php

namespace App\Filament\Pages;

use App\Models\CancerDiagnose;
use App\Models\PatientFollowUpForm;
use App\Models\PatientFollowUpFormAntiCancerDrugTherapy;
use App\Models\PatientFollowUpFormOtherCancerDirectedTherapy;
use App\Models\PatientFollowUpFormPalliativeCare;
use App\Models\PatientFollowUpFormRadiotherapy;
use App\Models\PatientFollowUpFormSurgery;
use App\Models\PatientFollowUpFormTheranostic;
use App\Models\SpecificClassification;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Carbon;
use Torgodly\Html2Media\Actions\Html2MediaAction;
use UnitEnum;

class Reports extends Page
{
    protected string $view = 'filament.pages.reports';

    public ?array $form1AReport = null;

    public ?array $form1BReport = null;

    public ?array $form1CReport = null;

    public ?array $form2AReport = null;

    public ?array $form2BReport = null;

    public ?array $form2CReport = null;

    protected array $form1AStages = [
        'Stage 0',
        'Stage I',
        'Stage II',
        'Stage III',
        'Stage IV',
        'Stage V',
        'Not Applicable',
        'Unknown',
    ];

    /**
     * @var array<int, array{label: string, patterns: array<int, string>}>
     */
    protected array $form1BCategories = [
        [
            'label' => 'Acute Lymphoblastic Leukemia',
            'patterns' => ['acute lymphoblastic', 'lymphoid leukemia', 'precursor cell leukemia'],
        ],
        [
            'label' => 'Burkitt Lymphoma',
            'patterns' => ['burkitt'],
        ],
        [
            'label' => 'Hodgkin Lymphoma',
            'patterns' => ['hodgkin'],
        ],
        [
            'label' => 'Low Grade Glioma',
            'patterns' => ['low grade glioma', 'astrocytoma', 'pilocytic'],
        ],
        [
            'label' => 'Retinoblastoma',
            'patterns' => ['retinoblastoma'],
        ],
        [
            'label' => 'Wilms Tumor',
            'patterns' => ['wilms', 'nephroblastoma'],
        ],
    ];

    /**
     * @var array<int, string>
     */
    protected array $form1CStatuses = [
        'Newly diagnosed',
        'New case to center, no treatment',
        'New case to center, received treatment elsewhere',
        'Old case to center, relapse/refractory',
        'Old case to center, returning (previous abandonment)',
        'Old case, secondary malignancy',
    ];

    /**
     * @var array<int, string>
     */
    protected array $form2ATreatments = [
        'Surgery',
        'Anti-Cancer Drug Therapy: Cytotoxic',
        'Anti-Cancer Drug Therapy: Hormonal',
        'Anti-Cancer Drug Therapy: Immunotherapy',
        'Anti-Cancer Drug Therapy: Targeted therapy',
        'Radiotherapy: 2D Conventional',
        'Radiotherapy: 3DCRT',
        'Radiotherapy: Brachytherapy',
        'Radiotherapy: Electron Therapy',
        'Radiotherapy: IMRT/VMAT/Helical',
        'Radiotherapy: IORT',
        'Radiotherapy: Heavy Particles',
        'Radiotherapy: Proton Therapy',
        'Radiotherapy: Stereotactic Radiosurgery / Stereotactic Radiotherapy',
        'Radiotherapy: Others',
        'Theranostics: Radioactive Iodine Therapy (RAI)',
        'Theranostics: Peptide Receptor Radionuclide Therapy (PRRT)',
        'Theranostics: Selective Internal Radiation Therapy (SIRT)',
        'Theranostics: Others',
        'Palliative Care: Concurrent-Curative',
        'Palliative Care: Concurrent-Transition',
        'Palliative Care: Control-focused',
        'Other Cancer Directed Therapies: Transplant',
        'Other Cancer Directed Therapies: Chemoembolization',
        'Other Cancer Directed Therapies: Others',
        'Non-receipt of Treatment',
    ];

    /**
     * @var array<int, string>
     */
    protected array $form2BOutcomes = [
        'Stable Disease',
        'Complete Remission',
        'Partial Response',
        'Disease Progression',
        'Recurrent Disease',
        'Death (Cancer related)',
        'Death (Treatment related)',
        'Death (Other Cause/Non-Cancer Related)',
        'Undetermined',
    ];

    /**
     * @var array<int, string>
     */
    protected array $form2CStatuses = [
        'Ongoing',
        'Completed',
        'Stopped/Interrupted',
        'Undetermined',
        'Not Initiated',
        'Abandonment',
    ];

    protected static bool $shouldRegisterNavigation = true;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Reports';

    protected static UnitEnum|string|null $navigationGroup = 'Main';

    protected static ?int $navigationSort = 10;

    protected static ?string $title = 'Reports';

    protected static ?string $slug = 'reports';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('View:Reports') ?? false;
    }

    protected function generateReportAction(): Action
    {
        return Action::make('generateReport')
            ->label('Generate')
            ->button()
            ->size('sm')
            ->color('primary')
            ->modalHeading(fn (array $arguments): string => 'Generate '.($arguments['form'] ?? 'Report'))
            ->modalDescription(fn (array $arguments): string => $arguments['title'] ?? '')
            ->modalSubmitActionLabel('Generate Report')
            // ->visible(fn (): bool => auth()->user()?->can('Generate:Reports') ?? false)
            ->schema([
                TextInput::make('facility_name')
                    ->label('Health Facility Name')
                    ->required(),
                TextInput::make('facility_registration_no')
                    ->label('Health Facility Registration No.')
                    ->required(),
                DatePicker::make('from_date')
                    ->label('Date From')
                    ->required()
                    ->minDate(now()->subYears(10)->startOfYear())
                    ->maxDate(now()->endOfYear()),

                DatePicker::make('to_date')
                    ->label('Date To')
                    ->required()
                    ->minDate(now()->subYears(10)->startOfYear())
                    ->maxDate(now()->endOfYear())
                    ->afterOrEqual('from_date'),
            ])
            ->action(function (array $data, array $arguments): void {
                $title = '';
                $body = 'Preview modal is ready. You can now export it as PDF.';
                $dispatch = '';

                switch (($arguments['code'] ?? '')) {
                    case '1A':
                        $this->form1AReport = $this->buildForm1AReport($data);
                        $this->form1BReport = null;
                        $this->form1CReport = null;
                        $this->form2AReport = null;
                        $this->form2BReport = null;
                        $this->form2CReport = null;
                        $title = 'Reporting Form 1A generated';
                        $dispatch = 'form-1a-preview-modal';
                        break;

                    case '1B':
                        $this->form1BReport = $this->buildForm1BReport($data);
                        $this->form1AReport = null;
                        $this->form1CReport = null;
                        $this->form2AReport = null;
                        $this->form2BReport = null;
                        $this->form2CReport = null;
                        $title = 'Reporting Form 1B generated';
                        $dispatch = 'form-1b-preview-modal';
                        break;

                    case '1C':
                        $this->form1CReport = $this->buildForm1CReport($data);
                        $this->form1AReport = null;
                        $this->form1BReport = null;
                        $this->form2AReport = null;
                        $this->form2BReport = null;
                        $this->form2CReport = null;
                        $title = 'Reporting Form 1C generated';
                        $dispatch = 'form-1c-preview-modal';
                        break;

                    case '2A':
                        $this->form2AReport = $this->buildForm2AReport($data);
                        $this->form1AReport = null;
                        $this->form1BReport = null;
                        $this->form1CReport = null;
                        $this->form2BReport = null;
                        $this->form2CReport = null;
                        $title = 'Reporting Form 2A generated';
                        $dispatch = 'form-2a-preview-modal';
                        break;

                    case '2B':
                        $this->form2BReport = $this->buildForm2BReport($data);
                        $this->form1AReport = null;
                        $this->form1BReport = null;
                        $this->form1CReport = null;
                        $this->form2AReport = null;
                        $this->form2CReport = null;
                        $title = 'Reporting Form 2B generated';
                        $dispatch = 'form-2b-preview-modal';
                        break;

                    case '2C':
                        $this->form2CReport = $this->buildForm2CReport($data);
                        $this->form1AReport = null;
                        $this->form1BReport = null;
                        $this->form1CReport = null;
                        $this->form2AReport = null;
                        $this->form2BReport = null;
                        $title = 'Reporting Form 2C generated';
                        $dispatch = 'form-2c-preview-modal';
                        break;

                    default:
                        Notification::make()
                            ->title('Not available yet')
                            ->body('This reporting form is not implemented yet.')
                            ->warning()
                            ->send();

                        return;
                }

                Notification::make()
                    ->title($title ?? '')
                    ->body($body ?? '')
                    ->success()
                    ->send();

                $this->dispatch('open-modal', id: $dispatch);
            });
    }

    protected function exportForm1APdfAction(): Html2MediaAction
    {
        return Html2MediaAction::make('exportForm1APdf')
            ->label('Export Form 1A PDF')
            ->icon(Heroicon::OutlinedArrowDownTray)
            ->color('success')
            ->savePdf()
            ->print(false)
            ->orientation('landscape')
            ->filename(fn (): string => $this->buildForm1AFilename())
            ->content(fn () => view('filament.pages.partials.form-1a-report', [
                'report' => $this->form1AReport,
                'forPdf' => true,
            ]));
    }

    protected function exportForm1BPdfAction(): Html2MediaAction
    {
        return Html2MediaAction::make('exportForm1BPdf')
            ->label('Export Form 1B PDF')
            ->icon(Heroicon::OutlinedArrowDownTray)
            ->color('success')
            ->savePdf()
            ->print(false)
            ->orientation('portrait')
            ->filename(fn (): string => $this->buildForm1BFilename())
            ->content(fn () => view('filament.pages.partials.form-1b-report', [
                'report' => $this->form1BReport,
                'forPdf' => true,
            ]));
    }

    protected function exportForm1CPdfAction(): Html2MediaAction
    {
        return Html2MediaAction::make('exportForm1CPdf')
            ->label('Export Form 1C PDF')
            ->icon(Heroicon::OutlinedArrowDownTray)
            ->color('success')
            ->savePdf()
            ->print(false)
            ->orientation('portrait')
            ->filename(fn (): string => $this->buildForm1CFilename())
            ->content(fn () => view('filament.pages.partials.form-1c-report', [
                'report' => $this->form1CReport,
                'forPdf' => true,
            ]));
    }

    protected function exportForm2APdfAction(): Html2MediaAction
    {
        return Html2MediaAction::make('exportForm2APdf')
            ->label('Export Form 2A PDF')
            ->icon(Heroicon::OutlinedArrowDownTray)
            ->color('success')
            ->savePdf()
            ->print(false)
            ->orientation('portrait')
            ->filename(fn (): string => $this->buildForm2AFilename())
            ->content(fn () => view('filament.pages.partials.form-2a-report', [
                'report' => $this->form2AReport,
                'forPdf' => true,
            ]));
    }

    protected function exportForm2BPdfAction(): Html2MediaAction
    {
        return Html2MediaAction::make('exportForm2BPdf')
            ->label('Export Form 2B PDF')
            ->icon(Heroicon::OutlinedArrowDownTray)
            ->color('success')
            ->savePdf()
            ->print(false)
            ->orientation('portrait')
            ->filename(fn (): string => $this->buildForm2BFilename())
            ->content(fn () => view('filament.pages.partials.form-2b-report', [
                'report' => $this->form2BReport,
                'forPdf' => true,
            ]));
    }

    protected function exportForm2CPdfAction(): Html2MediaAction
    {
        return Html2MediaAction::make('exportForm2CPdf')
            ->label('Export Form 2C PDF')
            ->icon(Heroicon::OutlinedArrowDownTray)
            ->color('success')
            ->savePdf()
            ->print(false)
            ->orientation('portrait')
            ->filename(fn (): string => $this->buildForm2CFilename())
            ->content(fn () => view('filament.pages.partials.form-2c-report', [
                'report' => $this->form2CReport,
                'forPdf' => true,
            ]));
    }

    protected function buildForm1AReport(array $data): array
    {
        $fromDate = Carbon::parse((string) $data['from_date'])->startOfDay();
        $toDate = Carbon::parse((string) $data['to_date'])->endOfDay();
        $facilityRegistrationNo = trim((string) $data['facility_registration_no']);

        $countRows = CancerDiagnose::query()
            ->selectRaw(
                'COALESCE(cancer_diagnoses.iccc_general_classification, ?) as general_name,
                NULLIF(cancer_diagnoses.iccc_parent_classification, ?) as parent_name,
                COALESCE(cancer_diagnoses.iccc_specific_classification, ?) as specific_name,
                COALESCE(cancer_diagnoses.clinical_stage, ?) as clinical_stage,
                LOWER(form_demographics.sex_at_birth) as sex_at_birth,
                COUNT(*) as total',
                ['Unclassified', '-----', 'Unspecified', 'Unknown']
            )
            ->join('form_demographics', 'form_demographics.id', '=', 'cancer_diagnoses.form_demographic_id')
            // ->where('cancer_diagnoses.health_facility_registration_no', $facilityRegistrationNo)
            // ->whereIn('cancer_diagnoses.current_status_of_cancer', [
            //     'Newly diagnosed',
            //     'New case to center, no treatment',
            // ])
            ->whereBetween('cancer_diagnoses.diagnosis_date', [$fromDate->toDateString(), $toDate->toDateString()])
            ->groupBy('general_name', 'parent_name', 'specific_name', 'clinical_stage', 'sex_at_birth')
            ->get();

        $counts = [];
        foreach ($countRows as $row) {
            $generalName = (string) $row->general_name;
            $parentName = $row->parent_name ? (string) $row->parent_name : null;
            $specificName = (string) $row->specific_name;

            $stage = (string) $row->clinical_stage;
            if (! in_array($stage, $this->form1AStages, true)) {
                $stage = 'Unknown';
            }

            $sex = (string) $row->sex_at_birth;
            if (! in_array($sex, ['male', 'female'], true)) {
                continue;
            }

            $counts[$generalName][$parentName ?? '__none__'][$specificName][$stage][$sex] = (int) $row->total;
        }

        $specificClassifications = SpecificClassification::query()
            ->with(['generalClassification:id,name', 'parentClassification:id,name'])
            ->orderBy('general_classification_id')
            ->orderBy('parent_classification_id')
            ->orderBy('name')
            ->get(['id', 'name', 'general_classification_id', 'parent_classification_id']);

        $groups = [];
        foreach ($specificClassifications as $specific) {
            $generalName = $specific->generalClassification?->name ?? 'Unclassified';
            $parentName = $specific->parentClassification?->name;

            if (! isset($groups[$generalName])) {
                $groups[$generalName] = [
                    'name' => $generalName,
                    'totals' => $this->blankTotals(),
                    'rows' => [],
                ];
            }

            $rowTotals = $this->blankTotals();
            foreach ($this->form1AStages as $stage) {
                $male = (int) ($counts[$generalName][$parentName ?? '__none__'][$specific->name][$stage]['male'] ?? 0);
                $female = (int) ($counts[$generalName][$parentName ?? '__none__'][$specific->name][$stage]['female'] ?? 0);
                $total = $male + $female;

                $rowTotals['stages'][$stage]['M'] = $male;
                $rowTotals['stages'][$stage]['F'] = $female;
                $rowTotals['stages'][$stage]['T'] = $total;
                $rowTotals['total_cases'] += $total;

                $groups[$generalName]['totals']['stages'][$stage]['M'] += $male;
                $groups[$generalName]['totals']['stages'][$stage]['F'] += $female;
                $groups[$generalName]['totals']['stages'][$stage]['T'] += $total;
                $groups[$generalName]['totals']['total_cases'] += $total;
            }

            $groups[$generalName]['rows'][] = [
                'name' => $specific->name,
                'parent' => $parentName,
                'totals' => $rowTotals,
            ];
        }

        $analytics = $this->buildForm1AAnalytics($groups);

        return [
            'code' => '1A',
            'form' => 'Reporting Form 1A',
            'title' => 'Number of New Cancer Cases by International Classification of Childhood Cancer, Sex and Initial Clinical Staging',
            'facility_name' => (string) $data['facility_name'],
            'facility_registration_no' => $facilityRegistrationNo,
            'from_date' => $fromDate->toDateString(),
            'to_date' => $toDate->toDateString(),
            'report_submission_date' => now()->toDateString(),
            'stages' => $this->form1AStages,
            'groups' => array_values($groups),
            'analytics' => $analytics,
        ];
    }

    protected function buildForm1BReport(array $data): array
    {
        $fromDate = Carbon::parse((string) $data['from_date'])->startOfDay();
        $toDate = Carbon::parse((string) $data['to_date'])->endOfDay();
        $facilityRegistrationNo = trim((string) $data['facility_registration_no']);

        $countRows = CancerDiagnose::query()
            ->selectRaw(
                'LOWER(COALESCE(cancer_diagnoses.gicc_indexed_cancer_type, cancer_diagnoses.iccc_specific_classification, ?)) as diagnosis_name,
                LOWER(form_demographics.sex_at_birth) as sex_at_birth,
                COUNT(*) as total',
                ['']
            )
            ->join('form_demographics', 'form_demographics.id', '=', 'cancer_diagnoses.form_demographic_id')
            // ->where('cancer_diagnoses.health_facility_registration_no', $facilityRegistrationNo)
            // ->whereIn('cancer_diagnoses.current_status_of_cancer', [
            //     'Newly diagnosed',
            //     'New case to center, no treatment',
            // ])
            ->whereBetween('cancer_diagnoses.diagnosis_date', [$fromDate->toDateString(), $toDate->toDateString()])
            ->groupBy('diagnosis_name', 'sex_at_birth')
            ->get();

        $rows = collect($this->form1BCategories)
            ->map(fn (array $category, int $index): array => [
                'no' => $index + 1,
                'label' => $category['label'],
                'male' => 0,
                'female' => 0,
                'total' => 0,
            ])
            ->all();

        foreach ($countRows as $record) {
            $diagnosisName = (string) $record->diagnosis_name;
            $sex = (string) $record->sex_at_birth;
            $count = (int) $record->total;

            if (! in_array($sex, ['male', 'female'], true)) {
                continue;
            }

            foreach ($this->form1BCategories as $index => $category) {
                $matchesCategory = false;
                foreach ($category['patterns'] as $pattern) {
                    if (str_contains($diagnosisName, $pattern)) {
                        $matchesCategory = true;
                        break;
                    }
                }

                if (! $matchesCategory) {
                    continue;
                }

                if ($sex === 'male') {
                    $rows[$index]['male'] += $count;
                } else {
                    $rows[$index]['female'] += $count;
                }

                $rows[$index]['total'] += $count;
                break;
            }
        }

        return [
            'code' => '1B',
            'form' => 'Reporting Form 1B',
            'title' => 'New Cancer Cases under the Global Initiative for Childhood Cancer Index',
            'facility_name' => (string) $data['facility_name'],
            'facility_registration_no' => $facilityRegistrationNo,
            'from_date' => $fromDate->toDateString(),
            'to_date' => $toDate->toDateString(),
            'report_submission_date' => now()->toDateString(),
            'rows' => $rows,
            'prepared_by_name' => '',
            'prepared_by_position' => '',
            'noted_by_name' => '',
            'noted_by_position' => 'CANCER COORDINATOR/ CANCER CENTER HEAD',
        ];
    }

    protected function buildForm1CReport(array $data): array
    {
        $fromDate = Carbon::parse((string) $data['from_date'])->startOfDay();
        $toDate = Carbon::parse((string) $data['to_date'])->endOfDay();

        $countRows = CancerDiagnose::query()
            ->selectRaw(
                'LOWER(COALESCE(cancer_diagnoses.current_status_of_cancer, ?)) as status_name,
                LOWER(form_demographics.sex_at_birth) as sex_at_birth,
                COUNT(*) as total',
                ['']
            )
            ->join('form_demographics', 'form_demographics.id', '=', 'cancer_diagnoses.form_demographic_id')
            ->whereBetween('cancer_diagnoses.diagnosis_date', [$fromDate->toDateString(), $toDate->toDateString()])
            ->groupBy('status_name', 'sex_at_birth')
            ->get();

        $rows = collect($this->form1CStatuses)
            ->map(fn (string $status, int $index): array => [
                'no' => $index + 1,
                'label' => $status,
                'male' => 0,
                'female' => 0,
                'total' => 0,
            ])
            ->all();

        foreach ($countRows as $record) {
            $statusName = (string) $record->status_name;
            $sex = (string) $record->sex_at_birth;
            $count = (int) $record->total;

            if (! in_array($sex, ['male', 'female'], true)) {
                continue;
            }

            foreach ($this->form1CStatuses as $index => $status) {
                if ($statusName !== strtolower($status)) {
                    continue;
                }

                if ($sex === 'male') {
                    $rows[$index]['male'] += $count;
                } else {
                    $rows[$index]['female'] += $count;
                }

                $rows[$index]['total'] += $count;
                break;
            }
        }

        return [
            'code' => '1C',
            'form' => 'Reporting Form 1C',
            'title' => 'Distribution of Patient\'s Disease Status at the Facility',
            'facility_name' => (string) $data['facility_name'],
            'facility_registration_no' => trim((string) $data['facility_registration_no']),
            'from_date' => $fromDate->toDateString(),
            'to_date' => $toDate->toDateString(),
            'report_submission_date' => now()->toDateString(),
            'rows' => $rows,
            'prepared_by_name' => '',
            'prepared_by_position' => '',
            'noted_by_name' => '',
            'noted_by_position' => 'CANCER COORDINATOR/ CANCER CENTER HEAD',
        ];
    }

    protected function buildForm2AReport(array $data): array
    {
        $fromDate = Carbon::parse((string) $data['from_date'])->startOfDay();
        $toDate = Carbon::parse((string) $data['to_date'])->endOfDay();
        $facilityRegistrationNo = trim((string) $data['facility_registration_no']);

        $baseFollowUps = PatientFollowUpForm::query()
            // ->where('patient_health_facility_id_no', $facilityRegistrationNo)
            ->whereBetween('follow_up_last_encounter_date', [$fromDate->toDateString(), $toDate->toDateString()]);

        // dump($baseFollowUps->toSql(), $baseFollowUps->getBindings());

        $rows = collect($this->form2ATreatments)
            ->map(fn (string $label, int $index): array => [
                'no' => $index + 1,
                'label' => $label,
                'patients' => 0,
                'frequency' => 0,
            ])
            ->all();

        $setTotals = function (int $index, $query, string $patientColumn = 'patient_follow_up_forms.form_demographic_id') use (&$rows): void {
            $rows[$index]['patients'] = (clone $query)->distinct($patientColumn)->count($patientColumn);
            $rows[$index]['frequency'] = (clone $query)->count();
        };

        $setTotals(
            0,
            PatientFollowUpFormSurgery::query()
                ->join('patient_follow_up_forms', 'patient_follow_up_forms.id', '=', 'patient_follow_up_form_surgeries.patient_follow_up_form_id')
                ->whereIn('patient_follow_up_forms.id', (clone $baseFollowUps)->select('id'))
        );

        $antiCancerBase = PatientFollowUpFormAntiCancerDrugTherapy::query()
            ->join('patient_follow_up_forms', 'patient_follow_up_forms.id', '=', 'patient_follow_up_form_anti_cancer_drug_therapies.patient_follow_up_form_id')
            ->whereIn('patient_follow_up_forms.id', (clone $baseFollowUps)->select('id'));

        $setTotals(1, $antiCancerBase->clone()->whereJsonContains('drug_types', 'Cytotoxic'));
        $setTotals(2, $antiCancerBase->clone()->whereJsonContains('drug_types', 'Hormonal'));
        $setTotals(3, $antiCancerBase->clone()->whereJsonContains('drug_types', 'Immunotherapy'));
        $setTotals(4, $antiCancerBase->clone()->whereJsonContains('drug_types', 'Targeted Therapy'));

        $radiotherapyBase = PatientFollowUpFormRadiotherapy::query()
            ->join('patient_follow_up_forms', 'patient_follow_up_forms.id', '=', 'patient_follow_up_form_radiotherapies.patient_follow_up_form_id')
            ->whereIn('patient_follow_up_forms.id', (clone $baseFollowUps)->select('id'));

        $setTotals(5, $radiotherapyBase->clone()->where('radiotherapy_type_specifics', 'like', '%2D%'));
        $setTotals(6, $radiotherapyBase->clone()->where('radiotherapy_type_specifics', 'like', '%3DCRT%'));
        $setTotals(7, $radiotherapyBase->clone()->where('radiotherapy_type', 'Brachytherapy'));
        $setTotals(8, $radiotherapyBase->clone()->where('radiotherapy_type_specifics', 'like', '%Electron%'));
        $setTotals(
            9,
            $radiotherapyBase->clone()->where(function ($query): void {
                $query->where('radiotherapy_type_specifics', 'like', '%IMRT%')
                    ->orWhere('radiotherapy_type_specifics', 'like', '%VMAT%')
                    ->orWhere('radiotherapy_type_specifics', 'like', '%Helical%');
            })
        );
        $setTotals(10, $radiotherapyBase->clone()->where('radiotherapy_type_specifics', 'like', '%IORT%'));
        $setTotals(11, $radiotherapyBase->clone()->where('radiotherapy_type_specifics', 'like', '%Heavy%'));
        $setTotals(12, $radiotherapyBase->clone()->where('radiotherapy_type_specifics', 'like', '%Proton%'));
        $setTotals(
            13,
            $radiotherapyBase->clone()->where(function ($query): void {
                $query->where('radiotherapy_type_specifics', 'like', '%Stereotactic%')
                    ->orWhere('radiotherapy_type_specifics', 'like', '%SRS%')
                    ->orWhere('radiotherapy_type_specifics', 'like', '%SRT%');
            })
        );
        $setTotals(14, $radiotherapyBase->clone()->where('radiotherapy_type', 'Others'));

        $theranosticsBase = PatientFollowUpFormTheranostic::query()
            ->join('patient_follow_up_forms', 'patient_follow_up_forms.id', '=', 'patient_follow_up_form_theranostics.patient_follow_up_form_id')
            ->whereIn('patient_follow_up_forms.id', (clone $baseFollowUps)->select('id'));

        $setTotals(15, $theranosticsBase->clone()->where('drugs_given', 'like', '%RAI%'));
        $setTotals(16, $theranosticsBase->clone()->where('drugs_given', 'like', '%PRRT%'));
        $setTotals(17, $theranosticsBase->clone()->where('drugs_given', 'like', '%SIRT%'));
        $setTotals(18, $theranosticsBase->clone()->where('drug_type_other', 'like', '%theranostic%'));

        $palliativeBase = PatientFollowUpFormPalliativeCare::query()
            ->join('patient_follow_up_forms', 'patient_follow_up_forms.id', '=', 'patient_follow_up_form_palliative_cares.patient_follow_up_form_id')
            ->whereIn('patient_follow_up_forms.id', (clone $baseFollowUps)->select('id'));

        $setTotals(19, $palliativeBase->clone()->where('type_of_care_integration', 'Concurrent-Curative'));
        $setTotals(20, $palliativeBase->clone()->where('type_of_care_integration', 'Concurrent-Palliative'));
        $setTotals(21, $palliativeBase->clone()->where('type_of_care_integration', 'Palliative Only'));

        $otherDirectedBase = PatientFollowUpFormOtherCancerDirectedTherapy::query()
            ->join('patient_follow_up_forms', 'patient_follow_up_forms.id', '=', 'patient_follow_up_form_other_cancer_directed_therapies.patient_follow_up_form_id')
            ->whereIn('patient_follow_up_forms.id', (clone $baseFollowUps)->select('id'));

        $setTotals(22, $otherDirectedBase->clone()->whereIn('type_of_cancer_directed_therapy', ['Stem Cell Transplant', 'Transplant']));
        $setTotals(
            23,
            $otherDirectedBase->clone()
                ->where('type_of_cancer_directed_therapy', 'Others')
                ->where('type_of_cancer_directed_therapy_other', 'like', '%Chemoembolization%')
        );
        $setTotals(
            24,
            $otherDirectedBase->clone()->where(function ($query): void {
                $query->where('type_of_cancer_directed_therapy', '!=', 'Stem Cell Transplant')
                    ->where(function ($nested): void {
                        $nested->where('type_of_cancer_directed_therapy', '!=', 'Transplant')
                            ->where('type_of_cancer_directed_therapy_other', 'not like', '%Chemoembolization%');
                    });
            })
        );

        $baseFollowUpIds = (clone $baseFollowUps)->pluck('id');

        $withAnyTreatmentIds = collect()
            ->merge(
                PatientFollowUpFormSurgery::query()
                    ->whereIn('patient_follow_up_form_id', $baseFollowUpIds)
                    ->pluck('patient_follow_up_form_id')
            )
            ->merge(
                PatientFollowUpFormAntiCancerDrugTherapy::query()
                    ->whereIn('patient_follow_up_form_id', $baseFollowUpIds)
                    ->pluck('patient_follow_up_form_id')
            )
            ->merge(
                PatientFollowUpFormRadiotherapy::query()
                    ->whereIn('patient_follow_up_form_id', $baseFollowUpIds)
                    ->pluck('patient_follow_up_form_id')
            )
            ->merge(
                PatientFollowUpFormTheranostic::query()
                    ->whereIn('patient_follow_up_form_id', $baseFollowUpIds)
                    ->pluck('patient_follow_up_form_id')
            )
            ->merge(
                PatientFollowUpFormPalliativeCare::query()
                    ->whereIn('patient_follow_up_form_id', $baseFollowUpIds)
                    ->pluck('patient_follow_up_form_id')
            )
            ->merge(
                PatientFollowUpFormOtherCancerDirectedTherapy::query()
                    ->whereIn('patient_follow_up_form_id', $baseFollowUpIds)
                    ->pluck('patient_follow_up_form_id')
            )
            ->filter()
            ->unique()
            ->values();

        $noTreatment = (clone $baseFollowUps)->whereNotIn('id', $withAnyTreatmentIds->all());

        $rows[25]['patients'] = $noTreatment->distinct('form_demographic_id')->count('form_demographic_id');
        $rows[25]['frequency'] = (clone $noTreatment)->count();

        return [
            'code' => '2A',
            'form' => 'Reporting Form 2A',
            'title' => 'Completed Treatments Given to Cancer Patients at the Health Facility of Consult',
            'facility_name' => (string) $data['facility_name'],
            'facility_registration_no' => $facilityRegistrationNo,
            'from_date' => $fromDate->toDateString(),
            'to_date' => $toDate->toDateString(),
            'report_submission_date' => now()->toDateString(),
            'rows' => $rows,
            'prepared_by_name' => '',
            'prepared_by_position' => '',
            'noted_by_name' => '',
            'noted_by_position' => 'CANCER COORDINATOR/ CANCER CENTER HEAD',
        ];
    }

    protected function buildForm2BReport(array $data): array
    {
        $fromDate = Carbon::parse((string) $data['from_date'])->startOfDay();
        $toDate = Carbon::parse((string) $data['to_date'])->endOfDay();
        $facilityRegistrationNo = trim((string) $data['facility_registration_no']);

        $followUps = PatientFollowUpForm::query()
            // ->where('patient_health_facility_id_no', $facilityRegistrationNo)
            ->whereNotNull('disease_outcome')
            ->whereBetween('follow_up_last_encounter_date', [$fromDate->toDateString(), $toDate->toDateString()])
            ->with('formDemographic:id,sex_at_birth')
            ->orderByDesc('follow_up_last_encounter_date')
            ->orderByDesc('id')
            ->get();

        $latestByPatient = $followUps
            ->unique('form_demographic_id')
            ->values();

        $rows = collect($this->form2BOutcomes)
            ->map(fn (string $label, int $index): array => [
                'no' => $index + 1,
                'label' => $label,
                'male' => 0,
                'female' => 0,
                'total' => 0,
            ])
            ->all();

        $normalizeOutcome = function (string $outcome): string {
            $normalized = strtolower(trim($outcome));

            return match ($normalized) {
                'progressive/refractory' => 'disease progression',
                'death (cancer-related)' => 'death (cancer related)',
                'death (treatment-related)' => 'death (treatment related)',
                default => $normalized,
            };
        };

        foreach ($latestByPatient as $followUp) {
            $outcome = $normalizeOutcome((string) ($followUp->disease_outcome ?? ''));
            $sex = strtolower((string) ($followUp->formDemographic?->sex_at_birth ?? ''));

            if (! in_array($sex, ['male', 'female'], true)) {
                continue;
            }

            foreach ($this->form2BOutcomes as $index => $label) {
                if ($outcome !== strtolower($label)) {
                    continue;
                }

                if ($sex === 'male') {
                    $rows[$index]['male']++;
                } else {
                    $rows[$index]['female']++;
                }

                $rows[$index]['total']++;
                break;
            }
        }

        return [
            'code' => '2B',
            'form' => 'Reporting Form 2B',
            'title' => 'Cancer Disease Outcome',
            'facility_name' => (string) $data['facility_name'],
            'facility_registration_no' => $facilityRegistrationNo,
            'from_date' => $fromDate->toDateString(),
            'to_date' => $toDate->toDateString(),
            'report_submission_date' => now()->toDateString(),
            'rows' => $rows,
            'prepared_by_name' => '',
            'prepared_by_position' => '',
            'noted_by_name' => '',
            'noted_by_position' => 'CANCER COORDINATOR/ CANCER CENTER HEAD',
        ];
    }

    protected function buildForm2CReport(array $data): array
    {
        $fromDate = Carbon::parse((string) $data['from_date'])->startOfDay();
        $toDate = Carbon::parse((string) $data['to_date'])->endOfDay();
        $facilityRegistrationNo = trim((string) $data['facility_registration_no']);

        $followUps = PatientFollowUpForm::query()
            // ->where('patient_health_facility_id_no', $facilityRegistrationNo)
            ->whereNotNull('treatment_status')
            ->whereBetween('follow_up_last_encounter_date', [$fromDate->toDateString(), $toDate->toDateString()])
            ->with('formDemographic:id,sex_at_birth')
            ->orderByDesc('follow_up_last_encounter_date')
            ->orderByDesc('id')
            ->get();

        $latestByPatient = $followUps
            ->unique('form_demographic_id')
            ->values();

        $rows = collect($this->form2CStatuses)
            ->map(fn (string $label, int $index): array => [
                'no' => $index + 1,
                'label' => $label,
                'male' => 0,
                'female' => 0,
                'total' => 0,
            ])
            ->all();

        foreach ($latestByPatient as $followUp) {
            $status = strtolower(trim((string) ($followUp->treatment_status ?? '')));
            $sex = strtolower((string) ($followUp->formDemographic?->sex_at_birth ?? ''));

            if (! in_array($sex, ['male', 'female'], true)) {
                continue;
            }

            foreach ($this->form2CStatuses as $index => $label) {
                if ($status !== strtolower($label)) {
                    continue;
                }

                if ($sex === 'male') {
                    $rows[$index]['male']++;
                } else {
                    $rows[$index]['female']++;
                }

                $rows[$index]['total']++;
                break;
            }
        }

        return [
            'code' => '2C',
            'form' => 'Reporting Form 2C',
            'title' => 'Patient Treatment Status',
            'facility_name' => (string) $data['facility_name'],
            'facility_registration_no' => $facilityRegistrationNo,
            'from_date' => $fromDate->toDateString(),
            'to_date' => $toDate->toDateString(),
            'report_submission_date' => now()->toDateString(),
            'rows' => $rows,
            'prepared_by_name' => '',
            'prepared_by_position' => '',
            'noted_by_name' => '',
            'noted_by_position' => 'CANCER COORDINATOR/ CANCER CENTER HEAD',
        ];
    }

    protected function buildForm1AAnalytics(array $groups): array
    {
        $stageTotals = [];
        $totalMale = 0;
        $totalFemale = 0;
        $totalCases = 0;
        $classificationTotals = [];

        foreach ($this->form1AStages as $stage) {
            $stageTotals[$stage] = [
                'M' => 0,
                'F' => 0,
                'T' => 0,
            ];
        }

        foreach ($groups as $group) {
            $rows = $group['rows'] ?? [];

            foreach ($rows as $row) {
                $rowTotalCases = (int) ($row['totals']['total_cases'] ?? 0);
                $classificationTotals[] = [
                    'name' => (string) ($row['name'] ?? 'Unspecified'),
                    'total' => $rowTotalCases,
                ];

                foreach ($this->form1AStages as $stage) {
                    $male = (int) ($row['totals']['stages'][$stage]['M'] ?? 0);
                    $female = (int) ($row['totals']['stages'][$stage]['F'] ?? 0);
                    $total = (int) ($row['totals']['stages'][$stage]['T'] ?? 0);

                    $stageTotals[$stage]['M'] += $male;
                    $stageTotals[$stage]['F'] += $female;
                    $stageTotals[$stage]['T'] += $total;

                    $totalMale += $male;
                    $totalFemale += $female;
                    $totalCases += $total;
                }
            }
        }

        $topClassifications = collect($classificationTotals)
            ->sortByDesc('total')
            ->filter(fn (array $item): bool => $item['total'] > 0)
            ->take(8)
            ->values()
            ->all();

        return [
            'total_cases' => $totalCases,
            'male_cases' => $totalMale,
            'female_cases' => $totalFemale,
            'stage_totals' => $stageTotals,
            'top_classifications' => $topClassifications,
        ];
    }

    protected function blankTotals(): array
    {
        $stages = [];
        foreach ($this->form1AStages as $stage) {
            $stages[$stage] = [
                'M' => 0,
                'F' => 0,
                'T' => 0,
            ];
        }

        return [
            'stages' => $stages,
            'total_cases' => 0,
        ];
    }

    protected function buildForm1AFilename(): string
    {
        if (! filled($this->form1AReport)) {
            return 'reporting-form-1a';
        }

        $from = str_replace('-', '', (string) ($this->form1AReport['from_date'] ?? now()->toDateString()));
        $to = str_replace('-', '', (string) ($this->form1AReport['to_date'] ?? now()->toDateString()));

        return uniqid() . "-reporting-form-1a-{$from}-to-{$to}";
    }

    protected function buildForm1BFilename(): string
    {
        if (! filled($this->form1BReport)) {
            return 'reporting-form-1b';
        }

        $from = str_replace('-', '', (string) ($this->form1BReport['from_date'] ?? now()->toDateString()));
        $to = str_replace('-', '', (string) ($this->form1BReport['to_date'] ?? now()->toDateString()));

        return uniqid() . "-reporting-form-1b-{$from}-to-{$to}";
    }

    protected function buildForm1CFilename(): string
    {
        if (! filled($this->form1CReport)) {
            return 'reporting-form-1c';
        }

        $from = str_replace('-', '', (string) ($this->form1CReport['from_date'] ?? now()->toDateString()));
        $to = str_replace('-', '', (string) ($this->form1CReport['to_date'] ?? now()->toDateString()));

        return uniqid() . "-reporting-form-1c-{$from}-to-{$to}";
    }

    protected function buildForm2AFilename(): string
    {
        if (! filled($this->form2AReport)) {
            return 'reporting-form-2a';
        }

        $from = str_replace('-', '', (string) ($this->form2AReport['from_date'] ?? now()->toDateString()));
        $to = str_replace('-', '', (string) ($this->form2AReport['to_date'] ?? now()->toDateString()));

        return uniqid() . "-reporting-form-2a-{$from}-to-{$to}";
    }

    protected function buildForm2BFilename(): string
    {
        if (! filled($this->form2BReport)) {
            return 'reporting-form-2b';
        }

        $from = str_replace('-', '', (string) ($this->form2BReport['from_date'] ?? now()->toDateString()));
        $to = str_replace('-', '', (string) ($this->form2BReport['to_date'] ?? now()->toDateString()));

        return uniqid() . "-reporting-form-2b-{$from}-to-{$to}";
    }

    protected function buildForm2CFilename(): string
    {
        if (! filled($this->form2CReport)) {
            return 'reporting-form-2c';
        }

        $from = str_replace('-', '', (string) ($this->form2CReport['from_date'] ?? now()->toDateString()));
        $to = str_replace('-', '', (string) ($this->form2CReport['to_date'] ?? now()->toDateString()));

        return uniqid() . "-reporting-form-2c-{$from}-to-{$to}";
    }
}
