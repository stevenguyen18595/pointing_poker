// Query key factory for consistent key management
export const queryKeys = {
    // Game-related queries
    games: ["games"] as const,
    game: (id: string | number) => ["games", id] as const,
    gameStatuses: ["gameStatuses"] as const,

    // Player-related queries
    players: (gameId: string | number) => ["players", gameId] as const,
    player: (gameId: string | number, playerId: string | number) =>
        ["players", gameId, playerId] as const,

    // Story-related queries
    stories: (gameId: string | number) => ["stories", gameId] as const,
    story: (storyId: string | number) => ["stories", storyId] as const,

    // Vote-related queries
    votes: (storyId: string | number) => ["votes", storyId] as const,
    vote: (storyId: string | number, playerId: string | number) =>
        ["votes", storyId, playerId] as const,

    // Point values
    pointValues: ["pointValues"] as const,
} as const;
