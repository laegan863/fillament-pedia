<?php

namespace App\Filament\Resources\CancerDiagnoses\Pages;

use App\Filament\Resources\CancerDiagnoses\CancerDiagnoseResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditCancerDiagnose extends EditRecord
{
    protected static string $resource = CancerDiagnoseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // DeleteAction::make(),
            Action::make('back')
            ->label('Return') 
            ->icon('heroicon-o-arrow-uturn-left')
            ->extraAttributes(['class' => 'me-auto flex items-center gap-1'])
            ->url(fn (): string => static::getResource()::getUrl('index', [
                'formId' => $this->record->form_demographic_id,
            ])),
        ];
    }
}
