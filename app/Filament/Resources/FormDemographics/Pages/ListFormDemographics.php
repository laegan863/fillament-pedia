<?php

namespace App\Filament\Resources\FormDemographics\Pages;

use App\Filament\Resources\FormDemographics\FormDemographicsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFormDemographics extends ListRecords
{
    protected static string $resource = FormDemographicsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->icon('heroicon-o-arrow-uturn-left')
            ->label('Return')
            ->url(fn (): string => self::getResource()::getUrl('index',[
                'formId' => request()->query('formId'),
            ])),
            CreateAction::make(),
        ];
    }
}
