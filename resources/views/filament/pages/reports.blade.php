<x-filament-panels::page>
    <x-filament::breadcrumbs :breadcrumbs="[
        '/' => 'Home',
        '/administrator/reports' => 'Reports',
    ]" />

    <div class="space-y-6">

        <x-filament::section>
            <x-slot name="heading">
                Reporting Forms Templates
            </x-slot>

            <x-slot name="description">
                Select a report form to view, generate, or export.
            </x-slot>

            <div class="space-y-8">

                {{-- FORM 1 --}}
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <div class="mb-4">
                        <h2 class="text-lg font-bold text-gray-950 dark:text-white">
                            Form 1: Baseline Patient Information
                        </h2>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Only newly diagnosed cancer cases shall be recorded within the designated reporting period.
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-800">
                                    <th class="border px-4 py-3 text-left font-semibold dark:border-gray-700">Form</th>
                                    <th class="border px-4 py-3 text-left font-semibold dark:border-gray-700">Report Title</th>
                                    <th class="border px-4 py-3 text-left font-semibold dark:border-gray-700">Source / Notes</th>
                                    <th class="border px-4 py-3 text-center font-semibold dark:border-gray-700">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td class="border px-4 py-3 font-semibold dark:border-gray-700">
                                        Reporting Form 1A
                                    </td>
                                    <td class="border px-4 py-3 dark:border-gray-700">
                                        Number of New Cancer Cases by International Classification of Childhood Cancer,
                                        Sex and Initial Clinical Staging
                                    </td>
                                    <td class="border px-4 py-3 text-gray-600 dark:border-gray-700 dark:text-gray-400">
                                        Only newly diagnosed cancer cases shall be recorded within the designated reporting period.
                                    </td>
                                    <td class="border px-4 py-3 text-center dark:border-gray-700">
                                        {{ ($this->generateReportAction)([
                                            'code' => '1A',
                                            'form' => 'Reporting Form 1A',
                                            'title' => 'Number of New Cancer Cases by International Classification of Childhood Cancer, Sex and Initial Clinical Staging',
                                        ]) }}
                                    </td>
                                </tr>

                                <tr>
                                    <td class="border px-4 py-3 font-semibold dark:border-gray-700">
                                        Reporting Form 1B
                                    </td>
                                    <td class="border px-4 py-3 dark:border-gray-700">
                                        New Cancer Cases under the Global Initiative for Childhood Cancer Index
                                    </td>
                                    <td class="border px-4 py-3 text-gray-600 dark:border-gray-700 dark:text-gray-400">
                                        Only newly diagnosed cancer cases shall be recorded within the designated reporting period.
                                    </td>
                                    <td class="border px-4 py-3 text-center dark:border-gray-700">
                                        {{ ($this->generateReportAction)([
                                            'code' => '1B',
                                            'form' => 'Reporting Form 1B',
                                            'title' => 'New Cancer Cases under the Global Initiative for Childhood Cancer Index',
                                        ]) }}
                                    </td>
                                </tr>

                                <tr>
                                    <td class="border px-4 py-3 font-semibold dark:border-gray-700">
                                        Reporting Form 1C
                                    </td>
                                    <td class="border px-4 py-3 dark:border-gray-700">
                                        Distribution of Patient's Disease Status at the Facility
                                    </td>
                                    <td class="border px-4 py-3 bg-warning-50 text-warning-700 dark:border-gray-700 dark:bg-warning-900/20 dark:text-warning-300">
                                        Shall be sourced from the total count of all active cancer patients, both new and existing,
                                        as of the reporting period.
                                    </td>
                                    <td class="border px-4 py-3 text-center dark:border-gray-700">
                                        {{ ($this->generateReportAction)([
                                            'code' => '1C',
                                            'form' => 'Reporting Form 1C',
                                            'title' => 'Distribution of Patient\'s Disease Status at the Facility',
                                        ]) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- FORM 2 --}}
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <div class="mb-4">
                        <h2 class="text-lg font-bold text-gray-950 dark:text-white">
                            Form 2: Patient Follow-Up Form
                        </h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-800">
                                    <th class="border px-4 py-3 text-left font-semibold dark:border-gray-700">Form</th>
                                    <th class="border px-4 py-3 text-left font-semibold dark:border-gray-700">Report Title</th>
                                    <th class="border px-4 py-3 text-left font-semibold dark:border-gray-700">Source / Notes</th>
                                    <th class="border px-4 py-3 text-center font-semibold dark:border-gray-700">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td class="border px-4 py-3 font-semibold dark:border-gray-700">
                                        Reporting Form 2A
                                    </td>
                                    <td class="border px-4 py-3 dark:border-gray-700">
                                        Completed Treatment Given to Cancer Patients at the Health Facility of Consult
                                    </td>
                                    <td class="border px-4 py-3 text-gray-600 dark:border-gray-700 dark:text-gray-400">
                                        Shall be sourced from the first completed treatment given to cancer patients
                                        at the health facility of consult.
                                    </td>
                                    <td class="border px-4 py-3 text-center dark:border-gray-700">
                                        {{ ($this->generateReportAction)([
                                            'code' => '2A',
                                            'form' => 'Reporting Form 2A',
                                            'title' => 'Completed Treatment Given to Cancer Patients at the Health Facility of Consult',
                                        ]) }}
                                    </td>
                                </tr>

                                <tr>
                                    <td class="border px-4 py-3 font-semibold dark:border-gray-700">
                                        Reporting Form 2B
                                    </td>
                                    <td class="border px-4 py-3 dark:border-gray-700">
                                        Cancer Disease Outcome
                                    </td>
                                    <td class="border px-4 py-3 text-gray-600 dark:border-gray-700 dark:text-gray-400">
                                        Shall be sourced from the latest cancer diagnosis outcome as of the reporting period.
                                    </td>
                                    <td class="border px-4 py-3 text-center dark:border-gray-700">
                                        {{ ($this->generateReportAction)([
                                            'code' => '2B',
                                            'form' => 'Reporting Form 2B',
                                            'title' => 'Cancer Disease Outcome',
                                        ]) }}
                                    </td>
                                </tr>

                                <tr>
                                    <td class="border px-4 py-3 font-semibold dark:border-gray-700">
                                        Reporting Form 2C
                                    </td>
                                    <td class="border px-4 py-3 dark:border-gray-700">
                                        Patient Treatment Status
                                    </td>
                                    <td class="border px-4 py-3 text-gray-600 dark:border-gray-700 dark:text-gray-400">
                                        Shall be sourced from the total count of all active cancer patients, both new and existing,
                                        as of the reporting period.
                                    </td>
                                    <td class="border px-4 py-3 text-center dark:border-gray-700">
                                        {{ ($this->generateReportAction)([
                                            'code' => '2C',
                                            'form' => 'Reporting Form 2C',
                                            'title' => 'Patient Treatment Status',
                                        ]) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- FORM 0 --}}
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <div class="mb-4">
                        <h2 class="text-lg font-bold text-gray-950 dark:text-white">
                            Form 0: General Reporting Form
                        </h2>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            The data shall be derived from all four HBCR forms based on the information recorded
                            within the designated reporting period.
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-800">
                                    <th class="border px-4 py-3 text-left font-semibold dark:border-gray-700">Form</th>
                                    <th class="border px-4 py-3 text-left font-semibold dark:border-gray-700">Report Title</th>
                                    <th class="border px-4 py-3 text-left font-semibold dark:border-gray-700">Source / Notes</th>
                                    <th class="border px-4 py-3 text-center font-semibold dark:border-gray-700">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td class="border px-4 py-3 font-semibold dark:border-gray-700">
                                        Reporting Form 0A
                                    </td>
                                    <td class="border px-4 py-3 dark:border-gray-700">
                                        Frequency of Financial Support Mechanism Availed by Cancer Patients
                                    </td>
                                    <td class="border px-4 py-3 text-gray-600 dark:border-gray-700 dark:text-gray-400">
                                        Shall be derived from HBCR forms based on records within the reporting period.
                                    </td>
                                    <td class="border px-4 py-3 text-center dark:border-gray-700">
                                        {{ ($this->generateReportAction)([
                                            'code' => '0A',
                                            'form' => 'Reporting Form 0A',
                                            'title' => 'Frequency of Financial Support Mechanism Availed by Cancer Patients',
                                        ]) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </x-filament::section>

        @if (filled($this->form1AReport))
            <x-filament::section>
                <x-slot name="heading">
                    Reporting Form 1A Ready
                </x-slot>

                <x-slot name="description">
                    Preview opens in a modal. You can reopen it anytime and export to PDF.
                </x-slot>

                <div class="mb-2 flex flex-wrap items-center justify-end gap-2">
                    <x-filament::button
                        color="primary"
                        icon="heroicon-o-eye"
                        x-on:click="$dispatch('open-modal', { id: 'form-1a-preview-modal' })"
                    >
                        Open Preview
                    </x-filament::button>

                    {{ ($this->exportForm1APdfAction)([]) }}
                </div>
            </x-filament::section>

            <x-filament::modal id="form-1a-preview-modal" width="7xl" slide-over="false" :close-by-clicking-away="false">
                <x-slot name="heading">
                    Reporting Form 1A Preview
                </x-slot>

                <x-slot name="description">
                    Review the generated results first, then export to PDF.
                </x-slot>

                <div id="form-1a-preview" class="max-h-[75vh] overflow-auto">
                    @include('filament.pages.partials.form-1a-report', [
                        'report' => $this->form1AReport,
                        'forPdf' => false,
                    ])
                </div>

                <x-slot name="footerActions">
                    <x-filament::button color="gray" x-on:click="$dispatch('close-modal', { id: 'form-1a-preview-modal' })">
                        Close
                    </x-filament::button>

                    {{ ($this->exportForm1APdfAction)([]) }}
                </x-slot>
            </x-filament::modal>
        @endif

        @if (filled($this->form1BReport))
            <x-filament::section>
                <x-slot name="heading">
                    Reporting Form 1B Ready
                </x-slot>

                <x-slot name="description">
                    Preview opens in a modal. You can reopen it anytime and export to PDF.
                </x-slot>

                <div class="mb-2 flex flex-wrap items-center justify-end gap-2">
                    <x-filament::button
                        color="primary"
                        icon="heroicon-o-eye"
                        x-on:click="$dispatch('open-modal', { id: 'form-1b-preview-modal' })"
                    >
                        Open Preview
                    </x-filament::button>

                    {{ ($this->exportForm1BPdfAction)([]) }}
                </div>
            </x-filament::section>

            <x-filament::modal id="form-1b-preview-modal" width="6xl" slide-over="false" :close-by-clicking-away="false">
                <x-slot name="heading">
                    Reporting Form 1B Preview
                </x-slot>

                <x-slot name="description">
                    Review the generated results first, then export to PDF.
                </x-slot>

                <div id="form-1b-preview" class="max-h-[75vh] overflow-auto">
                    @include('filament.pages.partials.form-1b-report', [
                        'report' => $this->form1BReport,
                        'forPdf' => false,
                    ])
                </div>

                <x-slot name="footerActions">
                    <x-filament::button color="gray" x-on:click="$dispatch('close-modal', { id: 'form-1b-preview-modal' })">
                        Close
                    </x-filament::button>

                    {{ ($this->exportForm1BPdfAction)([]) }}
                </x-slot>
            </x-filament::modal>
        @endif

        @if (filled($this->form1CReport))
            <x-filament::section>
                <x-slot name="heading">
                    Reporting Form 1C Ready
                </x-slot>

                <x-slot name="description">
                    Preview opens in a modal. You can reopen it anytime and export to PDF.
                </x-slot>

                <div class="mb-2 flex flex-wrap items-center justify-end gap-2">
                    <x-filament::button
                        color="primary"
                        icon="heroicon-o-eye"
                        x-on:click="$dispatch('open-modal', { id: 'form-1c-preview-modal' })"
                    >
                        Open Preview
                    </x-filament::button>

                    {{ ($this->exportForm1CPdfAction)([]) }}
                </div>
            </x-filament::section>

            <x-filament::modal id="form-1c-preview-modal" width="6xl" slide-over="false" :close-by-clicking-away="false">
                <x-slot name="heading">
                    Reporting Form 1C Preview
                </x-slot>

                <x-slot name="description">
                    Review the generated results first, then export to PDF.
                </x-slot>

                <div id="form-1c-preview" class="max-h-[75vh] overflow-auto">
                    @include('filament.pages.partials.form-1c-report', [
                        'report' => $this->form1CReport,
                        'forPdf' => false,
                    ])
                </div>

                <x-slot name="footerActions">
                    <x-filament::button color="gray" x-on:click="$dispatch('close-modal', { id: 'form-1c-preview-modal' })">
                        Close
                    </x-filament::button>

                    {{ ($this->exportForm1CPdfAction)([]) }}
                </x-slot>
            </x-filament::modal>
        @endif

        @if (filled($this->form2AReport))
            <x-filament::section>
                <x-slot name="heading">
                    Reporting Form 2A Ready
                </x-slot>

                <x-slot name="description">
                    Preview opens in a modal. You can reopen it anytime and export to PDF.
                </x-slot>

                <div class="mb-2 flex flex-wrap items-center justify-end gap-2">
                    <x-filament::button
                        color="primary"
                        icon="heroicon-o-eye"
                        x-on:click="$dispatch('open-modal', { id: 'form-2a-preview-modal' })"
                    >
                        Open Preview
                    </x-filament::button>

                    {{ ($this->exportForm2APdfAction)([]) }}
                </div>
            </x-filament::section>

            <x-filament::modal id="form-2a-preview-modal" width="6xl" slide-over="false" :close-by-clicking-away="false">
                <x-slot name="heading">
                    Reporting Form 2A Preview
                </x-slot>

                <x-slot name="description">
                    Review the generated results first, then export to PDF.
                </x-slot>

                <div id="form-2a-preview" class="max-h-[75vh] overflow-auto">
                    @include('filament.pages.partials.form-2a-report', [
                        'report' => $this->form2AReport,
                        'forPdf' => false,
                    ])
                </div>

                <x-slot name="footerActions">
                    <x-filament::button color="gray" x-on:click="$dispatch('close-modal', { id: 'form-2a-preview-modal' })">
                        Close
                    </x-filament::button>

                    {{ ($this->exportForm2APdfAction)([]) }}
                </x-slot>
            </x-filament::modal>
        @endif

        @if (filled($this->form2BReport))
            <x-filament::section>
                <x-slot name="heading">
                    Reporting Form 2B Ready
                </x-slot>

                <x-slot name="description">
                    Preview opens in a modal. You can reopen it anytime and export to PDF.
                </x-slot>

                <div class="mb-2 flex flex-wrap items-center justify-end gap-2">
                    <x-filament::button
                        color="primary"
                        icon="heroicon-o-eye"
                        x-on:click="$dispatch('open-modal', { id: 'form-2b-preview-modal' })"
                    >
                        Open Preview
                    </x-filament::button>

                    {{ ($this->exportForm2BPdfAction)([]) }}
                </div>
            </x-filament::section>

            <x-filament::modal id="form-2b-preview-modal" width="6xl" slide-over="false" :close-by-clicking-away="false">
                <x-slot name="heading">
                    Reporting Form 2B Preview
                </x-slot>

                <x-slot name="description">
                    Review the generated results first, then export to PDF.
                </x-slot>

                <div id="form-2b-preview" class="max-h-[75vh] overflow-auto">
                    @include('filament.pages.partials.form-2b-report', [
                        'report' => $this->form2BReport,
                        'forPdf' => false,
                    ])
                </div>

                <x-slot name="footerActions">
                    <x-filament::button color="gray" x-on:click="$dispatch('close-modal', { id: 'form-2b-preview-modal' })">
                        Close
                    </x-filament::button>

                    {{ ($this->exportForm2BPdfAction)([]) }}
                </x-slot>
            </x-filament::modal>
        @endif

        @if (filled($this->form2CReport))
            <x-filament::section>
                <x-slot name="heading">
                    Reporting Form 2C Ready
                </x-slot>

                <x-slot name="description">
                    Preview opens in a modal. You can reopen it anytime and export to PDF.
                </x-slot>

                <div class="mb-2 flex flex-wrap items-center justify-end gap-2">
                    <x-filament::button
                        color="primary"
                        icon="heroicon-o-eye"
                        x-on:click="$dispatch('open-modal', { id: 'form-2c-preview-modal' })"
                    >
                        Open Preview
                    </x-filament::button>

                    {{ ($this->exportForm2CPdfAction)([]) }}
                </div>
            </x-filament::section>

            <x-filament::modal id="form-2c-preview-modal" width="6xl" slide-over="false" :close-by-clicking-away="false">
                <x-slot name="heading">
                    Reporting Form 2C Preview
                </x-slot>

                <x-slot name="description">
                    Review the generated results first, then export to PDF.
                </x-slot>

                <div id="form-2c-preview" class="max-h-[75vh] overflow-auto">
                    @include('filament.pages.partials.form-2c-report', [
                        'report' => $this->form2CReport,
                        'forPdf' => false,
                    ])
                </div>

                <x-slot name="footerActions">
                    <x-filament::button color="gray" x-on:click="$dispatch('close-modal', { id: 'form-2c-preview-modal' })">
                        Close
                    </x-filament::button>

                    {{ ($this->exportForm2CPdfAction)([]) }}
                </x-slot>
            </x-filament::modal>
        @endif

    </div>
</x-filament-panels::page>