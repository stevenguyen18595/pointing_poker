<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'game_id' => $this->game_id,
            'title' => $this->title,
            'description' => $this->description,
            'acceptance_criteria' => $this->acceptance_criteria,
            'estimated_points' => $this->estimated_points,
            'sort_order' => $this->sort_order,
            'is_current' => $this->is_current,
            'is_completed' => $this->is_completed,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'has_votes' => $this->has_votes,
            'all_players_voted' => $this->all_players_voted,
            
            // Relationships
            'votes' => VoteResource::collection($this->whenLoaded('votes')),
            'votes_count' => $this->whenCounted('votes'),
        ];
    }
}
