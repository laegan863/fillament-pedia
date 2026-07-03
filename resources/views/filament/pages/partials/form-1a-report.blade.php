@php
    $report = $report ?? [];
    $stages = $report['stages'] ?? [];
    $groups = $report['groups'] ?? [];
    $analytics = $report['analytics'] ?? [];
    $stageTotals = $analytics['stage_totals'] ?? [];
    $topClassifications = $analytics['top_classifications'] ?? [];
    $maxTopClassificationTotal = collect($topClassifications)->max('total') ?: 1;
    $maxStageTotal = collect($stageTotals)->max('T') ?: 1;
    $forPdf = $forPdf ?? false;
    $fromDateCarbon = \Illuminate\Support\Carbon::parse($report['from_date'] ?? now()->toDateString());
    $quarter = (int) ceil($fromDateCarbon->month / 3);
    $year = $fromDateCarbon->year;
@endphp

<div class="space-y-4 {{ $forPdf ? 'text-[11px] text-black' : 'text-sm text-gray-800 dark:text-gray-100' }}" style="padding: 20px;">
    @if ($forPdf)
        <style>
            .pdf-report-table thead {
                display: table-header-group;
            }

            .pdf-report-table tr,
            .pdf-report-table td,
            .pdf-report-table th {
                page-break-inside: avoid;
                break-inside: avoid;
            }

            .pdf-report-table td,
            .pdf-report-table th {
                word-break: keep-all;
                overflow-wrap: anywhere;
            }
        </style>
    @endif

    @if ($forPdf)
        {{-- PDF: PCC Official Letterhead --}}
        <div style="text-align: center; margin-bottom: 14px; font-family: Arial, sans-serif; line-height: 1.5;">
            <div style="font-size: 11px;">Republic of the Philippines</div>
            <div style="font-size: 11px;">Department of Health</div>
            <div style="font-size: 14px; font-weight: bold;">Philippine Cancer Center</div>
            <div style="font-size: 16px; font-weight: bold; margin-top: 6px;">
                PCC HOSPITAL-BASED CANCER REGISTRY REPORTING FORMS
            </div>
            <div style="font-size: 11px; font-style: italic;">(Cancer Treatment Facility - Based)</div>
        </div>

        <div style="font-size: 10px; margin-bottom: 10px; line-height: 1.5;padding: 10px">
            a. Only newly diagnosed cancer cases shall be recorded within the designated reporting period.<br>
            b. Count only clinical staging
        </div>

        <div style="background-color: #1f2937; color: #ffffff; padding: 7px 10px; font-weight: bold; font-size: 11px; margin-bottom: 6px;">
            REPORTING FORM 1A: Number of new Cancer Cases by International Classification of Childhood Cancer, Sex and Initial Clinical Staging
        </div>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 6px; font-size: 10px;">
            <tr>
                <td style="border: 1px solid #555; padding: 5px 8px; font-weight: bold; width: 15%;">Quarter</td>
                <td style="border: 1px solid #555; padding: 5px 10px; width: 35%;">
                    @for ($q = 1; $q <= 4; $q++)
                        <span style="margin-right: 12px;">{{ $quarter === $q ? '☑' : '☐' }} {{ $q }}</span>
                    @endfor
                </td>
                <td style="border: 1px solid #555; padding: 5px 8px; font-weight: bold; width: 8%;">Year</td>
                <td style="border: 1px solid #555; padding: 5px 8px; width: 10%;">{{ $year }}</td>
                <td style="border: 1px solid #555; padding: 5px 8px; font-weight: bold; width: 18%;">Report Submission Date</td>
                <td style="border: 1px solid #555; padding: 5px 8px;">{{ $report['report_submission_date'] ?? '' }}</td>
            </tr>
        </table>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 10px;">
            <tr>
                <td style="border: 1px solid #555; padding: 5px 8px; font-weight: bold; width: 28%;">Health Facility Name</td>
                <td style="border: 1px solid #555; padding: 5px 8px;">{{ $report['facility_name'] ?? '' }}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #555; padding: 5px 8px; font-weight: bold;">Health Facility Registration No.</td>
                <td style="border: 1px solid #555; padding: 5px 8px;">{{ $report['facility_registration_no'] ?? '' }}</td>
            </tr>
        </table>
    @else
        {{-- Preview: Simple header --}}
        <div class="border border-gray-300 bg-gray-100 p-2 font-semibold dark:border-gray-700 dark:bg-gray-800">
            {{ $report['form'] ?? 'Reporting Form 1A' }}: {{ $report['title'] ?? '' }}
        </div>

        <div class="grid grid-cols-4 gap-2">
            <div class="border border-gray-300 p-2 dark:border-gray-700">
                <span class="font-semibold">Date From:</span>
                {{ $report['from_date'] ?? '' }}
            </div>
            <div class="border border-gray-300 p-2 dark:border-gray-700">
                <span class="font-semibold">Date To:</span>
                {{ $report['to_date'] ?? '' }}
            </div>
            <div class="border border-gray-300 p-2 dark:border-gray-700">
                <span class="font-semibold">Facility:</span>
                {{ $report['facility_name'] ?? '' }}
            </div>
            <div class="border border-gray-300 p-2 dark:border-gray-700">
                <span class="font-semibold">Reg. No.:</span>
                {{ $report['facility_registration_no'] ?? '' }}
            </div>
        </div>
    @endif

    @if (! $forPdf)
    <div class="space-y-3">
        <div class="grid gap-2 md:grid-cols-3">
            <div class="rounded border border-gray-300 bg-white p-3 {{ $forPdf ? '' : 'dark:border-gray-700 dark:bg-gray-900' }}">
                <p class="text-[11px] uppercase tracking-wide text-gray-500 {{ $forPdf ? '' : 'dark:text-gray-400' }}">Total Cases</p>
                <p class="mt-1 text-lg font-bold">{{ $analytics['total_cases'] ?? 0 }}</p>
            </div>
            <div class="rounded border border-gray-300 bg-white p-3 {{ $forPdf ? '' : 'dark:border-gray-700 dark:bg-gray-900' }}">
                <p class="text-[11px] uppercase tracking-wide text-gray-500 {{ $forPdf ? '' : 'dark:text-gray-400' }}">Male Cases</p>
                <p class="mt-1 text-lg font-bold">{{ $analytics['male_cases'] ?? 0 }}</p>
            </div>
            <div class="rounded border border-gray-300 bg-white p-3 {{ $forPdf ? '' : 'dark:border-gray-700 dark:bg-gray-900' }}">
                <p class="text-[11px] uppercase tracking-wide text-gray-500 {{ $forPdf ? '' : 'dark:text-gray-400' }}">Female Cases</p>
                <p class="mt-1 text-lg font-bold">{{ $analytics['female_cases'] ?? 0 }}</p>
            </div>
        </div>

        <div class="grid gap-3 lg:grid-cols-2">
            <div class="rounded border border-gray-300 bg-white p-3 {{ $forPdf ? '' : 'dark:border-gray-700 dark:bg-gray-900' }}">
                <p class="mb-2 font-semibold">Stage Distribution</p>

                <div class="space-y-2">
                    @foreach ($stages as $stage)
                        @php
                            $value = (int) ($stageTotals[$stage]['T'] ?? 0);
                            $width = (int) round(($value / $maxStageTotal) * 100);
                        @endphp

                        <div>
                            <div class="mb-1 flex items-center justify-between text-[11px]">
                                <span>{{ $stage }}</span>
                                <span class="font-semibold">{{ $value }}</span>
                            </div>
                            <div class="h-2.5 rounded bg-gray-200 {{ $forPdf ? '' : 'dark:bg-gray-700' }}">
                                <div class="h-2.5 rounded bg-primary-500" style="width: {{ $width }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded border border-gray-300 bg-white p-3 {{ $forPdf ? '' : 'dark:border-gray-700 dark:bg-gray-900' }}">
                <p class="mb-2 font-semibold">Top Classifications</p>

                <div class="space-y-2">
                    @forelse ($topClassifications as $item)
                        @php
                            $value = (int) ($item['total'] ?? 0);
                            $width = (int) round(($value / $maxTopClassificationTotal) * 100);
                        @endphp

                        <div>
                            <div class="mb-1 flex items-center justify-between text-[11px]">
                                <span class="truncate pr-2">{{ $item['name'] ?? '' }}</span>
                                <span class="font-semibold">{{ $value }}</span>
                            </div>
                            <div class="h-2.5 rounded bg-gray-200 {{ $forPdf ? '' : 'dark:bg-gray-700' }}">
                                <div class="h-2.5 rounded bg-success-500" style="width: {{ $width }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-[11px] text-gray-500 {{ $forPdf ? '' : 'dark:text-gray-400' }}">No classification counts yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @endif

    <table class="pdf-report-table w-full border-collapse {{ $forPdf ? 'text-[10px]' : 'text-xs' }}">
        <thead>
            <tr>
                <th rowspan="2" class="border border-gray-500 bg-gray-200 px-2 py-1.5 text-left font-semibold {{ $forPdf ? '' : 'sticky top-0 z-30' }}">CANCER TREATMENT</th>
                @foreach ($stages as $stage)
                    <th colspan="3" class="border border-gray-500 bg-gray-200 px-2 py-1.5 text-center font-semibold {{ $forPdf ? '' : 'sticky top-0 z-20' }}">{{ $stage }}</th>
                @endforeach
                <th rowspan="2" class="border border-gray-500 bg-gray-200 px-2 py-1.5 text-center font-semibold {{ $forPdf ? '' : 'sticky top-0 z-30' }}">TOTAL CASES</th>
            </tr>
            <tr>
                @foreach ($stages as $stage)
                    <th class="border border-gray-500 bg-gray-100 px-2 py-1.5 text-center {{ $forPdf ? '' : 'sticky top-8 z-10' }}">M</th>
                    <th class="border border-gray-500 bg-gray-100 px-2 py-1.5 text-center {{ $forPdf ? '' : 'sticky top-8 z-10' }}">F</th>
                    <th class="border border-gray-500 bg-gray-100 px-2 py-1.5 text-center {{ $forPdf ? '' : 'sticky top-8 z-10' }}">T</th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @foreach ($groups as $groupIndex => $group)
                <tr class="bg-gray-100 font-semibold {{ $forPdf ? '' : 'dark:bg-gray-800' }}">
                    <td class="border border-gray-500 px-2 py-1.5">
                        {{ $groupIndex + 1 }}. {{ $group['name'] }}
                    </td>

                    @foreach ($stages as $stage)
                        <td class="border border-gray-500 px-2 py-1.5 text-center">{{ $group['totals']['stages'][$stage]['M'] ?? 0 }}</td>
                        <td class="border border-gray-500 px-2 py-1.5 text-center">{{ $group['totals']['stages'][$stage]['F'] ?? 0 }}</td>
                        <td class="border border-gray-500 px-2 py-1.5 text-center">{{ $group['totals']['stages'][$stage]['T'] ?? 0 }}</td>
                    @endforeach

                    <td class="border border-gray-500 px-2 py-1.5 text-center">{{ $group['totals']['total_cases'] ?? 0 }}</td>
                </tr>

                @foreach ($group['rows'] as $rowIndex => $row)
                    <tr>
                        <td class="border border-gray-400 px-2 py-1.5 pl-5">
                            {{ $groupIndex + 1 }}.{{ $rowIndex + 1 }}
                            @if (! empty($row['parent']))
                                {{ $row['parent'] }} -
                            @endif
                            {{ $row['name'] }}
                        </td>

                        @foreach ($stages as $stage)
                            <td class="border border-gray-400 px-2 py-1.5 text-center">{{ $row['totals']['stages'][$stage]['M'] ?? 0 }}</td>
                            <td class="border border-gray-400 px-2 py-1.5 text-center">{{ $row['totals']['stages'][$stage]['F'] ?? 0 }}</td>
                            <td class="border border-gray-400 px-2 py-1.5 text-center">{{ $row['totals']['stages'][$stage]['T'] ?? 0 }}</td>
                        @endforeach

                        <td class="border border-gray-400 px-2 py-1.5 text-center">{{ $row['totals']['total_cases'] ?? 0 }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>
