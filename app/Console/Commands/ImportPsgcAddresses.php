<?php

namespace App\Console\Commands;

use App\Models\Barangay;
use App\Models\CityMunicipality;
use App\Models\Province;
use App\Models\Region;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Imports the Philippine Standard Geographic Code (PSGC) masterlist used by the address dropdowns.
 *
 * Data comes from the PSGC API (https://psgc.gitlab.io/api), which mirrors the PSA publication.
 * A copy is bundled in database/seeders/data/psgc and loaded by PsgcAddressSeeder, for servers
 * without internet access. Any folder with the same four JSON files can be passed with --path.
 */
#[Signature('psgc:import
    {--source=https://psgc.gitlab.io/api : Base URL of the PSGC API}
    {--path= : Folder with regions.json, provinces.json, cities-municipalities.json and barangays.json to import instead of downloading}')]
#[Description('Import Philippine regions, provinces, cities/municipalities and barangays from the PSGC')]
class ImportPsgcAddresses extends Command
{
    private const CHUNK_SIZE = 1000;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $regions = $this->fetch('regions');
            $provinces = $this->fetch('provinces');
            $citiesMunicipalities = $this->fetch('cities-municipalities');
            $barangays = $this->fetch('barangays');
        } catch (RuntimeException $exception) {
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }

        $now = now();

        DB::transaction(function () use ($regions, $provinces, $citiesMunicipalities, $barangays, $now): void {
            $this->replace(Region::class, array_map(fn (array $region): array => [
                'code' => $region['code'],
                'name' => $this->regionDisplayName($region),
                'created_at' => $now,
                'updated_at' => $now,
            ], $regions));

            $this->replace(Province::class, array_map(fn (array $province): array => [
                'code' => $province['code'],
                'region_code' => $province['regionCode'],
                'name' => $province['name'],
                'created_at' => $now,
                'updated_at' => $now,
            ], $provinces));

            $this->replace(CityMunicipality::class, array_map(fn (array $cityMunicipality): array => [
                'code' => $cityMunicipality['code'],
                'region_code' => $cityMunicipality['regionCode'],
                'province_code' => $cityMunicipality['provinceCode'] ?: null,
                'name' => $cityMunicipality['name'],
                'is_city' => (bool) ($cityMunicipality['isCity'] ?? false),
                'created_at' => $now,
                'updated_at' => $now,
            ], $citiesMunicipalities));

            $this->replace(Barangay::class, array_map(fn (array $barangay): array => [
                'code' => $barangay['code'],
                'city_municipality_code' => $barangay['cityCode'] ?: $barangay['municipalityCode'],
                'name' => $barangay['name'],
                'created_at' => $now,
                'updated_at' => $now,
            ], $barangays));
        });

        $this->components->info(sprintf(
            'Imported %d regions, %d provinces, %d cities/municipalities and %d barangays.',
            count($regions),
            count($provinces),
            count($citiesMunicipalities),
            count($barangays),
        ));

        return self::SUCCESS;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function fetch(string $resource): array
    {
        $path = $this->option('path');

        if (filled($path)) {
            $file = rtrim($path, '/\\').DIRECTORY_SEPARATOR."{$resource}.json";

            if (! is_file($file)) {
                throw new RuntimeException("PSGC file not found: {$file}");
            }

            $data = json_decode((string) file_get_contents($file), true);
        } else {
            $url = rtrim((string) $this->option('source'), '/')."/{$resource}/";

            try {
                $this->components->task("Downloading {$resource}", function () use ($url, &$data): void {
                    $data = Http::timeout(120)
                        ->retry(3, 1000, throw: false)
                        ->get($url)
                        ->json();
                });
            } catch (ConnectionException $exception) {
                throw new RuntimeException("Unable to download {$url}: {$exception->getMessage()}. To use the copy bundled with the application instead, run: php artisan db:seed --class=PsgcAddressSeeder");
            }
        }

        if (! is_array($data) || $data === []) {
            throw new RuntimeException("Unable to read PSGC {$resource}.");
        }

        return $data;
    }

    /**
     * Formats the region like the hospital records, e.g. "Region VI (Western Visayas)".
     *
     * @param  array{name: string, regionName?: string}  $region
     */
    private function regionDisplayName(array $region): string
    {
        $regionName = trim((string) ($region['regionName'] ?? ''));

        if ($regionName === '' || strcasecmp($regionName, $region['name']) === 0) {
            return $region['name'];
        }

        return "{$regionName} ({$region['name']})";
    }

    /**
     * @param  class-string<Model>  $model
     * @param  list<array<string, mixed>>  $rows
     */
    private function replace(string $model, array $rows): void
    {
        $model::query()->delete();

        foreach (array_chunk($rows, self::CHUNK_SIZE) as $chunk) {
            $model::query()->insert($chunk);
        }
    }
}
