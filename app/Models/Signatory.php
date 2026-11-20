<?php

namespace App\Models;

use Database\Factories\SignatoryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A person who can be selected as the "Noted by" signatory on reporting forms.
 */
class Signatory extends Model
{
    /** @use HasFactory<SignatoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'position',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * @param  Builder<Signatory>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }
}
