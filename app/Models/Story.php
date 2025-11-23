<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Story extends Model
{
    protected $fillable = [
        'game_id',
        'title',
        'description',
        'acceptance_criteria',
        'estimated_points',
        'sort_order',
        'is_current',
        'is_completed',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'is_completed' => 'boolean',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function setCurrent()
    {
        // Unset other current stories in the game
        $this->game->stories()->where('id', '!=', $this->id)->update(['is_current' => false]);
        
        // Set this story as current
        $this->update(['is_current' => true]);
    }

    public function complete(string $estimatedPoints = null)
    {
        $this->update([
            'is_completed' => true,
            'is_current' => false,
            'estimated_points' => $estimatedPoints,
        ]);
    }

    public function hasVotes(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->votes()->count() > 0
        );
    }

    public function allPlayersVoted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->votes()->count() >= $this->game->players()->count()
        );
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_completed', false);
    }
}
