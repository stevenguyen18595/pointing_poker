<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'game_code' => $this->game_code,
            'status_id' => $this->status_id,
            'created_by' => $this->created_by,
            'settings' => $this->settings,
            'started_at' => $this->started_at?->toISOString(),
            'completed_at' => $this->completed_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'is_active' => $this->is_active,
            
            // Relationships
            'status' => new GameStatusResource($this->whenLoaded('status')),
            'creator' => $this->whenLoaded('creator', fn() => [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
            ]),
            'players' => PlayerResource::collection($this->whenLoaded('players')),
            'votes' => VoteResource::collection($this->whenLoaded('votes')),
            
            // Computed attributes
            'players_count' => $this->whenCounted('players'),
            'votes_count' => $this->whenCounted('votes'),
        ];
    }
}
