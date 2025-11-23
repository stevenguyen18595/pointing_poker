import React, { useState } from "react";

interface JoinGameFormProps {
    onJoin: (gameId: string, userName: string) => void;
    onBack: () => void;
}

const JoinGameForm: React.FC<JoinGameFormProps> = ({ onJoin, onBack }) => {
    const [gameId, setGameId] = useState<string>("");
    const [userName, setUserName] = useState<string>("");
    const [error, setError] = useState<string>("");

    const handleSubmit = (e: React.FormEvent): void => {
        e.preventDefault();
        setError("");

        if (!gameId.trim()) {
            setError("Please enter a game ID");
            return;
        }

        if (!userName.trim()) {
            setError("Please enter your name");
            return;
        }

        onJoin(gameId.trim(), userName.trim());
    };

    return (
        <div className="min-h-screen bg-gray-100 flex items-center justify-center">
            <div className="max-w-md w-full bg-white rounded-lg shadow-md p-8">
                <div className="mb-6">
                    <button
                        onClick={onBack}
                        className="text-blue-500 hover:text-blue-600 underline"
                    >
                        ← Back to Home
                    </button>
                </div>

                <h1 className="text-3xl font-bold text-center text-gray-800 mb-8">
                    Join Planning Poker
                </h1>

                <form onSubmit={handleSubmit} className="space-y-4">
                    <div>
                        <label
                            htmlFor="gameId"
                            className="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Game ID
                        </label>
                        <input
                            type="text"
                            id="gameId"
                            value={gameId}
                            onChange={(e) => setGameId(e.target.value)}
                            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Enter game ID"
                        />
                    </div>

                    <div>
                        <label
                            htmlFor="userName"
                            className="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Your Name
                        </label>
                        <input
                            type="text"
                            id="userName"
                            value={userName}
                            onChange={(e) => setUserName(e.target.value)}
                            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Enter your name"
                        />
                    </div>

                    {error && (
                        <div className="text-red-600 text-sm bg-red-50 p-2 rounded">
                            {error}
                        </div>
                    )}

                    <button
                        type="submit"
                        className="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-4 rounded-lg transition-colors"
                    >
                        Join Game
                    </button>
                </form>
            </div>
        </div>
    );
};

export default JoinGameForm;
