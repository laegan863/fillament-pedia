@php
    $report = $report ?? [];
    $signatories = [
        ['label' => 'Prepared by:', 'name' => $report['prepared_by_name'] ?? '', 'position' => $report['prepared_by_position'] ?? ''],
        ['label' => 'Noted by:', 'name' => $report['noted_by_name'] ?? '', 'position' => $report['noted_by_position'] ?? ''],
    ];
@endphp

<div class="report-signatories" style="display: flex; justify-content: space-between; gap: 56px; padding-top: 40px; font-size: 13px; page-break-inside: avoid; break-inside: avoid;">
    @foreach ($signatories as $signatory)
        <div style="flex: 1; max-width: 340px;">
            <div style="font-weight: 600; margin-bottom: 32px;">{{ $signatory['label'] }}</div>
            <div style="text-align: center; font-weight: 700; text-transform: uppercase; min-height: 18px;">{{ $signatory['name'] }}</div>
            <div style="border-top: 1px solid #374151; margin-top: 2px;"></div>
            <div style="text-align: center; padding-top: 2px; font-weight: 600; color: #4b5563;">{{ $signatory['position'] }}</div>
        </div>
    @endforeach
</div>
