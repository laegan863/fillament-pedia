<?php

namespace App\Filament\Resources\FormDemographics\Pages;

use App\Filament\Resources\FormDemographics\FormDemographicsResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditFormDemographics extends EditRecord
{
    protected static string $resource = FormDemographicsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // DeleteAction::make(),
            Action::make('back')
            ->label('Back to patient demographics') 
            ->icon('heroicon-o-arrow-left')
            ->extraAttributes(['class' => 'me-auto'])
            ->url(fn (): string => static::getResource()::getUrl('index')),
        ];
    }
}
