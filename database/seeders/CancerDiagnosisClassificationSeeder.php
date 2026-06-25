<?php

namespace Database\Seeders;

use App\Models\CancerDiagnose;
use App\Models\GeneralClassification;
use App\Models\ParentClassification;
use App\Models\SpecificClassification;
use Illuminate\Database\Seeder;
use RuntimeException;

class CancerDiagnosisClassificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/specific_classification_valid_options.csv');

        if (! file_exists($path)) {
            throw new RuntimeException("Classification seed file not found: {$path}");
        }

        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new RuntimeException("Unable to open classification seed file: {$path}");
        }

        $headers = fgetcsv($handle);

        if ($headers === false) {
            fclose($handle);

            throw new RuntimeException("Classification seed file is empty: {$path}");
        }

        while (($row = fgetcsv($handle)) !== false) {
            $record = array_combine($headers, $row);

            if ($record === false) {
                continue;
            }

            $specificClassification = trim((string) ($record['Specific Classification'] ?? ''));

            if ($specificClassification === '') {
                continue;
            }

            $generalClassification = $this->findOrCreateGeneralClassification(
                trim((string) ($record['General Classification'] ?? '')),
            );

            $parentClassification = trim((string) ($record['Parent Classification'] ?? ''));
            $parentClassification = $parentClassification === '-----' || $parentClassification === ''
                ? null
                : $this->findOrCreateParentClassification($parentClassification);

            $specific = SpecificClassification::withTrashed()->firstOrNew([
                'name' => $specificClassification,
            ]);

            if ($specific->trashed()) {
                $specific->restore();
            }

            $specific->fill([
                'source_row' => (int) ($record['Source Row'] ?? 0) ?: null,
                'general_classification_id' => $generalClassification->id,
                'parent_classification_id' => $parentClassification?->id,
            ])->save();
        }

        fclose($handle);

        $this->backfillCancerDiagnoses();
    }

    private function findOrCreateGeneralClassification(string $name): GeneralClassification
    {
        $classification = GeneralClassification::withTrashed()->firstOrNew([
            'name' => $name,
        ]);

        if ($classification->trashed()) {
            $classification->restore();
        }

        if (! $classification->exists) {
            $classification->save();
        }

        return $classification;
    }

    private function findOrCreateParentClassification(string $name): ParentClassification
    {
        $classification = ParentClassification::withTrashed()->firstOrNew([
            'name' => $name,
        ]);

        if ($classification->trashed()) {
            $classification->restore();
        }

        if (! $classification->exists) {
            $classification->save();
        }

        return $classification;
    }

    private function backfillCancerDiagnoses(): void
    {
        $specificClassificationIds = SpecificClassification::query()
            ->pluck('id', 'name');

        CancerDiagnose::query()
            ->whereNotNull('iccc_specific_classification')
            ->lazyById()
            ->each(function (CancerDiagnose $cancerDiagnose) use ($specificClassificationIds): void {
                $specificClassificationId = $specificClassificationIds[$cancerDiagnose->iccc_specific_classification] ?? null;

                if ($specificClassificationId === null) {
                    return;
                }

                if ($cancerDiagnose->specific_classification_id === (int) $specificClassificationId) {
                    return;
                }

                $cancerDiagnose
                    ->forceFill(['specific_classification_id' => $specificClassificationId])
                    ->save();
            });
    }
}
