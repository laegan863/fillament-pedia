<?php

namespace App\Models;

use Database\Factories\RegionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * PSGC region, e.g. "Region VI (Western Visayas)".
 */
class Region extends Model
{
    /** @use HasFactory<RegionFactory> */
    use HasFactory;

    protected $primaryKey = 'code';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'code',
        'name',
    ];

    /**
     * @return HasMany<Province, $this>
     */
    public function provinces(): HasMany
    {
        return $this->hasMany(Province::class, 'region_code', 'code');
    }

    /**
     * @return HasMany<CityMunicipality, $this>
     */
    public function citiesMunicipalities(): HasMany
    {
        return $this->hasMany(CityMunicipality::class, 'region_code', 'code');
    }
}
