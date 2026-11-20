<?php

namespace App\Services;

use App\Models\Barangay;
use App\Models\CityMunicipality;
use App\Models\Province;
use App\Models\Region;

/**
 * Builds PSGC address dropdown options and matches free-text addresses (e.g. from the hospital
 * information system) to their PSGC names. Addresses are stored by name, so all inputs are names.
 */
class PsgcAddressLookup
{
    public const METRO_MANILA = 'Metro Manila';

    private const NCR_REGION_CODE = '130000000';

    /**
     * @return array<string, string>
     */
    public function regionOptions(): array
    {
        return $this->toOptions(Region::query()->orderBy('code')->pluck('name')->all());
    }

    /**
     * @return array<string, string>
     */
    public function provinceOptions(?string $regionName): array
    {
        $regionCode = $this->regionCode($regionName);

        if ($regionCode === null) {
            return [];
        }

        return $this->toOptions(
            Province::query()->where('region_code', $regionCode)->orderBy('name')->pluck('name')->all(),
        );
    }

    /**
     * Lists the cities/municipalities of the province, or of the whole region when no province
     * is chosen (NCR and other cities without a province).
     *
     * @return array<string, string>
     */
    public function cityMunicipalityOptions(?string $regionName, ?string $provinceName): array
    {
        $regionCode = $this->regionCode($regionName);

        if ($regionCode === null) {
            return [];
        }

        $provinceCode = $this->provinceCode($regionCode, $provinceName);

        return $this->toOptions(
            CityMunicipality::query()
                ->where('region_code', $regionCode)
                ->when($provinceCode !== null, fn ($query) => $query->where('province_code', $provinceCode))
                ->orderBy('name')
                ->pluck('name')
                ->all(),
        );
    }

    /**
     * @return array<string, string>
     */
    public function barangayOptions(?string $regionName, ?string $provinceName, ?string $cityMunicipalityName): array
    {
        $cityMunicipalityCode = $this->cityMunicipalityCode($regionName, $provinceName, $cityMunicipalityName);

        if ($cityMunicipalityCode === null) {
            return [];
        }

        return $this->toOptions(
            Barangay::query()
                ->where('city_municipality_code', $cityMunicipalityCode)
                ->orderBy('name')
                ->pluck('name')
                ->all(),
        );
    }

    /**
     * Matches a free-text address to PSGC names. Each part that has no PSGC match keeps its original text.
     *
     * @return array{region: ?string, province: ?string, city_municipality: ?string, barangay: ?string}
     */
    public function matchAddress(?string $region, ?string $province, ?string $cityMunicipality, ?string $barangay): array
    {
        $matchedRegion = $this->match($region, array_keys($this->regionOptions()));
        $matchedProvince = $this->match($province, array_keys($this->provinceOptions($matchedRegion)));
        $matchedCityMunicipality = $this->match(
            $cityMunicipality,
            array_keys($this->cityMunicipalityOptions($matchedRegion, $matchedProvince)),
        );
        $matchedBarangay = $this->match(
            $barangay,
            array_keys($this->barangayOptions($matchedRegion, $matchedProvince, $matchedCityMunicipality)),
        );

        return [
            'region' => $matchedRegion,
            'province' => $matchedProvince,
            'city_municipality' => $matchedCityMunicipality,
            'barangay' => $matchedBarangay,
        ];
    }

    /**
     * Provinces for the place of birth, plus "Metro Manila" for the NCR cities, which have no province.
     *
     * @return array<string, string>
     */
    public function birthProvinceOptions(): array
    {
        $options = $this->toOptions(Province::query()->orderBy('name')->pluck('name')->all());

        $options[self::METRO_MANILA] = self::METRO_MANILA;

        ksort($options);

        return $options;
    }

