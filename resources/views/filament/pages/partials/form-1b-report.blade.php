@php
    $report = $report ?? [];
    $forPdf = $forPdf ?? false;
    $rows = $report['rows'] ?? [];
    $fromDateCarbon = \Illuminate\Support\Carbon::parse($report['from_date'] ?? now()->toDateString());
    $quarter = (int) ceil($fromDateCarbon->month / 3);
    $year = $fromDateCarbon->year;
@endphp

<div class="space-y-4 {{ $forPdf ? 'text-[11px] text-black' : 'text-sm text-gray-800 dark:text-gray-100' }}" style="padding: 20px;">
    <div style="text-align: center; margin-bottom: 10px; line-height: 1.4;">
        <div class="text-[11px]">Republic of the Philippines</div>
        <div class="text-[11px]">Department of Health</div>
        <div class="text-[13px] font-bold">Philippine Cancer Center</div>
        <div class="text-[20px] font-bold">PCC HOSPITAL-BASED CANCER REGISTRY REPORTING FORMS</div>
        <div class="text-[11px] italic">(Cancer Treatment Facility - Based)</div>
    </div>

    <div class="border border-amber-300 bg-amber-100 px-3 py-1.5 text-[11px]">
        a. Only newly diagnosed cancer cases shall be recorded within the designated reporting period.
    </div>

    <div class="bg-gray-950 px-3 py-1.5 text-[15px] font-bold text-white">
        REPORTING FORM 1B: NEW CANCER CASES UNDER THE GLOBAL INITIATIVE FOR CHILDHOOD CANCER INDEX
    </div>

    <table class="w-full border-collapse text-[13px]">
        <tr>
            <td class="border border-gray-700 px-2 py-1 font-semibold">Quarter</td>
            <td class="border border-gray-700 px-2 py-1">
                @for ($q = 1; $q <= 4; $q++)
                    <span class="mr-4">{{ $quarter === $q ? '[x]' : '[ ]' }} {{ $q }}</span>
                @endfor
            </td>
            <td class="border border-gray-700 px-2 py-1 font-semibold">Year</td>
            <td class="border border-gray-700 px-2 py-1 font-semibold">{{ $year }}</td>
            <td class="border border-gray-700 px-2 py-1 font-semibold">Date Report Submitted</td>
            <td class="border border-gray-700 px-2 py-1 font-semibold">{{ $report['report_submission_date'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="border border-gray-700 px-2 py-1 font-semibold">Health Facility Name</td>
            <td colspan="5" class="border border-gray-700 px-2 py-1 text-center font-semibold uppercase">
                {{ $report['facility_name'] ?? '' }}
            </td>
        </tr>
        <tr>
            <td class="border border-gray-700 px-2 py-1 font-semibold">Health Facility Registry Code</td>
            <td colspan="5" class="border border-gray-700 px-2 py-1 text-center font-semibold">
                {{ $report['facility_registration_no'] ?? '' }}
            </td>
        </tr>
    </table>

    <table class="w-full border-collapse text-[13px]">
        <thead>
            <tr>
                <th class="border border-gray-700 bg-gray-500 px-2 py-1.5 text-left text-white">TREATMENT</th>
                <th class="border border-gray-700 bg-gray-500 px-2 py-1.5 text-center text-white">MALE</th>
                <th class="border border-gray-700 bg-gray-500 px-2 py-1.5 text-center text-white">FEMALE</th>
                <th class="border border-gray-700 bg-gray-500 px-2 py-1.5 text-center text-white">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    <td class="border border-gray-700 px-2 py-1.5">{{ $row['no'] }} {{ $row['label'] }}</td>
                    <td class="border border-gray-700 px-2 py-1.5 text-center">{{ $row['male'] }}</td>
                    <td class="border border-gray-700 px-2 py-1.5 text-center">{{ $row['female'] }}</td>
                    <td class="border border-gray-700 px-2 py-1.5 text-center font-semibold">{{ $row['total'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="grid grid-cols-2 gap-14 pt-10 text-center text-[13px]">
        <div>
            <div class="mb-8 text-left font-semibold">Prepared by:</div>
            <div class="border-t border-gray-700 pt-1 font-semibold">{{ $report['prepared_by_name'] ?? '' }}</div>
            <div class="font-semibold text-gray-600">{{ $report['prepared_by_position'] ?? '' }}</div>
        </div>

        <div>
            <div class="mb-8 text-left font-semibold">Noted by:</div>
            <div class="border-t border-gray-700 pt-1 font-semibold">{{ $report['noted_by_name'] ?? '' }}</div>
            <div class="font-semibold text-gray-600">{{ $report['noted_by_position'] ?? '' }}</div>
        </div>
    </div>
</div>
