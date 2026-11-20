<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pediatric Hospital-Based Cancer Registry Follow-Up Form</title>
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
            line-height: 1.15;
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
            letter-spacing: .18em;
            font-weight: 800;
            text-align: center;
            font-size: 14px;
            line-height: 1;
            padding: 7px 12px 6px;
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
            width: 18px;
            height: 18px;
            border: 1px solid #9ca3af;
            background: #fff;
            font-size: 12px;
            line-height: 1;
            margin-right: 6px;
            vertical-align: middle;
            flex: 0 0 18px;
        }

        .box.checked::before {
            content: "x";
            font-weight: 700;
        }

        .no-right {
            border-right: 0 !important;
        }

        .option {
            display: flex;
            align-items: center;
            gap: 2px;
        }

        .records {
            width: 100%;
            border-collapse: collapse;
        }

        .records th,
        .records td {
            border-right: 1px solid #111;
            border-bottom: 1px solid #111;
            padding: 4px 6px;
            vertical-align: top;
            text-align: left;
        }

        .records th:last-child,
        .records td:last-child {
            border-right: 0;
        }

        .records th {
            background: #d5d5d5;
            font-weight: 700;
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
        $values = function (mixed $value): array {
            if (blank($value)) {
                return [];
            }

            if (is_array($value)) {
                return array_values(array_filter($value, fn (mixed $item): bool => filled($item)));
            }

            return [(string) $value];
        };
        $checked = fn (bool $condition): string => $condition ? ' checked' : '';
        $field = fn (mixed $value, string $fallback = ''): string => filled($value) ? (string) $value : $fallback;
        $date = fn (mixed $value): string => filled($value) ? \Illuminate\Support\Carbon::parse($value)->format('Y-m-d') : '';

        $patientHealthFacilityIdNo = $field($followUp->patient_health_facility_id_no, $field($formDetails?->health_facility_id_no));
        $proceduresAdministered = $values($followUp->procedures_administered);
        $treatmentPlan = $values($followUp->treatment_plan);
        $changeProcedures = $values($followUp->change_in_treatment_plan_procedures);
        $changeReasons = is_array($followUp->change_in_treatment_plan_reasons) ? $followUp->change_in_treatment_plan_reasons : [];
        $financialSupport = $values($followUp->financial_support_mechanisms);
        $primarySites = $values($followUp->primary_sites_being_treated);

        $procedureOptions = [
            'Surgery' => 'Form 2B Surgery',
            'Anti-cancer drug therapy' => 'Form 2C Anti-Cancer Drug Therapy',
            'Radiotherapy' => 'Form 2D Radiotherapy',
            'Theranostics' => 'Form 2E Theranostics',
            'Palliative Care' => 'Form 2F Palliative Care',
            'Other Treatments' => 'Form 2G Other Treatments',
        ];
        $treatmentStatusOptions = ['Ongoing', 'Completed', 'Stopped/Interrupted', 'Unknown', 'Not Initiated', 'Abandonment'];
        $diseaseOutcomeOptions = [
            'Stable Disease',
            'Complete Remission',
            'Partial Response',
            'Progressive/Refractory',
            'Recurrent Disease',
            'Undetermined',
            'Death (Cancer-Related)',
            'Death (Treatment-Related)',
            'Death (Other Cause/Non-Cancer Related)',
            'Death (Pending Evaluation)',
        ];
        $treatmentPlanOptions = [
            'Continue Current Management',
            'New Chemotherapy Regimen',
            'Refer to Surgery',
            'Refer to Radiation Oncology',
            'Refer to Theranostics/Nuclear Medicine',
            'Refer to Supportive/Palliative Care Team',
            'Transfer to Adult Care',
            'Not Applicable',
            'Others, specify',
        ];
        $changeProcedureOptions = [
            'Surgery' => 'surgery',
            'Anti-cancer drug therapy' => 'anti_cancer_drug_therapy',
            'Radiotherapy' => 'radiotherapy',
            'Theranostics' => 'theranostics',
            'Other Treatments' => 'other_treatments',
        ];
        $financialSupportOptions = [
            'Discounts under Law' => 'Discounts under Law e.g. Senior Citizen, PWD',
            'PhilHealth' => 'PhilHealth',
            'HMO' => 'Health Maintenance Organization (HMO)',
            'PCSO' => 'Philippine Charity Sweepstakes Office (PCSO)',
            'AICS' => 'Assistance to Individuals in Crisis Situations (AICS)',
            'CAF' => 'Cancer Assistance Fund (CAF)',
            'MAIFIP' => 'Medical Assistance for Indigent and Financially-Incapacitated Patients (MAIFIP)',
            'Hospital Assistance Funds' => 'Hospital Assistance Funds',
            'CSPMAP' => 'Cancer and Supportive-Palliative Medicines Access Program (CSPMAP)',
            'PAGCOR' => 'Philippine Amusement and Gaming Corporation (PAGCOR)',
            'NGO / Civil Society Org.' => 'Non-Government Org. / Civil Society Org.',
            'Clinical Trial' => 'Clinical Trial',
            'Charitable Institutions' => 'Charitable Institutions',
            'Private Sector Assistance Program' => 'Private Sector Assistance Program',
            'None' => 'None',
            'Others, specify' => 'Others, specify',
        ];

        $drugTherapyColumns = [
            'date_started' => 'Date Started',
            'date_last_anti_cancer_drug_therapy' => 'Date of Last Therapy',
            'drugs_given' => 'Drugs Given',
            'drug_types' => 'Drug Type/s',
            'treatment_phase' => 'Treatment Phase',
            'cycle_no' => 'Cycle No.',
            'goal_of_anti_cancer_drug_therapy' => 'Goal',
        ];
        $treatmentTables = [
            'surgeries' => [
                'title' => 'FORM 2B: SURGERY',
                'columns' => [
                    'surgery_date' => 'Surgery Date',
                    'surgery_rvs_code' => 'Surgery Type / RVS Code',
                    'surgery_description' => 'Surgery Description',
                    'surgery_goal' => 'Goal of Surgery',
                ],
            ],
            'anti_cancer_drug_therapies' => [
                'title' => 'FORM 2C: ANTI-CANCER DRUG THERAPY',
                'columns' => $drugTherapyColumns,
            ],
            'radiotherapies' => [
                'title' => 'FORM 2D: RADIOTHERAPY',
                'columns' => [
                    'date_started' => 'Date Started',
                    'date_ended' => 'Date Ended',
                    'total_planned_dose' => 'Total Planned Dose',
                    'total_delivered_fraction' => 'Delivered Fraction',
                    'dose_per_fraction' => 'Dose / Fraction',
                    'total_number_of_days' => 'No. of Days',
                    'radiotherapy_type' => 'Type',
                    'target_site' => 'Target Site',
                    'goal_of_radiotherapy' => 'Goal',
                ],
            ],
            'theranostics' => [
                'title' => 'FORM 2E: THERANOSTICS',
                'columns' => $drugTherapyColumns,
            ],
            'palliative_cares' => [
                'title' => 'FORM 2F: PALLIATIVE CARE',
                'columns' => [
                    'date_started' => 'Date Started',
                    'date_last_palliative_care' => 'Date of Last Palliative Care',
                    'reasons' => 'Reasons',
                    'type_of_care_integration' => 'Type of Care Integration',
                    'goal_of_care' => 'Goal of Care',
                ],
            ],
            'other_cancer_directed_therapies' => [
                'title' => 'FORM 2G: OTHER TREATMENTS',
                'columns' => [
                    'date_of_therapy' => 'Date of Therapy',
                    'type_of_cancer_directed_therapy' => 'Type of Cancer-Directed Therapy',
                    'goal_of_cancer_directed_therapy' => 'Goal',
                ],
            ],
        ];
        $otherSpecifics = [
            'drug_types' => 'drug_type_other',
            'treatment_phase' => 'treatment_phase_other',
            'goal_of_anti_cancer_drug_therapy' => 'goal_other',
            'radiotherapy_type' => 'radiotherapy_type_specifics',
            'target_site' => 'target_site_specifics',
            'goal_of_radiotherapy' => 'goal_other',
            'reasons' => 'reason_specifics',
            'type_of_care_integration' => 'type_of_care_integration_other',
            'goal_of_care' => 'goal_of_care_other',
            'type_of_cancer_directed_therapy' => 'type_of_cancer_directed_therapy_other',
            'goal_of_cancer_directed_therapy' => 'goal_of_cancer_directed_therapy_other',
        ];
        $recordValue = function (array $record, string $column) use ($values, $otherSpecifics): string {
            $value = implode(', ', $values($record[$column] ?? null));
            $specific = $otherSpecifics[$column] ?? null;

            if ($specific !== null && filled($record[$specific] ?? null)) {
                $value = trim($value.' ('.$record[$specific].')');
            }

            return $value;
        };
    @endphp

    <main class="paper">
        <div class="scroll-wrap">
            <div class="form-shell text-[12px] leading-tight">
                <header class="px-3 pt-8 pb-3 text-center border-b border-black">
                    <div class="micro font-bold">Republic of the Philippines</div>
                    <div class="micro">Department of Health</div>
                    <div class="text-[15px] font-extrabold tracking-wide">Philippine Cancer Center</div>
                    <h1 class="text-[31px] font-extrabold tracking-[.06em] mt-1 uppercase">Pediatric Hospital-Based
                        Cancer Registry</h1>
                </header>

                <section class="black-bar">
                    FORM 2A: PATIENT FOLLOW-UP FORM
                    <span class="sub-bar">(Part of H B C R FORM 2)</span>
                </section>

                <div class="grid grid-cols-[270px_1fr_220px_200px]">
                    <div class="cell cell-left tiny">Patient’s Health Facility ID No.</div>
                    <div class="cell tiny font-bold">{{ $patientHealthFacilityIdNo }}</div>
                    <div class="cell cell-left tiny">Date of Follow-up / Last Encounter</div>
                    <div class="cell no-right tiny font-bold">{{ $date($followUp->follow_up_last_encounter_date) }}</div>
                </div>

                <div class="grid grid-cols-[270px_1fr]">
                    <div class="cell cell-left tiny">Patient Name</div>
                    <div class="cell no-right tiny font-bold uppercase">{{ $field($formDetails?->full_name) }}</div>
                </div>

                <div class="grid grid-cols-[270px_1fr_270px_1fr]">
                    <div class="cell cell-left tiny">Is there a change in Diagnosis?</div>
                    <div class="cell tiny flex gap-6">
                        <span class="option"><span class="box{{ $checked($followUp->has_change_in_diagnosis === true) }}"></span> Yes</span>
                        <span class="option"><span class="box{{ $checked($followUp->has_change_in_diagnosis === false) }}"></span> No</span>
                    </div>
                    <div class="cell cell-left tiny">More than one primary site being treated in the past quarter?</div>
                    <div class="cell no-right tiny flex gap-6">
                        <span class="option"><span class="box{{ $checked($followUp->has_more_than_one_primary_site === true) }}"></span> Yes</span>
                        <span class="option"><span class="box{{ $checked($followUp->has_more_than_one_primary_site === false) }}"></span> No</span>
                    </div>
                </div>

                @if ($followUp->has_change_in_diagnosis)
                    <div class="grid grid-cols-[270px_1fr]">
                        <div class="cell cell-left tiny">Amendment</div>
                        <div class="cell no-right tiny">
                            @if ($followUp->patientFollowupAmmendment)
                                {{ collect([
                                    $followUp->patientFollowupAmmendment->iccc_specific_classification,
                                    $date($followUp->patientFollowupAmmendment->diagnosis_date),
                                    $followUp->patientFollowupAmmendment->clinical_stage,
                                ])->filter()->implode(' — ') ?: 'Amendment form on file.' }}
                            @else
                                <span class="italic">No amendment form on file.</span>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($primarySites !== [])
                    <div class="grid grid-cols-[270px_1fr]">
                        <div class="cell cell-left tiny">Primary Sites Being Treated</div>
                        <div class="cell no-right tiny">{{ implode(', ', $primarySites) }}</div>
                    </div>
                @endif

                <section class="section-title">TREATMENT</section>

                <div class="grid grid-cols-[270px_1fr]">
                    <div class="cell cell-left tiny">Procedures Administered</div>
                    <div class="cell no-right tiny grid grid-cols-2 gap-y-1">
                        @foreach ($procedureOptions as $procedure => $encodeTo)
                            <span class="option">
                                <span class="box{{ $checked(in_array($procedure, $proceduresAdministered, true)) }}"></span>
                                {{ $procedure }} <span class="micro italic ml-1 text-gray-600">({{ $encodeTo }})</span>
                            </span>
                        @endforeach
                    </div>
                </div>

                @foreach ($treatmentTables as $key => $table)
                    @continue(empty($treatmentRecords[$key]))

                    <section class="section-title">{{ $table['title'] }}</section>

                    <table class="records tiny">
                        <thead>
                            <tr>
                                <th class="w-8">#</th>
                                @foreach ($table['columns'] as $label)
                                    <th>{{ $label }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($treatmentRecords[$key] as $index => $record)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    @foreach ($table['columns'] as $column => $label)
                                        <td>{{ str_contains($column, 'date') ? $date($record[$column] ?? null) : $recordValue($record, $column) }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach

                <section class="section-title">MEDICAL EVALUATION / TREATMENT OUTCOMES</section>

                <div class="grid grid-cols-[270px_1fr]">
                    <div class="cell cell-left tiny">Treatment Status</div>
                    <div class="cell no-right tiny grid grid-cols-3 gap-y-1">
                        @foreach ($treatmentStatusOptions as $option)
                            <span class="option"><span class="box{{ $checked($followUp->treatment_status === $option) }}"></span> {{ $option }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-[270px_1fr]">
                    <div class="cell cell-left tiny">Disease Outcome</div>
                    <div class="cell no-right tiny grid grid-cols-2 gap-y-1">
                        @foreach ($diseaseOutcomeOptions as $option)
                            <span class="option"><span class="box{{ $checked($followUp->disease_outcome === $option) }}"></span> {{ $option }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-[270px_1fr]">
                    <div class="cell cell-left tiny">Date of Disease Outcome</div>
                    <div class="cell no-right tiny font-bold">{{ $date($followUp->disease_outcome_date) }}</div>
                </div>

                @if (str_starts_with((string) $followUp->disease_outcome, 'Death'))
                    <section class="section-title">CAUSE OF DEATH</section>

                    @foreach ([
                        'immediate_cause_of_death' => 'Immediate Cause',
                        'antecedent_cause_of_death' => 'Antecedent Cause',
                        'underlying_cause_of_death' => 'Underlying Cause',
                        'other_significant_condition_related_to_death' => 'Other Significant Condition Directly Related to Death',
                    ] as $column => $label)
                        <div class="grid grid-cols-[270px_1fr]">
                            <div class="cell cell-left tiny">{{ $label }}</div>
                            <div class="cell no-right tiny">{{ $field($followUp->{$column}) }}</div>
                        </div>
                    @endforeach
                @endif

                <section class="section-title">TREATMENT PLAN</section>

                <div class="grid grid-cols-[270px_1fr]">
                    <div class="cell cell-left tiny">Treatment Plan</div>
                    <div class="cell no-right tiny grid grid-cols-2 gap-y-1">
                        @foreach ($treatmentPlanOptions as $option)
                            <span class="option">
                                <span class="box{{ $checked(in_array($option, $treatmentPlan, true)) }}"></span> {{ $option }}
                                @if ($option === 'Others, specify' && filled($followUp->treatment_plan_others))
                                    <span class="ml-1 underline">{{ $followUp->treatment_plan_others }}</span>
                                @endif
                            </span>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-[270px_1fr]">
                    <div class="cell cell-left tiny">Change in Treatment Plan</div>
                    <div class="cell no-right tiny space-y-1">
                        @foreach ($changeProcedureOptions as $procedure => $reasonKey)
                            <div class="option">
                                <span class="box{{ $checked(in_array($procedure, $changeProcedures, true)) }}"></span>
                                <span class="w-48">{{ $procedure }}</span>
                                @if (filled($changeReasons[$reasonKey] ?? null))
                                    <span class="italic">Reason: {{ $changeReasons[$reasonKey] }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <section class="section-title">FINANCIAL SUPPORT MECHANISMS</section>

                <div class="grid grid-cols-[270px_1fr]">
                    <div class="cell cell-left tiny">Did patient avail of any financial support mechanisms?</div>
                    <div class="cell no-right tiny flex gap-6">
                        <span class="option"><span class="box{{ $checked($followUp->availed_financial_support === true) }}"></span> Yes</span>
                        <span class="option"><span class="box{{ $checked($followUp->availed_financial_support === false) }}"></span> No</span>
                    </div>
                </div>

                <div class="grid grid-cols-[270px_1fr]">
                    <div class="cell cell-left tiny border-b-0">Financial Support Mechanisms<br><span class="font-normal italic">(Can be multiple)</span></div>
                    <div class="cell no-right tiny grid grid-cols-2 gap-y-1 border-b-0">
                        @foreach ($financialSupportOptions as $option => $label)
                            <span class="option">
                                <span class="box{{ $checked(in_array($option, $financialSupport, true)) }}"></span> {{ $label }}
                                @if ($option === 'Others, specify' && filled($followUp->financial_support_others))
                                    <span class="ml-1 underline">{{ $followUp->financial_support_others }}</span>
                                @endif
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>
