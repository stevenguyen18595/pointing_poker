import React, { useState } from "react";
import PokerGame from "./PokerGame";
import JoinGameForm from "./JoinGameForm";

type ViewType = "home" | "join" | "game";

interface User {
    id: string;
    name: string;
}

const App: React.FC = () => {
    const [currentView, setCurrentView] = useState<ViewType>("home");
    const [gameId, setGameId] = useState<string | null>(null);
    const [user, setUser] = useState<User | null>(null);

    const createGame = (): void => {
        const userName = prompt("Enter your name:");
        if (!userName) return;

        const newUser: User = {
            id: Math.random().toString(36).substr(2, 9),
            name: userName,
        };

        const newGameId = Math.random().toString(36).substr(2, 9);
        setUser(newUser);
        setGameId(newGameId);
        setCurrentView("game");
    };

    const showJoinForm = (): void => {
        setCurrentView("join");
    };

    const handleJoinGame = (gameId: string, userName: string): void => {
        const newUser: User = {
            id: Math.random().toString(36).substr(2, 9),
            name: userName,
        };

        setUser(newUser);
        setGameId(gameId);
        setCurrentView("game");
    };

    const backToHome = (): void => {
        setCurrentView("home");
        setGameId(null);
        setUser(null);
    };

    if (currentView === "join") {
        return <JoinGameForm onJoin={handleJoinGame} onBack={backToHome} />;
    }

    if (currentView === "game" && gameId && user) {
        return (
            <div className="min-h-screen bg-gray-100 py-8">
                <div className="container mx-auto">
                    <div className="mb-4 text-center">
                        <button
                            onClick={backToHome}
                            className="text-blue-500 hover:text-blue-600 underline"
                        >
                            ← Back to Home
                        </button>
                        <p className="text-gray-600 mt-2">Game ID: {gameId}</p>
                        <p className="text-gray-600">Welcome, {user.name}!</p>
                    </div>
                    <PokerGame gameId={gameId} user={user} />
                </div>
            </div>
        );
    }

    return (
        <div className="min-h-screen bg-gray-100 flex items-center justify-center">
            <div className="max-w-md w-full bg-white rounded-lg shadow-md p-8">
                <h1 className="text-3xl font-bold text-center text-gray-800 mb-8">
                    Planning Poker
                </h1>
                <div className="space-y-4">
                    <button
                        onClick={createGame}
                        className="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-4 rounded-lg transition-colors"
                    >
                        Create New Game
                    </button>
                    <button
                        onClick={showJoinForm}
                        className="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-4 rounded-lg transition-colors"
                    >
                        Join Game
                    </button>
                </div>
                <div className="mt-8 text-center text-sm text-gray-500">
                    <p>Welcome to Planning Poker!</p>
                    <p>Estimate story points with your team</p>
                </div>
            </div>
        </div>
    );
};
export default App;
