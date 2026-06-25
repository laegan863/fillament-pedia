<?php

namespace App\Pattern;

class cancerdiagnosis
{
    public static function classification(): array
    {
        return [

        ];
    }

    public static function getCancerDiagnosisOptions(): array
    {
        return [
            'breast_cancer' => 'Breast Cancer',
            'lung_cancer' => 'Lung Cancer',
            'prostate_cancer' => 'Prostate Cancer',
            'colorectal_cancer' => 'Colorectal Cancer',
            'skin_cancer' => 'Skin Cancer',
            'leukemia' => 'Leukemia',
            'lymphoma' => 'Lymphoma',
            'pancreatic_cancer' => 'Pancreatic Cancer',
            'liver_cancer' => 'Liver Cancer',
            'ovarian_cancer' => 'Ovarian Cancer',
        ];
    }
}
