<?php

namespace App\Filament\Resources\FormDemographics\Pages;

use App\Filament\Resources\FormDemographics\FormDemographicsResource;
use App\Filament\Resources\CancerDiagnoses\CancerDiagnoseResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\MaxWidth;
use Filament\Actions\Action;


class CreateFormDemographics extends CreateRecord
{
    protected static string $resource = FormDemographicsResource::class;

    protected function getRedirectUrl(): string
    {
        return CancerDiagnoseResource::getUrl('index', ['formId' => $this->record->id]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
            ->label('Return') 
            ->icon('heroicon-o-arrow-uturn-left')
            ->extraAttributes(['class' => 'me-auto'])
            ->url(fn (): string => static::getResource()::getUrl('index')),
        ];
    }
}
