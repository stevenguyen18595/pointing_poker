import axios, { AxiosError, AxiosResponse } from "axios";

// API Configuration - use environment variable or default to /api
const API_BASE_URL = (window as any)?.ENV?.VITE_API_URL || "/api";

// Create axios instance with base configuration
export const apiClient = axios.create({
    baseURL: API_BASE_URL,
    headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
    },
    withCredentials: true, // Include cookies for session-based auth
    timeout: 10000, // 10 second timeout
});

// Request interceptor to add CSRF token and auth
apiClient.interceptors.request.use(
    (config) => {
        // Add CSRF token for non-GET requests
        if (
            config.method &&
            !["get", "head", "options"].includes(config.method.toLowerCase())
        ) {
            const token = document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content");
            if (token) {
                config.headers["X-CSRF-TOKEN"] = token;
            }
        }

        // Add any auth tokens if needed
        const authToken = localStorage.getItem("auth_token");
        if (authToken) {
            config.headers.Authorization = `Bearer ${authToken}`;
        }

        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Response interceptor for centralized error handling
apiClient.interceptors.response.use(
    (response: AxiosResponse) => {
        return response;
    },
    (error: AxiosError) => {
        // Handle different types of errors
        if (error.response) {
            // Server responded with error status
            const status = error.response.status;
            const data = error.response.data as any;

            switch (status) {
                case 401:
                    // Unauthorized - redirect to login or show auth error
                    console.error("Unauthorized access");
                    break;
                case 403:
                    // Forbidden
                    console.error("Access forbidden");
                    break;
                case 404:
                    // Not found
                    console.error("Resource not found");
                    break;
                case 422:
                    // Validation errors
                    console.error(
                        "Validation error:",
                        data.errors || data.message
                    );
                    break;
                case 419:
                    // CSRF token mismatch
                    console.error(
                        "CSRF token mismatch - please refresh the page"
                    );
                    break;
                case 500:
                    // Server error
                    console.error("Server error");
                    break;
                default:
                    console.error(
                        "HTTP error:",
                        status,
                        data.message || error.message
                    );
            }

            // Create a more informative error object
            const enhancedError = new Error(
                data.message || `HTTP ${status}: ${error.response.statusText}`
            );
            (enhancedError as any).status = status;
            (enhancedError as any).data = data;
            return Promise.reject(enhancedError);
        } else if (error.request) {
            // Network error
            const networkError = new Error(
                "Network error - please check your connection"
            );
            (networkError as any).status = 0;
            return Promise.reject(networkError);
        } else {
            // Other error
            return Promise.reject(error);
        }
    }
);

// Helper function to extract error messages from API responses
export function getErrorMessage(error: AxiosError): string {
    if (error.response?.data) {
        const data = error.response.data as any;

        // Handle Laravel API responses with message field
        if (data.message) {
            return data.message;
        }

        // Handle Laravel validation errors
        if (data.errors) {
            const errors = Object.values(data.errors).flat() as string[];
            return errors.length > 0 ? errors.join(", ") : "Validation failed";
        }

        // Handle simple error strings
        if (typeof data === "string") {
            return data;
        }
    }

    // Fallback to axios error message
    return error.message || "An unexpected error occurred";
}

// Helper function to unwrap Laravel API responses
export function unwrapApiResponse<T>(response: AxiosResponse): T {
    // Laravel API responses typically have data nested under 'data' key
    if (
        response.data &&
        typeof response.data === "object" &&
        "data" in response.data
    ) {
        return response.data.data;
    }
    // Fallback to direct response data
    return response.data;
}

// API health check function for connection testing
export async function checkApiHealth(): Promise<boolean> {
    try {
        await apiClient.get("/game-statuses");
        return true;
    } catch (error) {
        console.warn(
            "API health check failed:",
            getErrorMessage(error as AxiosError)
        );
        return false;
    }
}

export default apiClient;