    /**
     * Cities/municipalities of the birth province. Cities without a province outside NCR
     * (e.g. City of Isabela, City of Cotabato) are listed under every province of their region.
     *
     * @return array<string, string>
     */
    public function birthCityMunicipalityOptions(?string $provinceName): array
    {
        if (blank($provinceName)) {
            return [];
        }

        if ($provinceName === self::METRO_MANILA) {
            return $this->toOptions(
                CityMunicipality::query()
                    ->where('region_code', self::NCR_REGION_CODE)
                    ->whereNull('province_code')
                    ->orderBy('name')
                    ->pluck('name')
                    ->all(),
            );
        }

        $province = Province::query()->where('name', $provinceName)->first();

        if ($province === null) {
            return [];
        }

        return $this->toOptions(
            CityMunicipality::query()
                ->where(fn ($query) => $query
                    ->where('province_code', $province->code)
                    ->orWhere(fn ($query) => $query
                        ->where('region_code', $province->region_code)
                        ->whereNull('province_code')))
                ->orderBy('name')
                ->pluck('name')
                ->all(),
        );
    }

    /**
     * Matches a free-text birthplace (the hospital record only has the city/municipality) to PSGC
     * names. The province is filled in when the city/municipality name exists in only one province.
     *
     * @return array{province: ?string, city_municipality: ?string}
     */
    public function matchBirthplace(?string $cityMunicipality): array
    {
        if (blank($cityMunicipality)) {
            return ['province' => null, 'city_municipality' => null];
        }

        $normalizedCityMunicipality = $this->normalize($cityMunicipality);

        $matches = CityMunicipality::query()
            ->with('province')
            ->get(['code', 'region_code', 'province_code', 'name'])
            ->filter(fn (CityMunicipality $candidate): bool => $this->normalize($candidate->name) === $normalizedCityMunicipality);

        if ($matches->count() !== 1) {
            return ['province' => null, 'city_municipality' => trim($cityMunicipality)];
        }

        $match = $matches->first();

        return [
            'province' => $match->province?->name ?? ($match->region_code === self::NCR_REGION_CODE ? self::METRO_MANILA : null),
            'city_municipality' => $match->name,
        ];
    }

    /**
     * Adds the current value to the options so values that are not in the PSGC (older records,
     * unmatched hospital data) still display and pass validation.
     *
     * @param  array<string, string>  $options
     * @return array<string, string>
     */
    public function withCurrentValue(array $options, ?string $currentValue): array
    {
        if (filled($currentValue) && ! array_key_exists($currentValue, $options)) {
            $options[$currentValue] = $currentValue;
        }

        return $options;
    }

    private function regionCode(?string $regionName): ?string
    {
        if (blank($regionName)) {
            return null;
        }

        return Region::query()->where('name', $regionName)->value('code');
    }

    private function provinceCode(string $regionCode, ?string $provinceName): ?string
    {
        if (blank($provinceName)) {
            return null;
        }

        return Province::query()
            ->where('region_code', $regionCode)
            ->where('name', $provinceName)
            ->value('code');
    }

    private function cityMunicipalityCode(?string $regionName, ?string $provinceName, ?string $cityMunicipalityName): ?string
    {
        $regionCode = $this->regionCode($regionName);

        if ($regionCode === null || blank($cityMunicipalityName)) {
            return null;
        }

        $provinceCode = $this->provinceCode($regionCode, $provinceName);

        return CityMunicipality::query()
            ->where('region_code', $regionCode)
            ->when($provinceCode !== null, fn ($query) => $query->where('province_code', $provinceCode))
            ->where('name', $cityMunicipalityName)
            ->value('code');
    }

    /**
     * @param  list<string>  $candidates
     */
    private function match(?string $value, array $candidates): ?string
    {
        if (blank($value)) {
            return null;
        }

        $normalizedValue = $this->normalize($value);

        foreach ($candidates as $candidate) {
            if ($this->normalize($candidate) === $normalizedValue) {
                return $candidate;
            }
        }

        return trim($value);
    }

    /**
     * Uppercases, collapses spaces and treats "City of X" and "X City" as the same name.
     */
    private function normalize(string $value): string
    {
        $value = mb_strtoupper(trim((string) preg_replace('/\s+/', ' ', $value)));

        if (str_starts_with($value, 'CITY OF ')) {
            $value = substr($value, strlen('CITY OF ')).' CITY';
        }

        return $value;
    }

    /**
     * @param  list<string>  $names
     * @return array<string, string>
     */
    private function toOptions(array $names): array
    {
        return array_combine($names, $names);
    }
}
