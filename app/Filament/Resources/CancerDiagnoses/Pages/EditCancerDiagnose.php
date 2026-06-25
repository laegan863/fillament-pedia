<?php

namespace App\Filament\Resources\CancerDiagnoses\Pages;

use App\Filament\Resources\CancerDiagnoses\CancerDiagnoseResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Arr;

class EditCancerDiagnose extends EditRecord
{
    protected static string $resource = CancerDiagnoseResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        foreach ([
            'metastasis_sites',
            'staging_used',
            'multidisciplinary_disciplines',
            'anti_cancer_drug_types',
            'other_cancer_directed_therapy_types',
        ] as $field) {
            $data[$field] = array_values(array_filter(
                Arr::wrap($data[$field] ?? []),
                fn (mixed $value): bool => filled($value),
            ));
        }

        return $data;
    }

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
