<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use RuntimeException;

/**
 * Loads the PSA's Philippine Standard Geographic Code (PSGC) address list from the copy bundled in
 * database/seeders/data/psgc, so the address dropdowns work on servers without internet access.
 */
class PsgcAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exitCode = Artisan::call('psgc:import', [
            '--path' => database_path('seeders/data/psgc'),
        ]);

        if ($exitCode !== 0) {
            throw new RuntimeException('PSGC address import failed: '.trim(Artisan::output()));
        }

        $this->command?->info(trim(Artisan::output()));
    }
}
