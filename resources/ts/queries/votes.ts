import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { apiClient } from "../lib/axios";
import { queryKeys } from "./keys";
import type { Vote, SubmitVoteRequest, ApiResponse } from "../types/api";

// Vote Queries
export function useVotes(gameId: string) {
    return useQuery({
        queryKey: queryKeys.votes(gameId),
        queryFn: async (): Promise<Vote[]> => {
            const response = await apiClient.get<ApiResponse<Vote[]>>(
                `/games/${gameId}/votes`
            );
            return response.data.data;
        },
        enabled: !!gameId,
        refetchInterval: 2000, // Poll every 2 seconds during voting
        refetchIntervalInBackground: true,
    });
}

export function useVote(gameId: string, playerId: string) {
    return useQuery({
        queryKey: queryKeys.vote(gameId, playerId),
        queryFn: async (): Promise<Vote | null> => {
            try {
                const response = await apiClient.get<ApiResponse<Vote>>(
                    `/games/${gameId}/votes/${playerId}`
                );
                return response.data.data;
            } catch (error: any) {
                // If vote doesn't exist (404), return null
                if (error.status === 404) {
                    return null;
                }
                throw error;
            }
        },
        enabled: !!gameId && !!playerId,
    });
}

// Vote Mutations
export function useSubmitVote(gameId: string) {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async (data: SubmitVoteRequest): Promise<Vote> => {
            const response = await apiClient.post<ApiResponse<Vote>>(
                `/games/${gameId}/votes`,
                data
            );
            return response.data.data;
        },
        onSuccess: (newVote) => {
            // Update vote cache
            queryClient.setQueryData(
                queryKeys.vote(gameId, newVote.player_id.toString()),
                newVote
            );

            // Invalidate votes list
            queryClient.invalidateQueries({
                queryKey: queryKeys.votes(gameId),
            });

            // Invalidate game to update status
            queryClient.invalidateQueries({
                queryKey: queryKeys.game(gameId),
            });
        },
    });
}

export function useUpdateVote(gameId: string) {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async ({
            voteId,
            data,
        }: {
            voteId: string;
            data: Partial<SubmitVoteRequest>;
        }): Promise<Vote> => {
            const response = await apiClient.patch<ApiResponse<Vote>>(
                `/votes/${voteId}`,
                data
            );
            return response.data.data;
        },
        onSuccess: (updatedVote) => {
            // Update vote cache
            queryClient.setQueryData(
                queryKeys.vote(gameId, updatedVote.player_id.toString()),
                updatedVote
            );

            // Invalidate votes list
            queryClient.invalidateQueries({
                queryKey: queryKeys.votes(gameId),
            });

            // Invalidate game
            queryClient.invalidateQueries({
                queryKey: queryKeys.game(gameId),
            });
        },
    });
}

export function useDeleteVote(gameId: string) {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async ({
            playerId,
        }: {
            playerId: string;
        }): Promise<void> => {
            await apiClient.delete(`/games/${gameId}/votes/${playerId}`);
        },
        onSuccess: () => {
            // Invalidate votes list
            queryClient.invalidateQueries({
                queryKey: queryKeys.votes(gameId),
            });

            // Invalidate game
            queryClient.invalidateQueries({
                queryKey: queryKeys.game(gameId),
            });
        },
    });
}

export function useRevealVotes(gameId: string) {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async (): Promise<{ votes: Vote[]; revealed: boolean }> => {
            const response = await apiClient.post<
                ApiResponse<{ votes: Vote[]; revealed: boolean }>
            >(`/games/${gameId}/reveal`);
            return response.data.data;
        },
        onSuccess: () => {
            // Invalidate votes to show revealed state
            queryClient.invalidateQueries({
                queryKey: queryKeys.votes(gameId),
            });

            // Invalidate game to update status
            queryClient.invalidateQueries({
                queryKey: queryKeys.game(gameId),
            });
        },
    });
}

export function useResetVotes(gameId: string) {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async (): Promise<void> => {
            await apiClient.delete(`/games/${gameId}/votes`);
        },
        onSuccess: () => {
            // Clear votes cache
            queryClient.invalidateQueries({
                queryKey: queryKeys.votes(gameId),
            });

            // Update game
            queryClient.invalidateQueries({
                queryKey: queryKeys.game(gameId),
            });
        },
    });
}
