import { useQuery } from "@tanstack/react-query";
import { apiClient } from "../lib/axios";
import { queryKeys } from "./keys";
import type { PointValue, ApiResponse } from "../types/api";

// Point Values Queries
export function usePointValues() {
    return useQuery({
        queryKey: queryKeys.pointValues,
        queryFn: async (): Promise<PointValue[]> => {
            const response = await apiClient.get<ApiResponse<PointValue[]>>(
                "/point-values"
            );
            return response.data.data;
        },
        staleTime: 1000 * 60 * 15, // 15 minutes - point values don't change often
        gcTime: 1000 * 60 * 30, // 30 minutes cache time
    });
}

export function useActivePointValues() {
    return useQuery({
        queryKey: [...queryKeys.pointValues, "active"],
        queryFn: async (): Promise<PointValue[]> => {
            const response = await apiClient.get<ApiResponse<PointValue[]>>(
                "/point-values?active=true"
            );
            return response.data.data;
        },
        staleTime: 1000 * 60 * 15, // 15 minutes
        gcTime: 1000 * 60 * 30, // 30 minutes cache time
    });
}
