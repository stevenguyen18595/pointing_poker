import { useGameStatuses } from "../queries/games";
import { usePointValues } from "../queries/pointValues";

export default function ApiTestComponent() {
    const {
        data: gameStatuses,
        isLoading: statusLoading,
        error: statusError,
    } = useGameStatuses();
    const {
        data: pointValues,
        isLoading: pointsLoading,
        error: pointsError,
    } = usePointValues();

    return (
        <div className="p-6 space-y-6">
            <h1 className="text-2xl font-bold">
                Planning Poker - API Integration Test
            </h1>

            <div className="bg-white p-4 rounded shadow">
                <h2 className="text-lg font-semibold mb-2">Game Statuses</h2>
                {statusLoading && <p>Loading game statuses...</p>}
                {statusError && (
                    <p className="text-red-600">
                        Error: {(statusError as Error).message}
                    </p>
                )}
                {gameStatuses && (
                    <div className="space-y-2">
                        {gameStatuses.map((status) => (
                            <div
                                key={status.id}
                                className="flex items-center space-x-2"
                            >
                                <span
                                    className={`px-2 py-1 rounded text-xs ${
                                        status.color_class || "bg-gray-200"
                                    }`}
                                >
                                    {status.label}
                                </span>
                                <span className="text-sm text-gray-600">
                                    {status.name}
                                </span>
                            </div>
                        ))}
                    </div>
                )}
            </div>

            <div className="bg-white p-4 rounded shadow">
                <h2 className="text-lg font-semibold mb-2">Point Values</h2>
                {pointsLoading && <p>Loading point values...</p>}
                {pointsError && (
                    <p className="text-red-600">
                        Error: {(pointsError as Error).message}
                    </p>
                )}
                {pointValues && (
                    <div className="grid grid-cols-6 gap-2">
                        {pointValues.map((point) => (
                            <div
                                key={point.id}
                                className={`p-2 text-center rounded border ${
                                    point.color_class || "bg-gray-100"
                                }`}
                            >
                                <div className="font-semibold">
                                    {point.value}
                                </div>
                                <div className="text-xs text-gray-500">
                                    {point.card_type}
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>

            <div className="bg-blue-50 p-4 rounded">
                <h3 className="font-medium mb-2">🎯 Next Steps</h3>
                <ul className="text-sm space-y-1 text-gray-700">
                    <li>✅ Backend API endpoints working</li>
                    <li>✅ React Query integration complete</li>
                    <li>✅ TypeScript types configured</li>
                    <li>🔄 Create game management interface</li>
                    <li>🔄 Add player join functionality</li>
                    <li>🔄 Build voting interface</li>
                </ul>
            </div>
        </div>
    );
}
