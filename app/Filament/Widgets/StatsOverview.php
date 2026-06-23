<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\CancerDiagnoses\CancerDiagnoseResource;
use App\Filament\Resources\FormDemographics\FormDemographicsResource;
use App\Filament\Resources\PatientFollowUpForms\PatientFollowUpFormResource;
use App\Models\CancerDiagnose;
use App\Models\FormDemographics;
use App\Models\PatientFollowUpForm;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = -3;

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $patientCount = FormDemographics::query()->count();
        $diagnosisCount = CancerDiagnose::query()->count();
        $followUpCount = PatientFollowUpForm::query()->count();
        $ongoingFollowUpCount = PatientFollowUpForm::query()
            ->where('treatment_status', 'Ongoing')
            ->count();

        return [
            Stat::make('Patient Records', Number::format($patientCount))
                ->description(Number::format($diagnosisCount).' diagnosis records')
                ->descriptionIcon(Heroicon::OutlinedClipboardDocumentList)
                ->icon(Heroicon::OutlinedUsers)
                ->color('primary')
                ->url(FormDemographicsResource::getUrl('index')),

            Stat::make('Cancer Diagnoses', Number::format($diagnosisCount))
                ->description($this->averagePerPatient($diagnosisCount, $patientCount))
                ->descriptionIcon(Heroicon::OutlinedUsers)
                ->icon(Heroicon::OutlinedClipboardDocumentList)
                ->color('info')
                ->url(CancerDiagnoseResource::getUrl('index')),

            Stat::make('Follow-Up Forms', Number::format($followUpCount))
                ->description($this->averagePerPatient($followUpCount, $patientCount))
                ->descriptionIcon(Heroicon::OutlinedUsers)
                ->icon(Heroicon::OutlinedCalendarDateRange)
                ->color('success')
                ->url(PatientFollowUpFormResource::getUrl('index')),

            Stat::make('Ongoing Treatments', Number::format($ongoingFollowUpCount))
                ->description(Number::format($this->percentage($ongoingFollowUpCount, $followUpCount)).'% of follow-ups')
                ->descriptionIcon(Heroicon::OutlinedArrowPath)
                ->icon(Heroicon::OutlinedArrowPath)
                ->color('warning')
                ->url(PatientFollowUpFormResource::getUrl('index')),
        ];
    }

    private function percentage(int $count, int $total): int
    {
        if ($total === 0) {
            return 0;
        }

        return (int) round(($count / $total) * 100);
    }

    private function averagePerPatient(int $count, int $patientCount): string
    {
        if ($patientCount === 0) {
            return 'No patient baseline';
        }

        return Number::format($count / $patientCount, precision: 1, maxPrecision: 1).' per patient';
    }
}
