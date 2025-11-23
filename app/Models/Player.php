<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Player extends Model
{
    protected $fillable = [
        'name',
        'game_id',
        'session_id',
        'is_moderator',
        'last_seen_at',
    ];

    protected $casts = [
        'is_moderator' => 'boolean',
        'last_seen_at' => 'datetime',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function updateActivity()
    {
        $this->update(['last_seen_at' => now()]);
    }

    public function isOnline(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->last_seen_at && $this->last_seen_at->gt(now()->subMinutes(5))
        );
    }

    public function scopeOnline($query)
    {
        return $query->where('last_seen_at', '>', now()->subMinutes(5));
    }
}
