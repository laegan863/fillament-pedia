<?php

namespace App\Models;

use Database\Factories\ProvinceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * PSGC province.
 */
class Province extends Model
{
    /** @use HasFactory<ProvinceFactory> */
    use HasFactory;

    protected $primaryKey = 'code';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'code',
        'region_code',
        'name',
    ];

    /**
     * @return BelongsTo<Region, $this>
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_code', 'code');
    }

    /**
     * @return HasMany<CityMunicipality, $this>
     */
    public function citiesMunicipalities(): HasMany
    {
        return $this->hasMany(CityMunicipality::class, 'province_code', 'code');
    }
}
