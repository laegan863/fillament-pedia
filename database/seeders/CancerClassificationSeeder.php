<?php

namespace Database\Seeders;

use App\Models\CancerDiagnose;
use App\Models\GeneralClassification;
use App\Models\ParentClassification;
use App\Models\SpecificClassification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CancerClassificationSeeder extends Seeder
{
    /**
     * Clear the general, parent and specific classifications and reseed them from the data file.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/cancer_classifications.php');

        if (! file_exists($path)) {
            throw new RuntimeException("Classification seed file not found: {$path}");
        }

        /** @var array<int, array{general: string, parent: string, specific: string}> $classifications */
        $classifications = require $path;

        DB::transaction(function () use ($classifications): void {
            $this->clearClassifications();

            /** @var array<string, int> $generalClassificationIds */
            $generalClassificationIds = [];

            /** @var array<string, int> $parentClassificationIds */
            $parentClassificationIds = [];

            foreach ($classifications as $index => $classification) {
                $generalClassificationIds[$classification['general']] ??= GeneralClassification::query()
                    ->create(['name' => $classification['general']])
                    ->id;

                $parentClassificationIds[$classification['parent']] ??= ParentClassification::query()
                    ->create(['name' => $classification['parent']])
                    ->id;

                SpecificClassification::query()->create([
                    'name' => $classification['specific'],
                    'source_row' => $index + 1,
                    'general_classification_id' => $generalClassificationIds[$classification['general']],
                    'parent_classification_id' => $parentClassificationIds[$classification['parent']],
                ]);
            }

            $this->backfillCancerDiagnoses();
        });
    }

    /**
     * Permanently remove every classification, including soft-deleted ones.
     */
    private function clearClassifications(): void
    {
        SpecificClassification::withTrashed()->forceDelete();
        ParentClassification::withTrashed()->forceDelete();
        GeneralClassification::withTrashed()->forceDelete();
    }

    /**
     * Relink existing cancer diagnoses to the new specific classifications by name.
     */
    private function backfillCancerDiagnoses(): void
    {
        $specificClassificationIds = SpecificClassification::query()
            ->pluck('id', 'name');

        CancerDiagnose::query()
            ->whereNotNull('iccc_specific_classification')
            ->lazyById()
            ->each(function (CancerDiagnose $cancerDiagnose) use ($specificClassificationIds): void {
                $cancerDiagnose
                    ->forceFill([
                        'specific_classification_id' => $specificClassificationIds[$cancerDiagnose->iccc_specific_classification] ?? null,
                    ])
                    ->save();
            });
    }
}
