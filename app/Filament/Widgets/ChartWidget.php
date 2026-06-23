<?php

namespace App\Filament\Widgets;

use App\Models\FormDemographics;
use Filament\Widgets\ChartWidget as BaseChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ChartWidget extends BaseChartWidget
{
    protected ?string $heading = 'Monthly Patient Monitoring';

    protected ?string $description = 'Patient records by month, based on first encounter date.';

    protected string $color = 'primary';

    protected ?string $maxHeight = '320px';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = -1;

    protected function getData(): array
    {
        $months = collect(range(11, 1))
            ->map(fn (int $monthsAgo): Carbon => now()->startOfMonth()->subMonths($monthsAgo))
            ->push(now()->startOfMonth());

        $startDate = $months->first()->copy()->startOfMonth();
        $endDate = $months->last()->copy()->endOfMonth();
        $dateExpression = 'COALESCE(first_encounter_date, created_at)';
        $monthExpression = $this->monthExpression($dateExpression);

        $patientCounts = FormDemographics::query()
            ->selectRaw("{$monthExpression} as month_key")
            ->selectRaw('count(*) as patient_count')
            ->whereBetween(DB::raw($dateExpression), [$startDate, $endDate])
            ->groupBy('month_key')
            ->pluck('patient_count', 'month_key');

        return [
            'datasets' => [
                [
                    'label' => 'Patients',
                    'data' => $months
                        ->map(fn (Carbon $month): int => (int) ($patientCounts[$month->format('Y-m')] ?? 0))
                        ->all(),
                    'borderColor' => '#d97706',
                    'backgroundColor' => '#f59e0b',
                ],
            ],
            'labels' => $months
                ->map(fn (Carbon $month): string => $month->format('M Y'))
                ->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'tooltip' => [
                    'displayColors' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }

    private function monthExpression(string $dateExpression): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y-%m', {$dateExpression})",
            'pgsql' => "to_char({$dateExpression}, 'YYYY-MM')",
            'sqlsrv' => "FORMAT({$dateExpression}, 'yyyy-MM')",
            default => "DATE_FORMAT({$dateExpression}, '%Y-%m')",
        };
    }
}
