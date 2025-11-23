import axios, { AxiosError, AxiosResponse } from "axios";

// Create axios instance with base configuration
export const apiClient = axios.create({
    baseURL: "/api",
    headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
    },
    withCredentials: true, // Include cookies for session-based auth
});

// Request interceptor to add CSRF token
apiClient.interceptors.request.use(
    (config) => {
        // Add CSRF token for non-GET requests
        if (config.method && config.method !== "get") {
            const token = document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content");
            if (token) {
                config.headers["X-CSRF-TOKEN"] = token;
            }
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

export default apiClient;
