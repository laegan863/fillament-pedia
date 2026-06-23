<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\CancerDiagnoses\CancerDiagnoseResource;
use App\Filament\Resources\FormDemographics\FormDemographicsResource;
use App\Filament\Resources\PatientFollowUpForms\PatientFollowUpFormResource;
use App\Models\CancerDiagnose;
use App\Models\FormDemographics;
use App\Models\PatientFollowUpForm;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;

class DashboardWidget extends Widget
{
    protected string $view = 'filament.widgets.dashboard-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = -2;

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $patientCount = FormDemographics::query()->count();
        $diagnosisCount = CancerDiagnose::query()->count();
        $followUpCount = PatientFollowUpForm::query()->count();
        $diagnosisClassifications = $this->groupedCounts(CancerDiagnose::class, 'iccc_general_classification');
        $treatmentStatuses = $this->groupedCounts(PatientFollowUpForm::class, 'treatment_status');
        $diseaseOutcomes = $this->groupedCounts(PatientFollowUpForm::class, 'disease_outcome');
        $treatmentCoverage = $this->treatmentCoverage($diagnosisCount);

        return [
            'clinicalBarChart' => $this->clinicalBarChart(
                $diagnosisClassifications,
                $treatmentStatuses,
                $diseaseOutcomes,
            ),
            'diagnosisClassifications' => $diagnosisClassifications,
            'treatmentStatuses' => $treatmentStatuses,
            'diseaseOutcomes' => $diseaseOutcomes,
            'treatmentCoverage' => $treatmentCoverage,
            'treatmentPieChart' => $this->treatmentPieChart($treatmentCoverage),
            'recentFollowUps' => $this->recentFollowUps(),
            'hasDashboardData' => ($patientCount + $diagnosisCount + $followUpCount) > 0,
            'links' => [
                'patients' => FormDemographicsResource::getUrl('index'),
                'diagnoses' => CancerDiagnoseResource::getUrl('index'),
                'followUps' => PatientFollowUpFormResource::getUrl('index'),
            ],
        ];
    }

    /**
     * @param  class-string<Model>  $model
     * @return array<int, array{label: string, count: int, formattedCount: string, percentage: int}>
     */
    private function groupedCounts(string $model, string $column, int $limit = 5): array
    {
        $total = $model::query()
            ->whereNotNull($column)
            ->count();

        if ($total === 0) {
            return [];
        }

        return $model::query()
            ->whereNotNull($column)
            ->select($column)
            ->selectRaw('count(*) as total')
            ->groupBy($column)
            ->orderByDesc('total')
            ->limit($limit)
            ->pluck('total', $column)
            ->map(fn (int|string $count, int|string $label): array => [
                'label' => filled($label) ? (string) $label : 'Not specified',
                'count' => (int) $count,
                'formattedCount' => Number::format((int) $count),
                'percentage' => $this->percentage((int) $count, $total),
            ])
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array{label: string, count: int, formattedCount: string, percentage: int}>  $diagnosisClassifications
     * @param  array<int, array{label: string, count: int, formattedCount: string, percentage: int}>  $treatmentStatuses
     * @param  array<int, array{label: string, count: int, formattedCount: string, percentage: int}>  $diseaseOutcomes
     * @return array{ticks: array<int, array{label: string, y: int}>, bars: array<int, array<string, int|string>>}
     */
    private function clinicalBarChart(array $diagnosisClassifications, array $treatmentStatuses, array $diseaseOutcomes): array
    {
        $sourceBars = [
            [
                'label' => 'ICCC',
                'fullLabel' => 'ICCC Classification',
                'detail' => $diagnosisClassifications[0]['label'] ?? 'No values recorded',
                'count' => $diagnosisClassifications[0]['count'] ?? 0,
                'color' => '#d97706',
            ],
            [
                'label' => 'Status',
                'fullLabel' => 'Treatment Status',
                'detail' => $treatmentStatuses[0]['label'] ?? 'No values recorded',
                'count' => $treatmentStatuses[0]['count'] ?? 0,
                'color' => '#2563eb',
            ],
            [
                'label' => 'Outcome',
                'fullLabel' => 'Disease Outcome',
                'detail' => $diseaseOutcomes[0]['label'] ?? 'No values recorded',
                'count' => $diseaseOutcomes[0]['count'] ?? 0,
                'color' => '#059669',
            ],
        ];

        $maxCount = max(collect($sourceBars)->pluck('count')->max() ?? 0, 1);
        $chartTop = 38;
        $chartHeight = 132;
        $baseline = $chartTop + $chartHeight;

        return [
            'ticks' => [
                ['label' => (string) $maxCount, 'y' => $chartTop],
                ['label' => Number::format($maxCount * 0.75, maxPrecision: 1), 'y' => $chartTop + 33],
                ['label' => Number::format($maxCount * 0.5, maxPrecision: 1), 'y' => $chartTop + 66],
                ['label' => Number::format($maxCount * 0.25, maxPrecision: 1), 'y' => $chartTop + 99],
                ['label' => '0', 'y' => $baseline],
            ],
            'bars' => collect($sourceBars)
                ->values()
                ->map(function (array $bar, int $index) use ($baseline, $chartHeight, $maxCount): array {
                    $barWidth = 54;
                    $barHeight = $bar['count'] > 0
                        ? max((int) round(($bar['count'] / $maxCount) * $chartHeight), 8)
                        : 0;
                    $barX = 70 + ($index * 92);

                    return [
                        'label' => $bar['label'],
                        'fullLabel' => $bar['fullLabel'],
                        'detail' => $bar['detail'],
                        'count' => $bar['count'],
                        'formattedCount' => Number::format($bar['count']),
                        'color' => $bar['color'],
                        'x' => $barX,
                        'y' => $baseline - $barHeight,
                        'width' => $barWidth,
                        'height' => $barHeight,
                        'center' => $barX + (int) ($barWidth / 2),
                    ];
                })
                ->all(),
        ];
    }

    /**
     * @return array<int, array{label: string, count: int, formattedCount: string, percentage: int, icon: Heroicon}>
     */
    private function treatmentCoverage(int $diagnosisCount): array
    {
        return collect([
            ['label' => 'Surgery', 'column' => 'has_surgery', 'icon' => Heroicon::OutlinedScissors],
            ['label' => 'Anti-Cancer Drug', 'column' => 'has_anti_cancer_drug', 'icon' => Heroicon::OutlinedBeaker],
            ['label' => 'Radiotherapy', 'column' => 'has_radiotherapy', 'icon' => Heroicon::OutlinedBolt],
            ['label' => 'Theranostics', 'column' => 'has_theranostics', 'icon' => Heroicon::OutlinedSparkles],
            ['label' => 'Palliative Care', 'column' => 'has_palliative_care', 'icon' => Heroicon::OutlinedHeart],
            ['label' => 'Other Therapies', 'column' => 'has_other_cancer_directed_therapy', 'icon' => Heroicon::OutlinedSquaresPlus],
        ])
            ->map(function (array $treatment) use ($diagnosisCount): array {
                $count = CancerDiagnose::query()
                    ->where($treatment['column'], true)
                    ->count();

                return [
                    'label' => $treatment['label'],
                    'count' => $count,
                    'formattedCount' => Number::format($count),
                    'percentage' => $this->percentage($count, $diagnosisCount),
                    'icon' => $treatment['icon'],
                ];
            })
            ->all();
    }

    /**
     * @param  array<int, array{label: string, count: int, formattedCount: string, percentage: int, icon: Heroicon}>  $treatmentCoverage
     * @return array{total: int, formattedTotal: string, segments: array<int, array<string, float|int|string|Heroicon>>}
     */
    private function treatmentPieChart(array $treatmentCoverage): array
    {
        $colors = ['#059669', '#0d9488', '#2563eb', '#7c3aed', '#d97706', '#dc2626'];
        $total = collect($treatmentCoverage)->sum('count');
        $offset = 0.0;

        return [
            'total' => $total,
            'formattedTotal' => Number::format($total),
            'segments' => collect($treatmentCoverage)
                ->values()
                ->map(function (array $treatment, int $index) use ($colors, $total, &$offset): array {
                    $share = $total > 0 ? round(($treatment['count'] / $total) * 100, 2) : 0.0;
                    $segment = [
                        'label' => $treatment['label'],
                        'count' => $treatment['count'],
                        'formattedCount' => $treatment['formattedCount'],
                        'percentage' => $share,
                        'formattedPercentage' => Number::format($share, maxPrecision: 1).'%',
                        'offset' => $offset,
                        'color' => $colors[$index % count($colors)],
                        'icon' => $treatment['icon'],
                    ];

                    $offset += $share;

                    return $segment;
                })
                ->all(),
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function recentFollowUps(): array
    {
        return PatientFollowUpForm::query()
            ->with([
                'formDemographic:id,health_facility_id_no,patient_first_name,patient_middle_name,patient_surname,patient_suffix',
            ])
            ->select([
                'id',
                'form_demographic_id',
                'patient_health_facility_id_no',
                'follow_up_last_encounter_date',
                'treatment_status',
                'disease_outcome',
                'created_at',
            ])
            ->orderByDesc('follow_up_last_encounter_date')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function (PatientFollowUpForm $followUp): array {
                $patientId = $followUp->formDemographic?->health_facility_id_no
                    ?: $followUp->patient_health_facility_id_no;

                return [
                    'patient' => $followUp->formDemographic?->full_name ?: 'Patient #'.$followUp->id,
                    'patientId' => filled($patientId) ? str_pad((string) $patientId, 10, '0', STR_PAD_LEFT) : 'No patient ID',
                    'encounterDate' => $followUp->follow_up_last_encounter_date?->format('M j, Y')
                        ?: $followUp->created_at?->format('M j, Y')
                        ?: 'No date',
                    'treatmentStatus' => $followUp->treatment_status ?: 'No status',
                    'diseaseOutcome' => $followUp->disease_outcome ?: 'No outcome',
                    'url' => PatientFollowUpFormResource::getUrl('edit', ['record' => $followUp]),
                ];
            })
            ->all();
    }

    private function percentage(int $count, int $total): int
    {
        if ($total === 0) {
            return 0;
        }

        return (int) round(($count / $total) * 100);
    }
}
