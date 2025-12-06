import React from "react";
import { useActivePointValues } from "../queries";

export const PointValueCards: React.FC = () => {
    const { data: pointValues, isLoading, error } = useActivePointValues();

    if (isLoading) {
        return (
            <div className="flex justify-center p-4">
                <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
            </div>
        );
    }

    if (error) {
        return (
            <div className="text-red-600 text-center p-4">
                Error loading point values: {error.message}
            </div>
        );
    }

    if (!pointValues || pointValues.length === 0) {
        return (
            <div className="text-gray-500 text-center p-4">
                No point values available
            </div>
        );
    }

    return (
        <div className="grid grid-cols-6 md:grid-cols-8 lg:grid-cols-10 gap-2 p-4">
            {pointValues
                .sort((a, b) => a.sort_order - b.sort_order)
                .map((pointValue) => (
                    <button
                        key={pointValue.id}
                        className={`
              aspect-[3/4] rounded-lg border-2 border-gray-300 
              flex items-center justify-center text-lg font-semibold
              hover:border-blue-400 hover:shadow-lg transition-all duration-200
              ${pointValue.color_class || "bg-white text-gray-800"}
            `}
                        title={pointValue.description || pointValue.label}
                    >
                        {pointValue.label}
                    </button>
                ))}
        </div>
    );
};
