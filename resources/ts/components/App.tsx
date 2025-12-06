import React, { useState } from "react";
import { QueryClientProvider } from "@tanstack/react-query";
import { ReactQueryDevtools } from "@tanstack/react-query-devtools";
import { queryClient } from "../lib/queryClient";
import PokerGame from "./PokerGame";
import { JoinGameForm } from "./JoinGameForm";
import { useCreateGame, useJoinGame } from "../queries/games";

type ViewType = "home" | "join" | "game" | "create";

interface User {
    id: string;
    name: string;
    is_moderator: boolean;
}

interface Game {
    id: string;
    gameCode: string;
    name: string;
}

const AppContent: React.FC = () => {
    const [currentView, setCurrentView] = useState<ViewType>("home");
    const [gameData, setGameData] = useState<Game | null>(null);
    const [user, setUser] = useState<User | null>(null);

    const createGameMutation = useCreateGame();
    const joinGameMutation = useJoinGame();

    //the person who creates the game is not one of the players.
    const handleCreateGame = async (): Promise<void> => {
        const userName = prompt("Enter your name:");
        const gameName = prompt("Enter game name:");
        if (!userName || !gameName) return;

        try {
            const { game, player } = await createGameMutation.mutateAsync({
                name: gameName,
                description: `Agile AF session for ${gameName}`,
                creator_name: userName,
            });

            // Use the player returned from backend
            if (player) {
                const newUser: User = {
                    id: player.id.toString(),
                    name: player.name,
                    is_moderator: player.is_moderator,
                };
                setUser(newUser);
            }

            setGameData({
                id: game.id.toString(),
                gameCode: game.game_code,
                name: game.name,
            });
            setCurrentView("game");
        } catch (error) {
            alert("Failed to create game. Please try again.");
        }
    };

    const showJoinForm = (): void => {
        setCurrentView("join");
    };

    const handleJoinGame = async (
        gameCode: string,
        userName: string,
        isModerator: boolean
    ): Promise<void> => {
        try {
            const response = await joinGameMutation.mutateAsync({
                game_code: gameCode,
                player_name: userName,
                is_moderator: isModerator,
            });

            const newUser: User = {
                id: response.player.id.toString(),
                name: response.player.name,
                is_moderator: response.player.is_moderator,
            };

            setUser(newUser);
            setGameData({
                id: response.game.id.toString(),
                gameCode: response.game.game_code,
                name: response.game.name,
            });
            setCurrentView("game");
        } catch (error) {
            alert("Failed to join game. Please check the game code.");
        }
    };

    const backToHome = (): void => {
        setCurrentView("home");
        setGameData(null);
        setUser(null);
    };

    if (currentView === "join") {
        return <JoinGameForm onJoin={handleJoinGame} onBack={backToHome} />;
    }

    if (currentView === "game" && gameData && user) {
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
                        <p className="text-gray-600 mt-2">
                            Game: {gameData.name}
                        </p>
                        <p className="text-gray-600">
                            Code: {gameData.gameCode}
                        </p>
                        <p className="text-gray-600">Welcome, {user.name}!</p>
                    </div>
                    <PokerGame gameId={gameData.id} user={user} />
                </div>
            </div>
        );
    }

    return (
        <div className="min-h-screen bg-gray-100 flex items-center justify-center">
            <div className="max-w-md w-full bg-white rounded-lg shadow-md p-8">
                <h1 className="text-3xl font-bold text-center text-gray-800 mb-8">
                    Agile AF
                </h1>
                <div className="space-y-4">
                    <button
                        onClick={handleCreateGame}
                        disabled={createGameMutation.isPending}
                        className="w-full bg-blue-500 hover:bg-blue-600 disabled:bg-blue-300 text-white font-semibold py-3 px-4 rounded-lg transition-colors"
                    >
                        {createGameMutation.isPending
                            ? "Creating..."
                            : "Create New Game"}
                    </button>
                    <button
                        onClick={showJoinForm}
                        className="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-4 rounded-lg transition-colors"
                    >
                        Join Game
                    </button>
                </div>
                <div className="mt-8 text-center text-sm text-gray-500">
                    <p>Welcome to Agile AF!</p>
                    <p>Estimate story points with your team</p>
                </div>
            </div>
        </div>
    );
};

const App: React.FC = () => {
    return (
        <QueryClientProvider client={queryClient}>
            <AppContent />
            {/* <ApiTestComponent /> */}
            <ReactQueryDevtools initialIsOpen={false} />
        </QueryClientProvider>
    );
};

export default App;
