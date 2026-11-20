<?php

namespace App\Filament\Pages;

use App\Models\CancerDiagnose;
use App\Models\FormDemographics;
use App\Models\PatientFollowUpForm;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use UnitEnum;

class PatientForms extends Page
{
    public const FORM_ONE = 'form1';

    public const FORM_TWO = 'form2';

    public const MINIMUM_SEARCH_LENGTH = 2;

    public const MAXIMUM_RESULTS = 10;

    protected string $view = 'filament.pages.patient-forms';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentMagnifyingGlass;

    protected static ?string $navigationLabel = 'Patient Forms';

    protected static UnitEnum|string|null $navigationGroup = 'Main';

    protected static ?int $navigationSort = 11;

    protected static ?string $title = 'Patient Forms';

    protected static ?string $slug = 'patient-forms';

    public string $search = '';

    public ?int $selectedPatientId = null;

    public ?string $activeForm = null;

    public ?int $selectedDiagnosisId = null;

    public ?int $selectedFollowUpId = null;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('View:PatientForms') ?? false;
    }

    /**
     * @return Collection<int, FormDemographics>
     */
    #[Computed]
    public function patientResults(): Collection
    {
        $terms = $this->searchTerms();

        if ($terms === []) {
            return collect();
        }

        return FormDemographics::query()
            ->withCount(['cancerDiagnoses', 'patientFollowUpForms'])
            ->where(function (Builder $query) use ($terms): void {
                foreach ($terms as $term) {
                    $query->where(fn (Builder $termQuery) => $this->matchTerm($termQuery, $term));
                }
            })
            ->orderBy('patient_surname')
            ->orderBy('patient_first_name')
            ->limit(self::MAXIMUM_RESULTS)
            ->get();
    }

    #[Computed]
    public function selectedPatient(): ?FormDemographics
    {
        if ($this->selectedPatientId === null) {
            return null;
        }

        return FormDemographics::query()
            ->with([
                'cancerDiagnoses' => fn ($query) => $query->orderBy('primary_cancer_site_number')->orderBy('id'),
                'patientFollowUpForms' => fn ($query) => $query->latest('follow_up_last_encounter_date')->latest('id'),
            ])
            ->find($this->selectedPatientId);
    }

    #[Computed]
    public function previewUrl(): ?string
    {
        if ($this->activeForm === self::FORM_ONE && $this->selectedDiagnosisId !== null) {
            return route('form-details', ['cancerDiagnose' => $this->selectedDiagnosisId]);
        }

        if ($this->activeForm === self::FORM_TWO && $this->selectedFollowUpId !== null) {
            return route('follow-up-form-details', ['patientFollowUpForm' => $this->selectedFollowUpId]);
        }

        return null;
    }

    public function canPreviewFormOne(): bool
    {
        return auth()->user()?->can('View:CancerDiagnose') ?? false;
    }

    public function canPreviewFormTwo(): bool
    {
        return auth()->user()?->can('View:PatientFollowUpForm') ?? false;
    }

    public function previewForm(int $patientId, string $form): void
    {
        $this->selectedPatientId = $patientId;
        unset($this->selectedPatient);

        $this->showForm($form);
    }

    public function showForm(string $form): void
    {
        $patient = $this->selectedPatient;

        if ($patient === null || ! in_array($form, [self::FORM_ONE, self::FORM_TWO], true)) {
            return;
        }

        $this->activeForm = $form;
        $this->selectedDiagnosisId = $patient->cancerDiagnoses->first()?->id;
        $this->selectedFollowUpId = $patient->patientFollowUpForms->first()?->id;
    }

    public function updatedSelectedDiagnosisId(mixed $value): void
    {
        $this->selectedDiagnosisId = $this->selectedPatient?->cancerDiagnoses
            ->firstWhere('id', (int) $value)?->id;
    }

    public function updatedSelectedFollowUpId(mixed $value): void
    {
        $this->selectedFollowUpId = $this->selectedPatient?->patientFollowUpForms
            ->firstWhere('id', (int) $value)?->id;
    }

    public function clearSelection(): void
    {
        $this->reset(['selectedPatientId', 'activeForm', 'selectedDiagnosisId', 'selectedFollowUpId']);
    }

    public function diagnosisLabel(CancerDiagnose $diagnosis): string
    {
        return collect([
            'Primary site #'.($diagnosis->primary_cancer_site_number ?? 1),
            $diagnosis->iccc_specific_classification,
            $diagnosis->diagnosis_date?->format('M d, Y'),
        ])->filter()->implode(' — ');
    }

    public function followUpLabel(PatientFollowUpForm $followUp): string
    {
        return collect([
            $followUp->follow_up_last_encounter_date?->format('M d, Y') ?? 'Undated follow-up',
            $followUp->treatment_status,
            $followUp->disease_outcome,
        ])->filter()->implode(' — ');
    }

    /**
     * @return array<int, string>
     */
    protected function searchTerms(): array
    {
        $search = trim($this->search);

        if (mb_strlen($search) < self::MINIMUM_SEARCH_LENGTH) {
            return [];
        }

        return array_values(array_filter(preg_split('/\s+/', $search) ?: []));
    }

    protected function matchTerm(Builder $query, string $term): void
    {
        $pattern = '%'.addcslashes($term, '%_\\').'%';

        $query->where('health_facility_id_no', 'like', $pattern)
            ->orWhere('patient_first_name', 'like', $pattern)
            ->orWhere('patient_middle_name', 'like', $pattern)
            ->orWhere('patient_surname', 'like', $pattern)
            ->orWhere('patient_suffix', 'like', $pattern)
            ->orWhereHas('cancerDiagnoses', fn (Builder $diagnoses) => $diagnoses->where('patient_health_facility_id_no', 'like', $pattern))
            ->orWhereHas('patientFollowUpForms', fn (Builder $followUps) => $followUps->where('patient_health_facility_id_no', 'like', $pattern));
    }
}
