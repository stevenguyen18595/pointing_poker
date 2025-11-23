import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { apiClient } from "../lib/axios";
import { queryKeys } from "./keys";
import type { Story, CreateStoryRequest, ApiResponse } from "../types/api";

// Story Queries
export function useStories(gameId: string) {
    return useQuery({
        queryKey: queryKeys.stories(gameId),
        queryFn: async (): Promise<Story[]> => {
            const response = await apiClient.get<ApiResponse<Story[]>>(
                `/games/${gameId}/stories`
            );
            return response.data.data;
        },
        enabled: !!gameId,
    });
}

export function useStory(storyId: string) {
    return useQuery({
        queryKey: queryKeys.story(storyId),
        queryFn: async (): Promise<Story> => {
            const response = await apiClient.get<ApiResponse<Story>>(
                `/stories/${storyId}`
            );
            return response.data.data;
        },
        enabled: !!storyId,
    });
}

// Story Mutations
export function useCreateStory(gameId: string) {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async (data: CreateStoryRequest): Promise<Story> => {
            const response = await apiClient.post<ApiResponse<Story>>(
                `/games/${gameId}/stories`,
                data
            );
            return response.data.data;
        },
        onSuccess: (newStory) => {
            // Add story to cache
            queryClient.setQueryData(queryKeys.story(newStory.id), newStory);

            // Invalidate stories list
            queryClient.invalidateQueries({
                queryKey: queryKeys.stories(gameId),
            });
        },
    });
}

export function useUpdateStory(gameId: string, storyId: string) {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async (data: Partial<Story>): Promise<Story> => {
            const response = await apiClient.patch<ApiResponse<Story>>(
                `/stories/${storyId}`,
                data
            );
            return response.data.data;
        },
        onSuccess: (updatedStory) => {
            // Update story cache
            queryClient.setQueryData(queryKeys.story(storyId), updatedStory);

            // Invalidate stories list
            queryClient.invalidateQueries({
                queryKey: queryKeys.stories(gameId),
            });
        },
    });
}

export function useDeleteStory(gameId: string) {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async (storyId: string): Promise<void> => {
            await apiClient.delete(`/stories/${storyId}`);
        },
        onSuccess: () => {
            // Invalidate stories list
            queryClient.invalidateQueries({
                queryKey: queryKeys.stories(gameId),
            });
        },
    });
}

export function useSetCurrentStory(gameId: string) {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async (storyId: string): Promise<Story> => {
            const response = await apiClient.post<ApiResponse<Story>>(
                `/stories/${storyId}/set-current`
            );
            return response.data.data;
        },
        onSuccess: (updatedStory) => {
            // Update story cache
            queryClient.setQueryData(
                queryKeys.story(updatedStory.id),
                updatedStory
            );

            // Invalidate stories list to update is_current flags
            queryClient.invalidateQueries({
                queryKey: queryKeys.stories(gameId),
            });
        },
    });
}

export function useCompleteStory(gameId: string) {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: async ({
            storyId,
            estimatedPoints,
        }: {
            storyId: string;
            estimatedPoints: string;
        }): Promise<Story> => {
            const response = await apiClient.post<ApiResponse<Story>>(
                `/stories/${storyId}/complete`,
                {
                    estimated_points: estimatedPoints,
                }
            );
            return response.data.data;
        },
        onSuccess: (updatedStory) => {
            // Update story cache
            queryClient.setQueryData(
                queryKeys.story(updatedStory.id),
                updatedStory
            );

            // Invalidate stories list
            queryClient.invalidateQueries({
                queryKey: queryKeys.stories(gameId),
            });
        },
    });
}
