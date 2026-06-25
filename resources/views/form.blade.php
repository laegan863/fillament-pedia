<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pediatric Hospital-Based Cancer Registry Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #e5e7eb;
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
        }

        .paper {
            width: 1080px;
            max-width: 100%;
            margin: 24px auto;
            background: #fff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .16);
        }

        .form-shell {
            min-width: 980px;
            border: 1px solid #111;
            background: #fff;
        }

        .tiny {
            font-size: 11px;
            line-height: 1.05;
        }

        .micro {
            font-size: 10px;
            line-height: 1.05;
        }

        .cell {
            border-right: 1px solid #111;
            border-bottom: 1px solid #111;
            min-height: 28px;
            padding: 5px 8px;
        }

        .cell-left {
            background: #d5d5d5;
            font-weight: 700;
        }

        .section-title {
            background: #000;
            color: #fff;
            letter-spacing: .32em;
            font-weight: 800;
            text-align: center;
            font-size: 17px;
            line-height: 1;
            padding: 9px 12px 8px;
            border-bottom: 1px solid #111;
        }

        .black-bar {
            background: #000;
            color: #fff;
            font-weight: 800;
            text-align: center;
            font-size: 18px;
            line-height: 1;
            padding: 9px 12px 8px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #111;
        }

        .sub-bar {
            display: block;
            font-size: 12px;
            letter-spacing: .45em;
            margin-top: 3px;
            font-style: italic;
        }

        .box {
            display: inline-grid;
            place-items: center;
            width: 20px;
            height: 20px;
            border: 1px solid #c9c9c9;
            background: #fff;
            font-size: 12px;
            line-height: 1;
            margin-right: 6px;
            vertical-align: middle;
        }

        .box.checked::before {
            content: "x";
        }

        .soft-input {
            height: 20px;
            border: 1px solid #cbd5e1;
            background: #fff;
            padding: 1px 5px;
            min-width: 44px;
            display: inline-flex;
            align-items: center;
        }

        .pill-select {
            border: 1px solid #d1d5db;
            height: 20px;
            border-radius: 999px;
            background: linear-gradient(#f5f6f7, #e4e7ea);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 28px 0 10px;
            position: relative;
            font-weight: 700;
        }

        .pill-select::after {
            content: "";
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 6px solid #333;
            position: absolute;
            right: 10px;
            top: 7px;
        }

        .no-right {
            border-right: 0 !important;
        }

        .no-bottom {
            border-bottom: 0 !important;
        }

        .v-center {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .diagnosis-code-grid {
            display: grid;
            grid-template-columns: 270px 180px 170px 260px 220px;
            width: 100%;
        }

        .diagnosis-code-cell {
            min-height: 72px;
            overflow: hidden;
        }

        .diagnosis-code-boxes {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0;
            width: 100%;
            max-width: 100%;
            overflow: hidden;
            margin-top: 8px;
        }

        .diagnosis-code-box {
            width: 28px;
            height: 32px;
            border: 1px solid #111;
            border-left-width: 0;
            display: grid;
            place-items: center;
            background: #fff;
            flex: 0 0 28px;
        }

        .diagnosis-code-box:first-child {
            border-left-width: 1px;
        }

        .morphology-cell {
            min-width: 220px;
            width: 220px;
        }

        .line-input {
            display: inline-block;
            border-bottom: 1px solid #111;
            min-width: 120px;
            height: 16px;
            vertical-align: baseline;
        }

        @media (max-width: 1024px) {
            body {
                background: #f3f4f6;
            }

            .paper {
                margin: 0;
                box-shadow: none;
                width: 100%;
            }

            .scroll-wrap {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                padding: 12px;
            }

            .form-shell {
                min-width: 980px;
            }
        }

        @media print {
            body {
                background: #fff;
            }

            .paper {
                margin: 0;
                box-shadow: none;
                width: 100%;
            }

            .scroll-wrap {
                padding: 0;
                overflow: visible;
            }

            .form-shell {
                min-width: 0;
                width: 100%;
            }
        }
    </style>
</head>

<body class="min-h-screen">
    @php
        $diagnosis = $cancerDiagnose ?? null;
        $diagnosisDate = $diagnosis?->diagnosis_date;
        $values = function (mixed $value): array {
            if (blank($value)) {
                return [];
            }

            if (is_array($value)) {
                return array_values(array_filter($value, fn (mixed $item): bool => filled($item)));
            }

            return [(string) $value];
        };
        $metastasisSites = $values($diagnosis?->metastasis_sites);
        $stagingUsed = $values($diagnosis?->staging_used);
        $multidisciplinaryDisciplines = $values($diagnosis?->multidisciplinary_disciplines);
        $antiCancerDrugTypes = $values($diagnosis?->anti_cancer_drug_types);
        $otherCancerDirectedTherapyTypes = $values($diagnosis?->other_cancer_directed_therapy_types);
        $patientHealthFacilityIdNo = filled($diagnosis?->patient_health_facility_id_no)
            ? (string) $diagnosis->patient_health_facility_id_no
            : (filled($formDetails->health_facility_id_no)
                ? str_pad((string) $formDetails->health_facility_id_no, 10, '0', STR_PAD_LEFT)
                : '');
        $hasMetastasis = $diagnosis && (filled($diagnosis->metastasis_status)
            ? $diagnosis->metastasis_status === 'Yes'
            : filled($metastasisSites));

        $checked = fn (bool $condition): string => $condition ? ' checked' : '';
        $matches = fn (mixed $actual, string $expected): string => $checked($actual === $expected);
        $contains = fn (mixed $actual, string $expected): string => $checked(in_array($expected, $values($actual), true));
        $field = fn (mixed $value, string $fallback = ''): string => filled($value) ? (string) $value : $fallback;
        $characters = function (mixed $value, int $length): array {
            return array_pad(str_split((string) ($value ?? '')), $length, '');
        };
        $topographyCodeCharacters = $characters($diagnosis?->icdo3_topography_code, 5);
        $morphologyCodeCharacters = $characters($diagnosis?->icdo3_morphology_code, 6);
    @endphp

    <main class="paper">
        <div class="scroll-wrap">
            <form class="form-shell text-[12px] leading-tight">
                <header class="px-3 pt-8 pb-3 text-center border-b border-black">
                    <div class="micro font-bold">Republic of the Philippines</div>
                    <div class="micro">Department of Health</div>
                    <div class="text-[15px] font-extrabold tracking-wide">Philippine Cancer Center</div>
                    <h1 class="text-[31px] font-extrabold tracking-[.06em] mt-1 uppercase">Pediatric Hospital-Based
                        Cancer Registry</h1>
                    <div class="grid grid-cols-[1fr_230px] gap-6 mt-2 text-left">
                        <div class="micro">
                            <span class="font-bold">General Instruction:</span>
                            <span class="ml-8">a. Mark the appropriate box with an <b>X</b>.</span><br>
                            <span class="ml-[127px]">b. Optional variables will be labeled with the word
                                <b><i>“Optional.”</i></b></span>
                        </div>
                        <div class="micro pt-4">
                            <div>City Registration No. <span class="line-input min-w-[120px]">{{ $field($diagnosis?->health_facility_registration_no) }}</span></div>
                        </div>
                    </div>
                </header>

                <section class="black-bar">
                    FORM 1A: PROFILE OF CANCER DIAGNOSIS OF PATIENT
                    <span class="sub-bar">(Part of H B C R FORM 1)</span>
                </section>

                <section class="section-title">PRIMARY CANCER DIAGNOSIS</section>

                <div class="grid grid-cols-[270px_1fr]">
                    <div class="cell cell-left tiny"><span class="font-normal mr-2">2</span> Patient’s Health Facility
                        ID No</div>
                    <div class="cell no-right tiny font-bold">{{ $patientHealthFacilityIdNo }}</div>
                </div>

                <div class="grid grid-cols-[270px_280px_1fr]">
                    <div class="cell cell-left tiny">More than 1 active Primary<br>Cancer Site/s?</div>
                    <div class="cell tiny v-center"><span class="box{{ $checked((bool) $diagnosis?->has_multiple_active_primary_cancer_sites) }}"></span> Yes <span class="box ml-7{{ $checked(! (bool) $diagnosis?->has_multiple_active_primary_cancer_sites) }}"></span>
                        No</div>
                    <div class="cell no-right micro italic">Note: If Yes, please document each cancer site separately
                        using an<br>additional copy of Form 1A.</div>
                </div>

                <div class="grid grid-cols-[270px_1fr]">
                    <div class="cell cell-left tiny"><span class="font-normal mr-2">10</span> Primary Cancer Site Number
                    </div>
                    <div class="cell no-right tiny text-blue-700 font-bold">{{ $field($diagnosis?->primary_cancer_site_number, '1') }}</div>
                </div>

                <div class="grid grid-cols-[270px_1fr]">
                    <div class="cell cell-left tiny"><span class="font-normal mr-2">13</span> Date of Diagnosis</div>
                    <div class="cell no-right">
                        <div class="flex items-end gap-2 tiny">
                            <div class="text-center"><span class="soft-input w-16 justify-center">{{ $diagnosisDate?->format('Y') }}</span>
                                <div class="micro text-gray-500">YYYY</div>
                            </div>
                            <div class="text-center"><span class="soft-input w-14 justify-center">{{ $diagnosisDate?->format('m') }}</span>
                                <div class="micro text-gray-500">MM</div>
                            </div>
                            <div class="text-center"><span class="soft-input w-14 justify-center">{{ $diagnosisDate?->format('d') }}</span>
                                <div class="micro text-gray-500">DD</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-[270px_1fr]">
                    <div class="cell cell-left tiny"><span class="font-normal mr-2">12</span> Age at Diagnosis <span
                            class="italic">(optional)</span></div>
                    <div class="cell no-right tiny"><span class="font-bold mr-14">{{ $field($diagnosis?->age_at_diagnosis_years) }}</span> Years <span
                            class="font-bold ml-14 mr-14">{{ $field($diagnosis?->age_at_diagnosis_months) }}</span> Months</div>
                </div>

                <div class="grid grid-cols-[270px_1fr]">
                    <div class="cell cell-left tiny"><span class="font-normal mr-2">13</span> Basis for Diagnosis</div>
                    <div class="cell no-right">
                        <div class="grid grid-cols-2 gap-x-12 gap-y-1 tiny max-w-[620px]">
                            <label><span class="box{{ $matches($diagnosis?->basis_for_diagnosis, 'Clinical Only') }}"></span> Clinical only</label>
                            <label><span class="box{{ $matches($diagnosis?->basis_for_diagnosis, 'Histology of Metastasis') }}"></span> Histology of Metastasis</label>
                            <label><span class="box{{ $matches($diagnosis?->basis_for_diagnosis, 'Clinical Investigation') }}"></span> Clinical investigation (X-ray, etc)</label>
                            <label><span class="box{{ $matches($diagnosis?->basis_for_diagnosis, 'Histology of Primary') }}"></span> Histology of Primary</label>
                            <label><span class="box{{ $matches($diagnosis?->basis_for_diagnosis, 'Specific Tumour Markers') }}"></span> Specific tumour Markers</label>
                            <label><span class="box{{ $matches($diagnosis?->basis_for_diagnosis, 'Unknown') }}"></span> Unknown</label>
                            <label><span class="box{{ $matches($diagnosis?->basis_for_diagnosis, 'Cytology / Hematology') }}"></span> Cytology/Hematology</label>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-[270px_1fr]">
                    <div class="cell cell-left tiny min-h-[118px]"><span class="font-normal mr-2">14</span>
                        Diagnosis-International<br><span class="ml-5">Classification of Childhood</span><br><span
                            class="ml-5">Cancer</span></div>
                    <div class="cell no-right">
                        <div class="grid grid-cols-[170px_1fr] gap-x-6 gap-y-2 tiny">
                            <div class="font-bold">Specific Classification</div>
                            <div class="pill-select">{{ $field($diagnosis?->iccc_specific_classification) }}</div>
                            <div class="font-bold">Parent Classification</div>
                            <div>{{ $field($diagnosis?->iccc_parent_classification, '----') }}</div>
                            <div class="font-bold">General Classification</div>
                            <div class="font-bold">{{ $field($diagnosis?->iccc_general_classification) }}</div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-[270px_95px_1fr]">
                    <div class="cell cell-left tiny"><span class="font-normal mr-2">15</span> Diagnosis one of
                        the<br><span class="ml-5">GICC 6 indexed cancers?</span></div>
                    <div class="cell tiny">
                        <div class="mb-6"><span class="box{{ $checked((bool) $diagnosis?->is_gicc_indexed_cancer) }}"></span> Yes</div>
                        <div><span class="box{{ $checked(! (bool) $diagnosis?->is_gicc_indexed_cancer) }}"></span> No</div>
                    </div>
                    <div class="cell no-right">
                        <div class="micro mb-1">If yes, specify</div>
                        <div class="grid grid-cols-3 gap-x-10 gap-y-1 tiny">
                            <label><span class="box{{ $matches($diagnosis?->gicc_indexed_cancer_type, 'Acute Lymphoblastic Leukemia') }}"></span> Acute Lymphoblastic Leukemia</label>
                            <label><span class="box{{ $matches($diagnosis?->gicc_indexed_cancer_type, 'Low Grade Glioma') }}"></span> Low Grade Glioma</label>
                            <span></span>
                            <label><span class="box{{ $matches($diagnosis?->gicc_indexed_cancer_type, 'Burkitt Lymphoma') }}"></span> Burkitt Lymphoma</label>
                            <label><span class="box{{ $matches($diagnosis?->gicc_indexed_cancer_type, 'Retinoblastoma') }}"></span> Retinoblastoma</label>
                            <span></span>
                            <label><span class="box{{ $matches($diagnosis?->gicc_indexed_cancer_type, 'Hodgkin Lymphoma') }}"></span> Hodgkin Lymphoma</label>
                            <label><span class="box{{ $matches($diagnosis?->gicc_indexed_cancer_type, 'Wilms Tumor') }}"></span> Wilms Tumor</label>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-[270px_1fr]">
                    <div class="cell cell-left tiny"><span class="font-normal mr-2">16</span> Topography</div>
                    <div class="cell no-right">
                        <div class="pill-select w-[720px] tiny mx-auto">{{ $field($diagnosis?->topography === 'Others' ? $diagnosis?->topography_other : $diagnosis?->topography) }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-[270px_1fr]">
                    <div class="cell cell-left tiny"><span class="font-normal mr-2">17</span> Laterality</div>
                    <div class="cell no-right">
                        <div class="grid grid-cols-4 gap-x-12 gap-y-1 tiny max-w-[560px]">
                            <label><span class="box{{ $matches($diagnosis?->laterality, 'Left') }}"></span> Left</label>
                            <label><span class="box{{ $matches($diagnosis?->laterality, 'Bilateral') }}"></span> Bilateral</label>
                            <label><span class="box{{ $matches($diagnosis?->laterality, 'Unknown') }}"></span> Unknown</label>
                            <span></span>
                            <label><span class="box{{ $matches($diagnosis?->laterality, 'Right') }}"></span> Right</label>
                            <label><span class="box{{ $matches($diagnosis?->laterality, 'Not Applicable') }}"></span> Not Applicable</label>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-[270px_95px_1fr]">
                    <div class="cell cell-left tiny"><span class="font-normal mr-2">18</span> Metastasis</div>
                    <div class="cell tiny">
                        <div class="mb-6"><span class="box{{ $checked((bool) $hasMetastasis) }}"></span> Yes</div>
                        <div><span class="box{{ $checked(! (bool) $hasMetastasis) }}"></span> None</div>
                    </div>
                    <div class="cell no-right">
                        <div class="micro mb-1">If yes, specific site/s (may be multiple)</div>
                        <div class="grid grid-cols-3 gap-x-16 gap-y-1 tiny max-w-[600px]">
                            <label><span class="box{{ $contains($metastasisSites, 'Bone') }}"></span> Bone</label>
                            <label><span class="box{{ $contains($metastasisSites, 'Lung') }}"></span> Lung</label>
                            <span></span>
                            <label><span class="box{{ $contains($metastasisSites, 'Bone Marrow') }}"></span> Bone Marrow</label>
                            <label><span class="box{{ $contains($metastasisSites, 'Lymph Node') }}"></span> Lymph Node</label>
                            <span></span>
                            <label><span class="box{{ $contains($metastasisSites, 'Brain') }}"></span> Brain</label>
                            <label><span class="box{{ $contains($metastasisSites, 'Spine') }}"></span> Spine</label>
                            <span></span>
                            <label><span class="box{{ $contains($metastasisSites, 'Cerebrospinal Fluid') }}"></span> Cerebrospinal Fluid</label>
                            <label><span class="box{{ $contains($metastasisSites, 'Testes') }}"></span> Testes</label>
                            <span></span>
                            <label><span class="box{{ $contains($metastasisSites, 'Liver') }}"></span> Liver</label>
                            <label><span class="box{{ $contains($metastasisSites, 'Other') }}"></span> Other, Specify</label>
                            <span class="border-b border-black">{{ $field($diagnosis?->metastasis_other_site) }}</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-[270px_1fr]">
                    <div class="cell cell-left tiny"><span class="font-normal mr-2">18</span> Details for Diagnosis
                    </div>
                    <div class="cell no-right micro text-center">{{ $field($diagnosis?->details_for_diagnosis) }}</div>
                </div>

                <div class="diagnosis-code-grid">
                    <div class="cell cell-left tiny diagnosis-code-cell">
                        <span class="font-normal mr-2">20</span> ICD 10
                    </div>

                    <div class="cell text-center tiny diagnosis-code-cell">
                        <div class="pt-1">{{ $field($diagnosis?->icd10_code) }}</div>
                    </div>

                    <div class="cell text-center tiny font-bold diagnosis-code-cell">
                        ICD-O-3<br><span class="font-normal">(optional)</span>
                        <div class="diagnosis-code-boxes">
                            <span class="diagnosis-code-box"></span>
                            <span class="diagnosis-code-box"></span>
                            <span class="diagnosis-code-box"></span>
                        </div>
                    </div>

                    <div class="cell text-center tiny font-bold diagnosis-code-cell">
                        Topography
                        <div class="diagnosis-code-boxes">
                            @foreach ($topographyCodeCharacters as $character)
                                <span class="diagnosis-code-box">{{ $character }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div class="cell no-right text-center tiny font-bold diagnosis-code-cell morphology-cell">
                        Morphology
                        <div class="diagnosis-code-boxes">
                            @foreach ($morphologyCodeCharacters as $character)
                                <span class="diagnosis-code-box">{{ $character }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>


                <div class="grid grid-cols-[270px_1fr]">
                    <div class="cell cell-left tiny min-h-[54px]"><span class="font-normal mr-2">21</span> Disease Stage
                        <span class="italic">(optional)</span></div>
                    <div class="cell no-right tiny">
                        <div class="grid grid-cols-4 gap-x-8 gap-y-1 max-w-[620px]">
                            <label><span class="box{{ $matches($diagnosis?->clinical_stage, 'Stage I') }}"></span> Stage I</label>
                            <label><span class="box{{ $matches($diagnosis?->clinical_stage, 'Stage II') }}"></span> Stage II</label>
                            <label><span class="box{{ $matches($diagnosis?->clinical_stage, 'Stage III') }}"></span> Stage III</label>
                            <label><span class="box{{ $matches($diagnosis?->clinical_stage, 'Stage IV') }}"></span> Stage IV</label>
                            <label><span class="box{{ $matches($diagnosis?->clinical_stage, 'Unknown') }}"></span> Unknown</label>
                            <label><span class="box{{ $matches($diagnosis?->clinical_stage, 'Not Applicable') }}"></span> Not Applicable</label>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-[270px_1fr]">
                    <div class="cell cell-left tiny min-h-[60px] no-bottom"><span class="font-normal mr-2">22</span>
                        Notes / Remarks</div>
                    <div class="cell no-right no-bottom tiny">
                        <div class="h-10 border-b border-black">{{ $field($diagnosis?->clinical_stage_other) }}</div>
                    </div>
                </div>

                <!-- Clinical Staging -->
                <section class="section-title !tracking-[.18em] !text-[13px] !py-[5px]">CLINICAL STAGING</section>

                <div class="grid grid-cols-[270px_1fr]">
                    <div class="cell cell-left tiny min-h-[68px]">
                        <span class="font-normal mr-2">22</span> Clinical Stage
                    </div>
                    <div class="cell no-right tiny">
                        <div class="grid grid-cols-4 gap-x-10 gap-y-1 max-w-[650px]">
                            <label><span class="box{{ $matches($diagnosis?->clinical_stage, 'Stage 0') }}"></span> Stage 0</label>
                            <label><span class="box{{ $matches($diagnosis?->clinical_stage, 'Stage II') }}"></span> Stage II</label>
                            <label><span class="box{{ $matches($diagnosis?->clinical_stage, 'Stage IV') }}"></span> Stage IV</label>
                            <label><span class="box{{ $matches($diagnosis?->clinical_stage, 'Not Applicable') }}"></span> Not Applicable</label>
                            <label><span class="box{{ $matches($diagnosis?->clinical_stage, 'Stage I') }}"></span> Stage I</label>
                            <label><span class="box{{ $matches($diagnosis?->clinical_stage, 'Stage III') }}"></span> Stage III</label>
                            <label><span class="box{{ $matches($diagnosis?->clinical_stage, 'Stage V') }}"></span> Stage V</label>
                            <label><span class="box{{ $matches($diagnosis?->clinical_stage, 'Unknown') }}"></span> Unknown</label>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-[270px_1fr]">
                    <div class="cell cell-left tiny min-h-[150px]">
                        <span class="font-normal mr-2">23</span> Staging Used<br>
                        <span class="micro font-normal italic ml-5">(If applicable, select Staging system used)</span>
                    </div>
                    <div class="cell no-right tiny">
                        <div class="grid gap-y-1">
                            <label><span class="box{{ $contains($stagingUsed, 'Ann Arbor') }}"></span> Ann Arbor Staging System</label>
                            <label><span class="box{{ $contains($stagingUsed, 'St. Jude / Murphy') }}"></span> St Jude/Murphy Staging for NHL</label>
                            <label><span class="box{{ $contains($stagingUsed, 'MTS / AJCC TNM') }}"></span> Musculoskeletal Tumor Society (MTS) Classification and
                                Staging</label>
                            <label><span class="box{{ $contains($stagingUsed, 'Intraocular Retinoblastoma') }}"></span> International Classification System for Intraocular
                                Retinoblastoma (ICRB)</label>
                            <label><span class="box{{ $contains($stagingUsed, 'COG / PRETEXT') }}"></span> COG Staging for Hepatoblastoma (Evans)</label>
                            <label><span class="box{{ $contains($stagingUsed, 'NWTS / COG / SIOP') }}"></span> INSS/COG/SIOP Staging for Wilms</label>
                            <label><span class="box{{ $contains($stagingUsed, 'INSS / INRG') }}"></span> Intl Neuroblastoma Staging System (INSS)/Intl Neuroblastoma
                                Risk Group Classification (INRG)</label>
                            <label><span class="box{{ $contains($stagingUsed, 'COG / CCLG Germ Cell') }}"></span> COG/CCLG staging for Germ Cell Tumors (Gonadal and
                                extragonadal)</label>
                            <label><span class="box{{ $contains($stagingUsed, 'Toronto Tier-1') }}"></span> Toronto Tier-1 Staging</label>
                            <label><span class="box{{ $contains($stagingUsed, 'Other') }}"></span> Other</label>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-[270px_1fr]">
                    <div class="cell cell-left tiny"></div>
                    <div class="cell no-right tiny font-bold">
                        Other remarks <span class="font-normal ml-10">{{ $field($diagnosis?->staging_other_remarks, 'n/a') }}</span>
                    </div>
                </div>

                <!-- Disease Status -->
                <section class="section-title !tracking-[.18em] !text-[15px] !py-[6px]">DISEASE STATUS</section>

                <div class="grid grid-cols-[270px_1fr]">
                    <div class="cell cell-left tiny min-h-[78px]">
                        <span class="font-normal mr-2">24</span> Current Status of Cancer
                    </div>
                    <div class="cell no-right tiny">
                        <div class="grid grid-cols-2 gap-x-16 gap-y-1 max-w-[780px]">
                            <label><span class="box{{ $matches($diagnosis?->current_status_of_cancer, 'Newly diagnosed') }}"></span> Newly diagnosed</label>
                            <label><span class="box{{ $matches($diagnosis?->current_status_of_cancer, 'Old case to center, returning') }}"></span> Old case to center, returning (previous
                                abandonment)</label>
                            <label><span class="box{{ $matches($diagnosis?->current_status_of_cancer, 'New case to center, no treatment') }}"></span> New case to center, no treatment</label>
                            <label><span class="box{{ $matches($diagnosis?->current_status_of_cancer, 'Old case, secondary malignancy') }}"></span> Old case, secondary malignancy</label>
                            <label><span class="box{{ $matches($diagnosis?->current_status_of_cancer, 'New case to center, received treatment elsewhere') }}"></span> New case to center, received treatment
                                elsewhere</label>
                            <label><span class="box{{ $matches($diagnosis?->current_status_of_cancer, 'Old case to center, relapse/refractory') }}"></span> Old case to center, relapse/refractory</label>
                        </div>
                    </div>
                </div>


                <!-- Treatment Plan -->
                <section class="section-title !tracking-[.18em] !text-[15px] !py-[6px]">TREATMENT PLAN</section>

                <div class="grid grid-cols-[270px_95px_1fr]">
                    <div class="cell cell-left tiny min-h-[118px]">
                        <span class="font-normal mr-2">25</span> Multidisciplinary Cancer Team<br>
                        <span class="ml-5">Approach Practice and disciplines</span><br>
                        <span class="ml-5">involved</span>
                    </div>
                    <div class="cell tiny">
                        <div class="mb-6"><span class="box{{ $checked((bool) $diagnosis?->has_multidisciplinary_cancer_team) }}"></span> Yes</div>
                        <div><span class="box{{ $checked(! (bool) $diagnosis?->has_multidisciplinary_cancer_team) }}"></span> No</div>
                    </div>
                    <div class="cell no-right tiny">
                        <div class="micro mb-1">If yes, specify all disciplines involved in the treatment approach?
                        </div>
                        <div class="grid grid-cols-2 gap-x-14 gap-y-1 max-w-[720px]">
                            <label><span class="box{{ $contains($multidisciplinaryDisciplines, 'Anesthesia / Pain') }}"></span> Anesthesia/Pain</label>
                            <label><span class="box{{ $contains($multidisciplinaryDisciplines, 'Pediatric Oncology') }}"></span> Pediatric Oncology</label>
                            <label><span class="box{{ $contains($multidisciplinaryDisciplines, 'Child Life Specialist') }}"></span> Child Life Specialist</label>
                            <label><span class="box{{ $contains($multidisciplinaryDisciplines, 'Radiation Oncology') }}"></span> Radiation Oncology</label>
                            <label><span class="box{{ $contains($multidisciplinaryDisciplines, 'Complementary and Alternative') }}"></span> Complementary and Alternative</label>
                            <label><span class="box{{ $contains($multidisciplinaryDisciplines, 'Rehabilitation Medicine') }}"></span> Rehabilitation Medicine</label>
                            <label><span class="box{{ $contains($multidisciplinaryDisciplines, 'Gynecologic Oncology') }}"></span> Gynecologic Oncology</label>
                            <label><span class="box{{ $contains($multidisciplinaryDisciplines, 'Supportive / Palliative Care') }}"></span> Supportive/Palliative Care</label>
                            <label><span class="box{{ $contains($multidisciplinaryDisciplines, 'Pathology') }}"></span> Pathology</label>
                            <label><span class="box"></span> Surgery</label>
                            <label><span class="box{{ $contains($multidisciplinaryDisciplines, 'Pediatric Hematology') }}"></span> Pediatric Hematology</label>
                            <label><span class="box{{ $contains($multidisciplinaryDisciplines, 'Other') }}"></span> Others, specify <span
                                    class="inline-block border-b border-black min-w-[120px]">{{ $field($diagnosis?->multidisciplinary_other_discipline) }}</span></label>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-[270px_95px_1fr]">
                    <div class="cell cell-left tiny min-h-[48px]">
                        <span class="font-normal mr-2">26</span> Surgery
                    </div>
                    <div class="cell tiny">
                        <div><span class="box{{ $checked((bool) $diagnosis?->has_surgery) }}"></span> Yes</div>
                        <div><span class="box{{ $checked(! (bool) $diagnosis?->has_surgery) }}"></span> No</div>
                    </div>
                    <div class="cell no-right tiny">
                        <div class="micro mb-1">If yes, specify goal</div>
                        <div class="grid grid-cols-4 gap-x-8 max-w-[610px]">
                            <label><span class="box{{ $matches($diagnosis?->surgery_goal, 'Definitive') }}"></span> Definitive</label>
                            <label><span class="box{{ $matches($diagnosis?->surgery_goal, 'Debulking') }}"></span> Debulking</label>
                            <label><span class="box{{ $matches($diagnosis?->surgery_goal, 'Diagnostic') }}"></span> Diagnostic</label>
                            <label><span class="box{{ $matches($diagnosis?->surgery_goal, 'Reconstructive') }}"></span> Reconstructive</label>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-[270px_95px_1fr]">
                    <div class="cell cell-left tiny min-h-[112px]">
                        <span class="font-normal mr-2">27</span> Anti-Cancer Drug
                    </div>
                    <div class="cell tiny">
                        <div class="mb-6"><span class="box{{ $checked((bool) $diagnosis?->has_anti_cancer_drug) }}"></span> Yes</div>
                        <div><span class="box{{ $checked(! (bool) $diagnosis?->has_anti_cancer_drug) }}"></span> No</div>
                    </div>
                    <div class="cell no-right tiny !p-0">
                        <div class="grid grid-cols-[170px_1fr] border-b border-black min-h-[48px]">
                            <div class="p-2 font-bold border-r border-black flex items-center">
                                Purpose of Drug<br>Administration
                            </div>
                            <div class="p-2">
                                <div class="micro mb-1">If yes, specify main provider of palliative care</div>
                                <div class="grid grid-cols-2 gap-x-16 max-w-[390px]">
                                    <label><span class="box{{ $matches($diagnosis?->anti_cancer_drug_purpose, 'Curative') }}"></span> Curative</label>
                                    <label><span class="box{{ $matches($diagnosis?->anti_cancer_drug_purpose, 'Palliative') }}"></span> Palliative</label>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-[170px_1fr] min-h-[63px]">
                            <div class="p-2 font-bold border-r border-black flex items-center">
                                Drug Type/s (can be<br>multiple)
                            </div>
                            <div class="p-2">
                                <div class="grid grid-cols-2 gap-x-16 gap-y-1 max-w-[420px]">
                                    <label><span class="box{{ $contains($antiCancerDrugTypes, 'Cytotoxic') }}"></span> Cytotoxic</label>
                                    <label><span class="box{{ $contains($antiCancerDrugTypes, 'Targeted') }}"></span> Targeted</label>
                                    <label><span class="box{{ $contains($antiCancerDrugTypes, 'Hormonal') }}"></span> Hormonal</label>
                                    <label><span class="box{{ $contains($antiCancerDrugTypes, 'Other') }}"></span> Other, specify {{ $field($diagnosis?->anti_cancer_drug_other_type) }}</label>
                                    <label><span class="box{{ $contains($antiCancerDrugTypes, 'Immunologic') }}"></span> Immunologic</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-[270px_95px_1fr]">
                    <div class="cell cell-left tiny min-h-[48px]">
                        <span class="font-normal mr-2">28</span> Radiotherapy
                    </div>
                    <div class="cell tiny">
                        <div><span class="box{{ $checked((bool) $diagnosis?->has_radiotherapy) }}"></span> Yes</div>
                        <div><span class="box{{ $checked(! (bool) $diagnosis?->has_radiotherapy) }}"></span> No</div>
                    </div>
                    <div class="cell no-right tiny">
                        <div class="micro mb-1">If yes, specify goal</div>
                        <div class="grid grid-cols-2 gap-x-16 max-w-[320px]">
                            <label><span class="box{{ $matches($diagnosis?->radiotherapy_goal, 'Curative') }}"></span> Curative</label>
                            <label><span class="box{{ $matches($diagnosis?->radiotherapy_goal, 'Palliative') }}"></span> Palliative</label>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-[270px_95px_1fr]">
                    <div class="cell cell-left tiny min-h-[48px]">
                        <span class="font-normal mr-2">29</span> Theranostics
                    </div>
                    <div class="cell tiny">
                        <div><span class="box{{ $checked((bool) $diagnosis?->has_theranostics) }}"></span> Yes</div>
                        <div><span class="box{{ $checked(! (bool) $diagnosis?->has_theranostics) }}"></span> No</div>
                    </div>
                    <div class="cell no-right tiny">
                        <div class="micro mb-1">If yes, specify goal</div>
                        <div class="grid grid-cols-2 gap-x-16 max-w-[320px]">
                            <label><span class="box{{ $matches($diagnosis?->theranostics_goal, 'Curative') }}"></span> Curative</label>
                            <label><span class="box{{ $matches($diagnosis?->theranostics_goal, 'Palliative') }}"></span> Palliative</label>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-[270px_95px_1fr]">
                    <div class="cell cell-left tiny min-h-[100px]">
                        <span class="font-normal mr-2">30</span> Palliative Care
                    </div>
                    <div class="cell tiny">
                        <div class="mb-6"><span class="box{{ $checked((bool) $diagnosis?->has_palliative_care) }}"></span> Yes</div>
                        <div><span class="box{{ $checked(! (bool) $diagnosis?->has_palliative_care) }}"></span> No</div>
                    </div>
                    <div class="cell no-right tiny">
                        <div class="micro mb-1">If yes, specify main provider of palliative care</div>
                        <div class="grid gap-y-1 max-w-[440px]">
                            <label><span class="box{{ $matches($diagnosis?->palliative_care_provider, 'Palliative Care Physician') }}"></span> Palliative Care Physician</label>
                            <label><span class="box{{ $matches($diagnosis?->palliative_care_provider, 'Pediatric Oncologist') }}"></span> Pediatric Oncologist / Hematologist</label>
                            <label><span class="box{{ $matches($diagnosis?->palliative_care_provider, 'Palliative Care Nurse') }}"></span> Palliative Care Nurse</label>
                            <label><span class="box{{ $matches($diagnosis?->palliative_care_provider, 'Pain Management Specialist') }}"></span> Pain Management Specialist</label>
                            <label><span class="box{{ $matches($diagnosis?->palliative_care_provider, 'Community Based Palliative Care') }}"></span> Community Based Palliative Care Worker</label>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-[270px_95px_1fr]">
                    <div class="cell cell-left tiny min-h-[56px]">
                        <span class="font-normal mr-2">31</span> Other Cancer-Directed Therapy
                    </div>
                    <div class="cell tiny">
                        <div><span class="box{{ $checked((bool) $diagnosis?->has_other_cancer_directed_therapy) }}"></span> Yes</div>
                        <div><span class="box{{ $checked(! (bool) $diagnosis?->has_other_cancer_directed_therapy) }}"></span> No</div>
                    </div>
                    <div class="cell no-right tiny">
                        <div class="micro mb-1">If yes, specify type</div>
                        <div class="grid grid-cols-3 gap-x-10 max-w-[570px]">
                            <label><span class="box{{ $contains($otherCancerDirectedTherapyTypes, 'Transplant') }}"></span> Transplant</label>
                            <label><span class="box{{ $contains($otherCancerDirectedTherapyTypes, 'Other') }}"></span> Other, Specify <span
                                    class="inline-block border-b border-black min-w-[130px]">{{ $field($diagnosis?->other_cancer_directed_therapy_other_type) }}</span></label>
                            <label><span class="box{{ $contains($otherCancerDirectedTherapyTypes, 'RAI') }}"></span> RAI</label>
                            <label><span class="box{{ $contains($otherCancerDirectedTherapyTypes, 'Chemoembolization') }}"></span> Chemoembolization</label>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-[270px_1fr]">
                    <div class="cell cell-left tiny min-h-[44px]">
                        <span class="font-normal mr-2">32</span> Overall Goal of Therapy
                    </div>
                    <div class="cell no-right tiny">
                        <div class="micro mb-1">Specify goal of therapy</div>
                        <div class="grid grid-cols-2 gap-x-16 max-w-[300px]">
                            <label><span class="box{{ $matches($diagnosis?->overall_goal_of_therapy, 'Curative') }}"></span> Curative</label>
                            <label><span class="box{{ $matches($diagnosis?->overall_goal_of_therapy, 'Palliative') }}"></span> Palliative</label>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </main>
</body>
</html>
