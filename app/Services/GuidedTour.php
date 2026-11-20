<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * Defines the guided tour shown on each administrator panel page. A tour starts by itself the first
 * time a user opens a page and can be replayed from the "Take a tour" button in the top bar.
 *
 * Each step points at a CSS selector; steps without a target are shown in the middle of the screen,
 * and steps whose target is not on the page (e.g. hidden by permissions) are skipped by the browser.
 */
class GuidedTour
{
    /**
     * Bump to show every tour again to all users after the steps change.
     */
    public const VERSION = 1;

    private const ROUTE_PREFIX = 'filament.administrator.';

    public const PSA_SOURCE_LABEL = 'Source: PSA';

    public const PSA_SOURCE_TOOLTIP = 'These options come from the Philippine Standard Geographic Code (PSGC) published by the Philippine Statistics Authority (PSA).';

    /**
     * @return array{id: string, title: string, steps: list<array{target: ?string, match: string, title: string, body: string}>}|null
     */
    public function forRoute(?string $routeName): ?array
    {
        if (blank($routeName) || ! str_starts_with($routeName, self::ROUTE_PREFIX)) {
            return null;
        }

        $route = Str::after($routeName, self::ROUTE_PREFIX);

        $tour = match (true) {
            $route === 'pages.dashboard' => $this->dashboard(),
            $route === 'pages.patient-forms' => $this->patientForms(),
            $route === 'pages.reports' => $this->reports(),
            in_array($route, ['resources.form-demographics.create', 'resources.form-demographics.edit'], true) => $this->patientRecordForm(),
            str_starts_with($route, 'resources.') && str_ends_with($route, '.index') => $this->recordList(),
            str_starts_with($route, 'resources.') && (str_ends_with($route, '.create') || str_ends_with($route, '.edit')) => $this->recordForm(),
            default => $this->general(),
        };

        return [
            'id' => $route.'.v'.self::VERSION,
            'title' => $tour['title'],
            'steps' => array_map(fn (array $step): array => [
                'target' => $step['target'] ?? null,
                'match' => $step['match'] ?? 'all',
                'title' => $step['title'],
                'body' => $step['body'],
            ], $tour['steps']),
        ];
    }

    /**
     * @return array{title: string, steps: list<array<string, string|null>>}
     */
    private function dashboard(): array
    {
        return [
            'title' => 'Welcome tour',
            'steps' => [
                [
                    'title' => 'Welcome to HBCR PEDIA',
                    'body' => 'This short tour shows you around. A tour like this opens the first time you visit each page, so you can learn as you go.',
                ],
                [
                    'target' => '[data-group-label="Main"]',
                    'title' => 'Main menu',
                    'body' => 'Encode and look up patients here: Patient Records (demographics), Form 1 and Form 2, Patient Forms preview, Reports and User Management.',
                ],
                [
                    'target' => '[data-group-label="Classifications"]',
                    'title' => 'Classifications',
                    'body' => 'The parent, general and specific cancer classifications used by the diagnosis forms.',
                ],
                [
                    'target' => '[data-group-label="Settings"]',
                    'title' => 'Settings',
                    'body' => 'Roles & permissions and the "Noted By" signatories printed on the reports.',
                ],
                [
                    'target' => '.fi-wi-stats-overview-stat',
                    'title' => 'Dashboard',
                    'body' => 'A quick summary of the registry: patient records, cancer diagnoses and follow-ups. Charts and the latest follow-ups are further down.',
                ],
                [
                    'target' => '.fi-user-menu-trigger',
                    'title' => 'Your account',
                    'body' => 'Switch between light and dark mode or sign out from here.',
                ],
                [
                    'target' => '[data-tour="tour-button"]',
                    'title' => 'Need help again?',
                    'body' => 'Click this button on any page to replay its tour.',
                ],
            ],
        ];
    }

    /**
     * Fallback for pages without a tour of their own.
     *
     * @return array{title: string, steps: list<array<string, string|null>>}
     */
    private function general(): array
    {
        return [
            'title' => 'Page tour',
            'steps' => [
                [
                    'target' => '.fi-sidebar-nav',
                    'title' => 'Navigation',
                    'body' => 'Use the menu on the left to move between Patient Records, Forms, Reports and Settings.',
                ],
                [
                    'target' => '[data-tour="tour-button"]',
                    'title' => 'Need help?',
                    'body' => 'Click this button on any page to replay its tour.',
                ],
            ],
        ];
    }

