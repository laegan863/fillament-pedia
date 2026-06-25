<?php

use App\Filament\Resources\CancerDiagnoses\Schemas\CancerDiagnoseForm;
use App\Models\CancerDiagnose;
use App\Models\GeneralClassification;
use App\Models\ParentClassification;
use App\Models\SpecificClassification;
use Database\Seeders\CancerDiagnosisClassificationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(CancerDiagnosisClassificationSeeder::class);
});

test('cancer diagnosis classifications are seeded from the csv data', function (): void {
    $options = CancerDiagnoseForm::icccDiagnosisOptions();
    $firstSpecificClassification = SpecificClassification::query()
        ->where('name', 'Precursor cell leukemia')
        ->firstOrFail();

    expect(GeneralClassification::query()->count())
        ->toBe(12)
        ->and(ParentClassification::query()->count())
        ->toBe(16)
        ->and(SpecificClassification::query()->count())
        ->toBe(116)
        ->and($options)
        ->toHaveCount(116)
        ->and(array_key_first($options))
        ->toBe($firstSpecificClassification->id)
        ->and($options[$firstSpecificClassification->id])
        ->toBe('Precursor cell leukemia')
        ->and($firstSpecificClassification->generalClassification->name)
        ->toBe('Leukemias, Myeloproliferative Diseases, Myelodysplastic Disease')
        ->and($firstSpecificClassification->parentClassification->name)
        ->toBe('Lymphoid Leukemia')
        ->and(SpecificClassification::query()->where('name', 'Acute Myeloid Leukemia')->firstOrFail()->parent_classification_id)
        ->toBeNull();
});

test('classification seeder backfills existing cancer diagnoses with the specific classification foreign key', function (): void {
    $cancerDiagnose = CancerDiagnose::query()->create([
        'iccc_specific_classification' => 'Retinoblastoma',
    ]);

    $this->seed(CancerDiagnosisClassificationSeeder::class);

    expect($cancerDiagnose->refresh()->specificClassification)
        ->not->toBeNull()
        ->and($cancerDiagnose->specificClassification->name)
        ->toBe('Retinoblastoma');
});

test('specific iccc classifications fill the expected parent and general classifications', function (
    string $specificClassification,
    ?string $expectedParentClassification,
    string $expectedGeneralClassification,
): void {
    expect(CancerDiagnoseForm::icccClassificationValue($specificClassification, 'parent'))
        ->toBe($expectedParentClassification)
        ->and(CancerDiagnoseForm::icccClassificationValue($specificClassification, 'general'))
        ->toBe($expectedGeneralClassification);
})->with([
    'lymphoid leukemia nos' => [
        'Lymphoid Leukemia, NOS',
        'Lymphoid Leukemia',
        'Leukemias, Myeloproliferative Diseases, Myelodysplastic Disease',
    ],
    'acute myeloid leukemia has no parent' => [
        'Acute Myeloid Leukemia',
        null,
        'Leukemias, Myeloproliferative Diseases, Myelodysplastic Disease',
    ],
    'retinoblastoma has no parent' => [
        'Retinoblastoma',
        null,
        'Retinoblastoma',
    ],
    'cns embryonal tumor' => [
        'Medulloblastoma',
        'Intracranial and Intraspinal Embryonal Tumors',
        'CNS and Miscellaneous Intracranial and Intraspinal Neoplasms',
    ],
    'renal tumor' => [
        'Nephroblastoma',
        'Nephroblastoma or Other Nonepithelial Renal Tumors',
        'Renal Tumors',
    ],
    'germ cell tumor' => [
        'Malignant Gonadal Tumors of Mixed Forms',
        'Malignant Gonadal Germ Cell Tumors',
        'Germ Cell Tumors, Trophoblastic Tumors and Neoplasms',
    ],
]);
