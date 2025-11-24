<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vote extends Model
{
    protected $fillable = [
        'game_id',
        'player_id',
        'point_value_id',
        'voted_at',
    ];

    protected $casts = [
        'voted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($vote) {
            if (!$vote->voted_at) {
                $vote->voted_at = now();
            }
        });
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function pointValue(): BelongsTo
    {
        return $this->belongsTo(PointValue::class);
    }

    public function scopeForGame($query, $gameId)
    {
        return $query->where('game_id', $gameId);
    }

    public function scopeForPlayer($query, $playerId)
    {
        return $query->where('player_id', $playerId);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('voted_at', 'desc');
    }
}
