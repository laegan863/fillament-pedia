<x-filament-panels::page>
    <x-filament::breadcrumbs :breadcrumbs="[
        '/' => 'Home',
        '/administrator/patient-forms' => 'Patient Forms',
    ]" />

    <div class="space-y-6">
        {{-- SEARCH --}}
        <x-filament::section icon="heroicon-o-magnifying-glass" data-tour="patient-forms-search">
            <x-slot name="heading">
                Find a Patient
            </x-slot>

            <x-slot name="description">
                Search by Hospital ID / Patient's Health Facility ID No. or patient name, then choose Form 1 or Form 2 to preview.
            </x-slot>

            <div class="space-y-4">
                <x-filament::input.wrapper prefix-icon="heroicon-m-magnifying-glass" wire:target="search" class="max-w-3xl">
                    <x-filament::input
                        type="search"
                        wire:model.live.debounce.400ms="search"
                        placeholder="e.g. 0000012345 or Juan Dela Cruz"
                        autocomplete="off"
                        autofocus
                    />
                </x-filament::input.wrapper>

                @php
                    $trimmedSearch = trim($search);
                    $results = $this->patientResults;
                @endphp

                @if (mb_strlen($trimmedSearch) === 0)
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Start typing at least {{ \App\Filament\Pages\PatientForms::MINIMUM_SEARCH_LENGTH }} characters to search.
                    </p>
                @elseif (mb_strlen($trimmedSearch) < \App\Filament\Pages\PatientForms::MINIMUM_SEARCH_LENGTH)
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Keep typing…
                    </p>
                @elseif ($results->isEmpty())
                    <div class="rounded-xl border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                        No patient found for “<span class="font-semibold text-gray-700 dark:text-gray-200">{{ $trimmedSearch }}</span>”.
                    </div>
                @else
                    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 text-gray-700 dark:bg-white/5 dark:text-gray-300">
                                <tr>
                                    <th class="px-4 py-3 font-semibold">Health Facility ID No.</th>
                                    <th class="px-4 py-3 font-semibold">Patient Name</th>
                                    <th class="px-4 py-3 font-semibold">Sex</th>
                                    <th class="px-4 py-3 font-semibold">Date of Birth</th>
                                    <th class="px-4 py-3 font-semibold text-center">Records</th>
                                    <th class="px-4 py-3 font-semibold text-right">Preview</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($results as $patient)
                                    <tr wire:key="patient-result-{{ $patient->id }}"
                                        @class([
                                            'bg-primary-50 dark:bg-primary-500/10' => $selectedPatientId === $patient->id,
                                        ])>
                                        <td class="px-4 py-3 font-mono text-gray-950 dark:text-white">
                                            {{ $patient->health_facility_id_no ?: '—' }}
                                        </td>
                                        <td class="px-4 py-3 font-medium text-gray-950 dark:text-white">
                                            {{ $patient->full_name ?: '—' }}
                                        </td>
                                        <td class="px-4 py-3 capitalize text-gray-700 dark:text-gray-300">
                                            {{ $patient->sex_at_birth ?: '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                            {{ $patient->date_of_birth?->format('M d, Y') ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="flex flex-wrap justify-center gap-1">
                                                <x-filament::badge color="info" size="sm">
                                                    {{ $patient->cancer_diagnoses_count }} Form 1
                                                </x-filament::badge>
                                                <x-filament::badge color="success" size="sm">
                                                    {{ $patient->patient_follow_up_forms_count }} Form 2
                                                </x-filament::badge>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex justify-end gap-2">
                                                @if ($this->canPreviewFormOne())
                                                    <x-filament::button size="sm" icon="heroicon-m-document-text"
                                                        wire:click="previewForm({{ $patient->id }}, 'form1')"
                                                        :disabled="$patient->cancer_diagnoses_count === 0">
                                                        Form 1
                                                    </x-filament::button>
                                                @endif

                                                @if ($this->canPreviewFormTwo())
                                                    <x-filament::button size="sm" color="gray" icon="heroicon-m-clipboard-document-list"
                                                        wire:click="previewForm({{ $patient->id }}, 'form2')"
                                                        :disabled="$patient->patient_follow_up_forms_count === 0">
                                                        Form 2
                                                    </x-filament::button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($results->count() === \App\Filament\Pages\PatientForms::MAXIMUM_RESULTS)
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Showing the first {{ \App\Filament\Pages\PatientForms::MAXIMUM_RESULTS }} matches. Refine your search to narrow the list.
                        </p>
                    @endif
                @endif
            </div>
        </x-filament::section>

        {{-- PREVIEW --}}
        @php($selectedPatient = $this->selectedPatient)

        @if ($selectedPatient && $activeForm)
            <x-filament::section icon="heroicon-o-eye">
                <x-slot name="heading">
                    {{ $selectedPatient->full_name ?: 'Unnamed patient' }}
                </x-slot>

                <x-slot name="description">
                    Health Facility ID No.: {{ $selectedPatient->health_facility_id_no ?: '—' }}
                </x-slot>

                <x-slot name="afterHeader">
                    <x-filament::button size="sm" color="gray" icon="heroicon-m-x-mark" wire:click="clearSelection">
                        Close
                    </x-filament::button>
                </x-slot>

                <div class="space-y-4">
                    <div class="flex flex-wrap items-end justify-between gap-4">
                        <x-filament::tabs>
                            @if ($this->canPreviewFormOne())
                                <x-filament::tabs.item icon="heroicon-m-document-text"
                                    :active="$activeForm === 'form1'" wire:click="showForm('form1')">
                                    Form 1: Baseline Patient Information
                                </x-filament::tabs.item>
                            @endif

                            @if ($this->canPreviewFormTwo())
                                <x-filament::tabs.item icon="heroicon-m-clipboard-document-list"
                                    :active="$activeForm === 'form2'" wire:click="showForm('form2')">
                                    Form 2: Patient Follow-Up Form
                                </x-filament::tabs.item>
                            @endif
                        </x-filament::tabs>

                        <div class="flex flex-wrap items-end gap-2">
                            @if ($activeForm === 'form1' && $selectedPatient->cancerDiagnoses->count() > 1)
                                <x-filament::input.wrapper class="min-w-72">
                                    <x-filament::input.select wire:model.live="selectedDiagnosisId">
                                        @foreach ($selectedPatient->cancerDiagnoses as $diagnosis)
                                            <option value="{{ $diagnosis->id }}">{{ $this->diagnosisLabel($diagnosis) }}</option>
                                        @endforeach
                                    </x-filament::input.select>
                                </x-filament::input.wrapper>
                            @endif

                            @if ($activeForm === 'form2' && $selectedPatient->patientFollowUpForms->count() > 1)
                                <x-filament::input.wrapper class="min-w-72">
                                    <x-filament::input.select wire:model.live="selectedFollowUpId">
                                        @foreach ($selectedPatient->patientFollowUpForms as $followUp)
                                            <option value="{{ $followUp->id }}">{{ $this->followUpLabel($followUp) }}</option>
                                        @endforeach
                                    </x-filament::input.select>
                                </x-filament::input.wrapper>
                            @endif

                            @if ($this->previewUrl)
                                <x-filament::button size="sm" color="gray" icon="heroicon-m-arrow-top-right-on-square"
                                    tag="a" :href="$this->previewUrl" target="_blank">
                                    Open / Print
                                </x-filament::button>
                            @endif
                        </div>
                    </div>

                    @if ($this->previewUrl)
                        <div class="overflow-hidden rounded-xl border border-gray-200 bg-gray-100 dark:border-gray-700"
                            wire:key="preview-{{ $activeForm }}-{{ $selectedDiagnosisId }}-{{ $selectedFollowUpId }}">
                            <iframe src="{{ $this->previewUrl }}" title="Form preview" class="block w-full"
                                style="height: 80vh; min-height: 640px;" loading="lazy"></iframe>
                        </div>
                    @else
                        <div class="rounded-xl border border-dashed border-gray-300 p-8 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                            @if ($activeForm === 'form1')
                                No Form 1 (cancer diagnosis) has been recorded for this patient yet.
                            @else
                                No Form 2 (follow-up) has been recorded for this patient yet.
                            @endif
                        </div>
                    @endif
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
