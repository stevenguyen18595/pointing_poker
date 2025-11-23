<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PointValue extends Model
{
    protected $fillable = [
        'value',
        'label',
        'description',
        'color_class',
        'card_type',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('card_type', $type);
    }

    public function scopeNumbers($query)
    {
        return $query->where('card_type', 'number');
    }

    public function scopeSpecial($query)
    {
        return $query->where('card_type', 'special');
    }
}
