// Types for the Planning Poker API
export interface Game {
    id: number;
    name: string;
    game_code: string;
    status_id: number;
    created_by: number;
    settings: Record<string, any> | null;
    started_at: string | null;
    completed_at: string | null;
    created_at: string;
    updated_at: string;
    status?: GameStatus;
    players?: Player[];
}

export interface GameStatus {
    id: number;
    name: string;
    label: string;
    description: string;
    color_class: string;
    sort_order: number;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

export interface Player {
    id: number;
    name: string;
    game_id: number;
    user_id: number | null;
    session_id: string | null;
    is_moderator: boolean;
    last_seen_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface Vote {
    id: number;
    game_id: number;
    player_id: number;
    point_value_id: number;
    voted_at: string;
    created_at: string;
    updated_at: string;
    player?: Player;
    point_value?: PointValue;
}

export interface PointValue {
    id: number;
    value: string;
    label: string;
    description: string | null;
    color_class: string | null;
    card_type: "number" | "special" | "break";
    sort_order: number;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

// API Request/Response types
export interface CreateGameRequest {
    name: string;
    description?: string;
    settings?: Record<string, any>;
    creator_name?: string;
}

export interface JoinGameRequest {
    game_code: string;
    player_name: string;
    is_moderator?: boolean;
}

export interface SubmitVoteRequest {
    game_id: number;
    point_value_id: number;
    player_id: number;
}

// API Response wrapper
export interface ApiResponse<T> {
    data: T;
    message?: string;
    meta?: {
        pagination?: {
            current_page: number;
            total_pages: number;
            total_count: number;
            per_page: number;
        };
    };
}
