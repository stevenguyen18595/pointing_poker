<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'game_id' => $this->game_id,
            'player_id' => $this->player_id,
            'point_value_id' => $this->point_value_id,
            'voted_at' => $this->voted_at->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            
            // Relationships
            'player' => new PlayerResource($this->whenLoaded('player')),
            'point_value' => new PointValueResource($this->whenLoaded('pointValue')),
            'game' => new GameResource($this->whenLoaded('game')),
        ];
    }
}
