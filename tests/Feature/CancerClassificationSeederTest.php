<?php

use App\Models\CancerDiagnose;
use App\Models\GeneralClassification;
use App\Models\ParentClassification;
use App\Models\SpecificClassification;
use Database\Seeders\CancerClassificationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('classifications are seeded from the data file with every level filled', function (): void {
    $this->seed(CancerClassificationSeeder::class);

    $acuteMyeloidLeukemia = SpecificClassification::query()
        ->where('name', 'Acute Myeloid Leukemia')
        ->firstOrFail();

    expect(GeneralClassification::query()->count())->toBe(12)
        ->and(ParentClassification::query()->count())->toBe(27)
        ->and(SpecificClassification::query()->count())->toBe(116)
        ->and(SpecificClassification::query()->whereNull('parent_classification_id')->count())->toBe(0)
        ->and($acuteMyeloidLeukemia->source_row)->toBe(5)
        ->and($acuteMyeloidLeukemia->parentClassification->name)
        ->toBe('Leukemias, Myeloproliferative Diseases, Myelodysplastic Disease')
        ->and($acuteMyeloidLeukemia->generalClassification->name)
        ->toBe('Leukemias, Myeloproliferative Diseases, Myelodysplastic Disease');
});

test('seeder clears existing classifications before seeding', function (): void {
    $staleGeneral = GeneralClassification::query()->create(['name' => 'Stale General']);
    $staleParent = ParentClassification::query()->create(['name' => 'Stale Parent']);
    SpecificClassification::query()->create([
        'name' => 'Stale Specific',
        'general_classification_id' => $staleGeneral->id,
        'parent_classification_id' => $staleParent->id,
    ])->delete();

    $this->seed(CancerClassificationSeeder::class);
    $this->seed(CancerClassificationSeeder::class);

    expect(SpecificClassification::withTrashed()->where('name', 'Stale Specific')->exists())->toBeFalse()
        ->and(ParentClassification::withTrashed()->where('name', 'Stale Parent')->exists())->toBeFalse()
        ->and(GeneralClassification::withTrashed()->where('name', 'Stale General')->exists())->toBeFalse()
        ->and(SpecificClassification::withTrashed()->count())->toBe(116);
});

test('seeder relinks existing cancer diagnoses by specific classification name', function (): void {
    $cancerDiagnose = CancerDiagnose::query()->create([
        'iccc_specific_classification' => 'Retinoblastoma',
    ]);

    $this->seed(CancerClassificationSeeder::class);

    expect($cancerDiagnose->refresh()->specificClassification?->name)->toBe('Retinoblastoma');
});
