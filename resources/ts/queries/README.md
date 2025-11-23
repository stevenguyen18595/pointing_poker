# React Query with Axios - Usage Examples

## Overview

All React Query hooks are organized in the `resources/ts/queries/` folder, with Axios configured for HTTP requests.

## File Structure

```
resources/ts/
├── queries/
│   ├── index.ts          # Exports all hooks
│   ├── keys.ts           # Query key factory
│   ├── games.ts          # Game-related queries
│   ├── players.ts        # Player-related queries
│   ├── stories.ts        # Story-related queries
│   ├── votes.ts          # Vote-related queries
│   └── pointValues.ts    # Point values queries
├── lib/
│   └── axios.ts          # Axios configuration
└── components/
    └── PointValueCards.tsx # Example component using queries
```

## Import Examples

```tsx
// Import specific hooks
import { useGame, useCreateGame, usePlayers } from "../queries";

// Import query keys for manual cache manipulation
import { queryKeys } from "../queries";
```

## Basic Usage Examples

### 1. Fetching Data

```tsx
function GameComponent({ gameId }: { gameId: string }) {
    const { data: game, isLoading, error } = useGame(gameId);

    if (isLoading) return <div>Loading...</div>;
    if (error) return <div>Error: {error.message}</div>;

    return <div>Game: {game?.name}</div>;
}
```

### 2. Creating Data

```tsx
function CreateGameForm() {
    const createGameMutation = useCreateGame();

    const handleSubmit = async (formData: CreateGameRequest) => {
        try {
            const newGame = await createGameMutation.mutateAsync(formData);
            console.log("Game created:", newGame);
        } catch (error) {
            console.error("Failed to create game:", error);
        }
    };

    return (
        <form onSubmit={handleSubmit}>
            <button type="submit" disabled={createGameMutation.isPending}>
                {createGameMutation.isPending ? "Creating..." : "Create Game"}
            </button>
        </form>
    );
}
```

### 3. Real-time Updates (Polling)

```tsx
function PlayersPanel({ gameId }: { gameId: string }) {
    // Automatically polls every 5 seconds
    const { data: players, isLoading } = usePlayers(gameId);

    return (
        <div>
            <h3>Players ({players?.length || 0})</h3>
            {players?.map((player) => (
                <div key={player.id}>{player.name}</div>
            ))}
        </div>
    );
}
```

### 4. Optimistic Updates

```tsx
function VoteButton({ storyId, pointValueId, gameId }: VoteButtonProps) {
    const submitVoteMutation = useSubmitVote(storyId, gameId);

    const handleVote = () => {
        submitVoteMutation.mutate({ point_value_id: pointValueId });
    };

    return (
        <button onClick={handleVote} disabled={submitVoteMutation.isPending}>
            Vote
        </button>
    );
}
```

## Advanced Patterns

### 1. Dependent Queries

```tsx
function StoryVotes({ gameId }: { gameId: string }) {
    const { data: stories } = useStories(gameId);
    const currentStory = stories?.find((s) => s.is_current);

    // Only fetch votes if there's a current story
    const { data: votes } = useVotes(currentStory?.id.toString() || "", {
        enabled: !!currentStory,
    });

    return (
        <div>
            {currentStory && (
                <div>
                    <h4>{currentStory.title}</h4>
                    <div>Votes: {votes?.length || 0}</div>
                </div>
            )}
        </div>
    );
}
```

### 2. Manual Cache Updates

```tsx
function GameManager({ gameId }: { gameId: string }) {
    const queryClient = useQueryClient();

    const refreshPlayers = () => {
        queryClient.invalidateQueries({
            queryKey: queryKeys.players(gameId),
        });
    };

    const updateGameInCache = (updatedGame: Game) => {
        queryClient.setQueryData(queryKeys.game(gameId), updatedGame);
    };

    return (
        <div>
            <button onClick={refreshPlayers}>Refresh Players</button>
        </div>
    );
}
```

### 3. Error Handling

```tsx
function GameView({ gameId }: { gameId: string }) {
    const { data: game, error, isError, refetch } = useGame(gameId);

    if (isError) {
        const errorMessage = error?.message || "Unknown error occurred";
        const isNetworkError = (error as any)?.status === 0;

        return (
            <div className="error-container">
                <p>Error: {errorMessage}</p>
                {isNetworkError && <p>Please check your internet connection</p>}
                <button onClick={() => refetch()}>Try Again</button>
            </div>
        );
    }

    return <div>{game?.name}</div>;
}
```

## Axios Configuration Features

### 1. Automatic CSRF Token

Axios automatically includes CSRF tokens from the meta tag for non-GET requests.

### 2. Error Handling

-   **401**: Unauthorized access
-   **403**: Forbidden
-   **404**: Not found
-   **422**: Validation errors
-   **419**: CSRF token mismatch
-   **500**: Server errors
-   **Network errors**: Connection issues

### 3. Request/Response Interceptors

-   Adds CSRF token automatically
-   Transforms errors into consistent format
-   Logs errors to console for debugging

## Query Key Management

Use the query key factory for consistent cache management:

```tsx
import { queryKeys } from "../queries";

// Invalidate all games
queryClient.invalidateQueries({ queryKey: queryKeys.games });

// Invalidate specific game
queryClient.invalidateQueries({ queryKey: queryKeys.game(gameId) });

// Invalidate all players for a game
queryClient.invalidateQueries({ queryKey: queryKeys.players(gameId) });
```

## Best Practices

1. **Always handle loading and error states**
2. **Use enabled option for conditional queries**
3. **Leverage staleTime for static data (game statuses, point values)**
4. **Use refetchInterval for real-time data (players, votes)**
5. **Implement optimistic updates for better UX**
6. **Use proper TypeScript types for all queries**
7. **Handle network errors gracefully**
8. **Invalidate related queries after mutations**
