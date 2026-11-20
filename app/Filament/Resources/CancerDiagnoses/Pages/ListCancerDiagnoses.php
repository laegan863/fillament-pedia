<?php

namespace App\Filament\Resources\CancerDiagnoses\Pages;

use App\Filament\Resources\CancerDiagnoses\CancerDiagnoseResource;
use App\Filament\Resources\FormDemographics\FormDemographicsResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCancerDiagnoses extends ListRecords
{
    protected static string $resource = CancerDiagnoseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Return')
                ->icon('heroicon-o-arrow-uturn-left')
                ->extraAttributes(['class' => 'me-auto'])
                ->url(fn (): string => FormDemographicsResource::getUrl('index')),

            CreateAction::make()->label('New cancer diagnosis')
                ->url(fn (): string => static::getResource()::getUrl('create', [
                    'formId' => request()->query('formId'),
                ])),
        ];
    }
}