    /**
     * @return array{title: string, steps: list<array<string, string|null>>}
     */
    private function patientRecordForm(): array
    {
        return [
            'title' => 'Patient record tour',
            'steps' => [
                [
                    'title' => 'Encoding a patient record',
                    'body' => 'Fill in the patient\'s demographics section by section. Fields marked with * are required.',
                ],
                [
                    'target' => '[data-tour="health-facility-id"]',
                    'title' => 'Start with the Health Facility ID',
                    'body' => 'Type the patient\'s Health Facility ID No. and click Validate. The system checks it is not yet registered and loads the patient\'s details from the hospital system for you.',
                ],
                [
                    'target' => '[data-tour="psgc-birthplace"]',
                    'title' => 'Place of birth — from PSA',
                    'body' => 'Province and city/municipality lists come from the PSA\'s Philippine Standard Geographic Code (PSGC). Look for the "'.self::PSA_SOURCE_LABEL.'" badge.',
                ],
                [
                    'target' => '[data-tour="psgc-permanent-address"]',
                    'title' => 'Address — from PSA',
                    'body' => 'These highlighted fields are the official PSA PSGC list. Choose Region first, then Province, City / Municipality and Barangay — each list narrows to the one before it. Addresses loaded from the hospital system are matched to PSA names; please double-check them.',
                ],
                [
                    'target' => '[data-tour="same-as-permanent"]',
                    'title' => 'Same address?',
                    'body' => 'Tick this to copy the permanent address into the current address.',
                ],
                [
                    'target' => '[data-tour="psgc-current-address"]',
                    'title' => 'Current address — from PSA',
                    'body' => 'Also taken from the PSA PSGC list and filled in the same way.',
                ],
                [
                    'target' => '[data-tour="not-applicable"]',
                    'title' => 'No PhilHealth, phone or email?',
                    'body' => 'Tick the matching N/A box instead of leaving the field blank.',
                ],
                [
                    'target' => '.fi-page .fi-sc-actions',
                    'match' => 'last',
                    'title' => 'Save',
                    'body' => 'When everything is filled in, save the record here.',
                ],
            ],
        ];
    }

    /**
     * @return array{title: string, steps: list<array<string, string|null>>}
     */
    private function recordList(): array
    {
        return [
            'title' => 'List tour',
            'steps' => [
                [
                    'target' => '.fi-header-actions-ctn',
                    'title' => 'Add a new record',
                    'body' => 'Use this button to create a new record.',
                ],
                [
                    'target' => '.fi-ta-search-field',
                    'title' => 'Search',
                    'body' => 'Type a name or ID to find a record quickly.',
                ],
                [
                    'target' => '.fi-ta-ctn',
                    'title' => 'Records',
                    'body' => 'Click a row or its actions to view or edit it. Use the column headers to sort.',
                ],
            ],
        ];
    }

    /**
     * @return array{title: string, steps: list<array<string, string|null>>}
     */
    private function recordForm(): array
    {
        return [
            'title' => 'Form tour',
            'steps' => [
                [
                    'title' => 'Filling in this form',
                    'body' => 'Work through each section from top to bottom. Fields marked with * are required, and some fields appear only after you answer the question before them.',
                ],
                [
                    'target' => '.fi-page .fi-sc-actions',
                    'match' => 'last',
                    'title' => 'Save',
                    'body' => 'Save the record here when you are done.',
                ],
            ],
        ];
    }

    /**
     * @return array{title: string, steps: list<array<string, string|null>>}
     */
    private function patientForms(): array
    {
        return [
            'title' => 'Patient forms tour',
            'steps' => [
                [
                    'target' => '[data-tour="patient-forms-search"]',
                    'title' => 'Find a patient',
                    'body' => 'Search by Health Facility ID No. or name. Matching patients appear below as you type.',
                ],
                [
                    'title' => 'Preview Form 1 or Form 2',
                    'body' => 'Pick Form 1 (baseline) or Form 2 (follow-up) on a patient\'s row to preview and print it.',
                ],
            ],
        ];
    }

    /**
     * @return array{title: string, steps: list<array<string, string|null>>}
     */
    private function reports(): array
    {
        return [
            'title' => 'Reports tour',
            'steps' => [
                [
                    'target' => '[data-tour="report-templates"] table',
                    'match' => 'first',
                    'title' => 'Reporting forms',
                    'body' => 'Every reporting form is listed in tables like this one (Form 1 and Form 2). Click Generate on the form you need and choose the reporting period.',
                ],
                [
                    'title' => 'Preview and export',
                    'body' => 'Once generated, the report opens in a preview. You can reopen it anytime and export it to PDF.',
                ],
            ],
        ];
    }
}
