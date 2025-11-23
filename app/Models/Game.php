<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class Game extends Model
{
    protected $fillable = [
        'name',
        'game_code',
        'status_id',
        'settings',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($game) {
            if (!$game->game_code) {
                $game->game_code = strtoupper(Str::random(8));
            }
        });
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(GameStatus::class);
    }

    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    public function stories(): HasMany
    {
        return $this->hasMany(Story::class);
    }

    public function currentStory(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Story::class)->where('is_current', true);
    }

    public function isActive(): Attribute
    {
        return Attribute::make(
            get: fn () => in_array($this->status?->name, ['waiting', 'voting'])
        );
    }
}
