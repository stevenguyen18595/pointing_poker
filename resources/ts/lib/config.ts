// Environment configuration for the frontend
declare global {
    interface Window {
        ENV?: {
            VITE_API_URL?: string;
            APP_URL?: string;
            APP_NAME?: string;
        };
    }
}

export const config = {
    api: {
        baseUrl: (window as any)?.ENV?.VITE_API_URL || "/api",
        timeout: 10000,
    },
    app: {
        name: (window as any)?.ENV?.APP_NAME || "Planning Poker",
        url: (window as any)?.ENV?.APP_URL || window.location.origin,
    },
    features: {
        realtime: false, // Enable when WebSocket support is added
        authentication: false, // Enable when auth is implemented
        persistence: true, // Local storage for session data
    },
};

export default config;
