import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { apiClient } from "../lib/axios";
import { queryKeys } from "./keys";
import type { Vote, SubmitVoteRequest, ApiResponse } from "../types/api";

// Vote Queries
export function useVotes(storyId: string) {
    return useQuery({
        queryKey: queryKeys.votes(storyId),
        queryFn: async (): Promise<Vote[]> => {
            const response = await apiClient.get<ApiResponse<Vote[]>>(
                `/stories/${storyId}/votes`
            );
            return response.data.data;
        },
        enabled: !!storyId,
        refetchInterval: 2000, // Poll every 2 seconds during voting
        refetchIntervalInBackground: true,
    });
}

export function useVote(storyId: string, playerId: string) {
    return useQuery({
        queryKey: queryKeys.vote(storyId, playerId),
        queryFn: async (): Promise<Vote | null> => {
            try {
                const response = await apiClient.get<ApiResponse<Vote>>(
                    `/stories/${storyId}/votes/${playerId}`
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
        enabled: !!storyId && !!playerId,
    });
}

// Vote Mutations
export function useSubmitVote(storyId: string, gameId: string) {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async (data: SubmitVoteRequest): Promise<Vote> => {
            const response = await apiClient.post<ApiResponse<Vote>>(
                `/stories/${storyId}/votes`,
                data
            );
            return response.data.data;
        },
        onSuccess: (newVote) => {
            // Update vote cache
            queryClient.setQueryData(
                queryKeys.vote(storyId, newVote.player_id.toString()),
                newVote
            );

            // Invalidate votes list
            queryClient.invalidateQueries({
                queryKey: queryKeys.votes(storyId),
            });

            // Invalidate stories list to potentially update voting status
            queryClient.invalidateQueries({
                queryKey: queryKeys.stories(gameId),
            });
        },
    });
}

export function useUpdateVote(storyId: string, gameId: string) {
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
                queryKeys.vote(storyId, updatedVote.player_id.toString()),
                updatedVote
            );

            // Invalidate votes list
            queryClient.invalidateQueries({
                queryKey: queryKeys.votes(storyId),
            });

            // Invalidate stories list
            queryClient.invalidateQueries({
                queryKey: queryKeys.stories(gameId),
            });
        },
    });
}

export function useDeleteVote(storyId: string, gameId: string) {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async ({
            playerId,
        }: {
            playerId: string;
        }): Promise<void> => {
            await apiClient.delete(`/stories/${storyId}/votes/${playerId}`);
        },
        onSuccess: () => {
            // Invalidate votes list
            queryClient.invalidateQueries({
                queryKey: queryKeys.votes(storyId),
            });

            // Invalidate stories list
            queryClient.invalidateQueries({
                queryKey: queryKeys.stories(gameId),
            });
        },
    });
}

export function useRevealVotes(storyId: string, gameId: string) {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async (): Promise<{ votes: Vote[]; revealed: boolean }> => {
            const response = await apiClient.post<
                ApiResponse<{ votes: Vote[]; revealed: boolean }>
            >(`/stories/${storyId}/reveal`);
            return response.data.data;
        },
        onSuccess: () => {
            // Invalidate votes to show revealed state
            queryClient.invalidateQueries({
                queryKey: queryKeys.votes(storyId),
            });

            // Invalidate stories to update voting status
            queryClient.invalidateQueries({
                queryKey: queryKeys.stories(gameId),
            });
        },
    });
}

export function useStartVoting(storyId: string, gameId: string) {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async (): Promise<void> => {
            await apiClient.post(`/stories/${storyId}/start-voting`);
        },
        onSuccess: () => {
            // Clear existing votes
            queryClient.invalidateQueries({
                queryKey: queryKeys.votes(storyId),
            });

            // Update story status
            queryClient.invalidateQueries({
                queryKey: queryKeys.stories(gameId),
            });
        },
    });
}

export function useResetVotes(storyId: string, gameId: string) {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async (): Promise<void> => {
            await apiClient.delete(`/stories/${storyId}/votes`);
        },
        onSuccess: () => {
            // Clear votes cache
            queryClient.invalidateQueries({
                queryKey: queryKeys.votes(storyId),
            });

            // Update story status
            queryClient.invalidateQueries({
                queryKey: queryKeys.stories(gameId),
            });
        },
    });
}
