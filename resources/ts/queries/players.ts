import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { apiClient } from "../lib/axios";
import { queryKeys } from "./keys";
import type { Player, ApiResponse } from "../types/api";

// Player Queries
export function usePlayers(gameId: string) {
    return useQuery({
        queryKey: queryKeys.players(gameId),
        queryFn: async (): Promise<Player[]> => {
            const response = await apiClient.get<ApiResponse<Player[]>>(
                `/games/${gameId}/players`
            );
            return response.data.data;
        },
        enabled: !!gameId,
        refetchInterval: 5000, // Poll every 5 seconds for real-time updates
        refetchIntervalInBackground: true,
    });
}

export function usePlayer(gameId: string, playerId: string) {
    return useQuery({
        queryKey: queryKeys.player(gameId, playerId),
        queryFn: async (): Promise<Player> => {
            const response = await apiClient.get<ApiResponse<Player>>(
                `/games/${gameId}/players/${playerId}`
            );
            return response.data.data;
        },
        enabled: !!gameId && !!playerId,
    });
}

// Player Mutations
export function useUpdatePlayer(gameId: string, playerId: string) {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async (data: Partial<Player>): Promise<Player> => {
            const response = await apiClient.patch<ApiResponse<Player>>(
                `/games/${gameId}/players/${playerId}`,
                data
            );
            return response.data.data;
        },
        onSuccess: (updatedPlayer) => {
            // Update player cache
            queryClient.setQueryData(
                queryKeys.player(gameId, playerId),
                updatedPlayer
            );

            // Invalidate players list
            queryClient.invalidateQueries({
                queryKey: queryKeys.players(gameId),
            });
        },
    });
}

export function useUpdatePlayerActivity(gameId: string, playerId: string) {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async (): Promise<void> => {
            await apiClient.post(
                `/games/${gameId}/players/${playerId}/activity`
            );
        },
        onSuccess: () => {
            // Invalidate players list to update last_seen_at
            queryClient.invalidateQueries({
                queryKey: queryKeys.players(gameId),
            });
        },
    });
}

export function useRemovePlayer(gameId: string) {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async (playerId: string): Promise<void> => {
            await apiClient.delete(`/games/${gameId}/players/${playerId}`);
        },
        onSuccess: () => {
            // Invalidate players list
            queryClient.invalidateQueries({
                queryKey: queryKeys.players(gameId),
            });
        },
    });
}
