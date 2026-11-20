<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Converts stored nationalities (e.g. FILIPINO) to the upper-cased country (e.g. PHILIPPINES).
     */
    public function up(): void
    {
        foreach (config('nationalities') as $nationality => $country) {
            DB::table('form_demographics')
                ->whereRaw('UPPER(TRIM(nationality)) = ?', [$nationality])
                ->update(['nationality' => strtoupper($country)]);
        }
    }

    /**
     * Converts stored countries back to the upper-cased nationality.
     */
    public function down(): void
    {
        foreach (config('nationalities') as $nationality => $country) {
            DB::table('form_demographics')
                ->where('nationality', strtoupper($country))
                ->update(['nationality' => $nationality]);
        }
    }
};
