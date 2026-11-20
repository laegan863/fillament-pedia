<?php

namespace App\Models;

use Database\Factories\CityMunicipalityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * PSGC city or municipality. NCR cities have no province.
 */
class CityMunicipality extends Model
{
    /** @use HasFactory<CityMunicipalityFactory> */
    use HasFactory;

    protected $table = 'cities_municipalities';

    protected $primaryKey = 'code';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'code',
        'region_code',
        'province_code',
        'name',
        'is_city',
    ];

    protected function casts(): array
    {
        return [
            'is_city' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Region, $this>
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_code', 'code');
    }

    /**
     * @return BelongsTo<Province, $this>
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_code', 'code');
    }

    /**
     * @return HasMany<Barangay, $this>
     */
    public function barangays(): HasMany
    {
        return $this->hasMany(Barangay::class, 'city_municipality_code', 'code');
    }
}
