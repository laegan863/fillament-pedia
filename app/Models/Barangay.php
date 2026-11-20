<?php

namespace App\Models;

use Database\Factories\BarangayFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * PSGC barangay.
 */
class Barangay extends Model
{
    /** @use HasFactory<BarangayFactory> */
    use HasFactory;

    protected $primaryKey = 'code';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'code',
        'city_municipality_code',
        'name',
    ];

    /**
     * @return BelongsTo<CityMunicipality, $this>
     */
    public function cityMunicipality(): BelongsTo
    {
        return $this->belongsTo(CityMunicipality::class, 'city_municipality_code', 'code');
    }
}
