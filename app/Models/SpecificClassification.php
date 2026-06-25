<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpecificClassification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'general_classification_id',
        'parent_classification_id',
    ];

    protected $casts = [
        'general_classification_id' => 'integer',
        'parent_classification_id' => 'integer',
    ];

    public function generalClassification(): BelongsTo
    {
        return $this->belongsTo(GeneralClassification::class);
    }

    public function parentClassification(): BelongsTo
    {
        return $this->belongsTo(ParentClassification::class);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('name');
    }

    /**
     * @return array<int, string>
     */
    public static function options(): array
    {
        return static::query()
            ->ordered()
            ->pluck('name', 'id')
            ->all();
    }

    public static function findForFormValue(int|string|null $value): ?self
    {
        if (blank($value)) {
            return null;
        }

        return static::query()
            ->with(['generalClassification', 'parentClassification'])
            ->when(
                is_numeric($value),
                fn (Builder $query): Builder => $query->whereKey((int) $value),
                fn (Builder $query): Builder => $query->where('name', $value),
            )
            ->first();
    }
}
