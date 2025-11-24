import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { apiClient, unwrapApiResponse } from "../lib/axios";
import { queryKeys } from "./keys";
import type {
    Game,
    GameStatus,
    CreateGameRequest,
    JoinGameRequest,
    ApiResponse,
    Player,
} from "../types/api";

// Game Status Queries
export function useGameStatuses() {
    return useQuery({
        queryKey: queryKeys.gameStatuses,
        queryFn: async (): Promise<GameStatus[]> => {
            const response = await apiClient.get<ApiResponse<GameStatus[]>>(
                "/game-statuses"
            );
            return unwrapApiResponse(response);
        },
        staleTime: 1000 * 60 * 15, // 15 minutes - statuses don't change often
    });
}

// Game Queries
export function useGame(gameId: string) {
    return useQuery({
        queryKey: queryKeys.game(gameId),
        queryFn: async (): Promise<Game> => {
            const response = await apiClient.get<ApiResponse<Game>>(
                `/games/${gameId}`
            );
            return response.data.data;
        },
        enabled: !!gameId,
    });
}

export function useGames() {
    return useQuery({
        queryKey: queryKeys.games,
        queryFn: async (): Promise<Game[]> => {
            const response = await apiClient.get<ApiResponse<Game[]>>("/games");
            return response.data.data;
        },
    });
}

// Game Mutations
export function useCreateGame() {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async (
            data: CreateGameRequest
        ): Promise<{ game: Game; player: Player | null }> => {
            const response = await apiClient.post<
                ApiResponse<{ game: Game; player: Player | null }>
            >("/games", data);
            return response.data.data;
        },
        onSuccess: (result) => {
            // Update the games list cache
            queryClient.invalidateQueries({ queryKey: queryKeys.games });

            // Add the new game to cache
            queryClient.setQueryData(
                queryKeys.game(result.game.id),
                result.game
            );
        },
    });
}

export function useJoinGame() {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async (
            data: JoinGameRequest
        ): Promise<{ game: Game; player: Player }> => {
            const response = await apiClient.post<
                ApiResponse<{ game: Game; player: Player }>
            >("/games/join", data);
            return response.data.data;
        },
        onSuccess: ({ game, player }) => {
            // Update game cache
            queryClient.setQueryData(queryKeys.game(game.id), game);

            // Invalidate players list to include new player
            queryClient.invalidateQueries({
                queryKey: queryKeys.players(game.id),
            });
        },
    });
}

export function useUpdateGameStatus(gameId: string) {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async (statusId: number): Promise<Game> => {
            const response = await apiClient.patch<ApiResponse<Game>>(
                `/games/${gameId}/status`,
                { status_id: statusId }
            );
            return response.data.data;
        },
        onSuccess: (updatedGame) => {
            // Update game cache
            queryClient.setQueryData(queryKeys.game(gameId), updatedGame);

            // Invalidate games list
            queryClient.invalidateQueries({ queryKey: queryKeys.games });
        },
    });
}
