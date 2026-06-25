<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeneralClassification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];

    public function specificClassifications(): HasMany
    {
        return $this->hasMany(SpecificClassification::class);
    }
}
