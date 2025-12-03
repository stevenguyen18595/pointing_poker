import React, { useState } from "react";
import Card from "./Card";
import { useGame } from "../queries/games";
import { usePlayers } from "../queries/players";
import { usePointValues } from "../queries/pointValues";
import {
    useVotes,
    useSubmitVote,
    useRevealVotes,
    useResetVotes,
} from "../queries/votes";
import type { Player, PointValue, Vote } from "../types/api";

type CardValue = number | string;

interface User {
    id: string;
    name: string;
    is_moderator: boolean;
}

interface PokerGameProps {
    gameId: string;
    user: User;
}

const PokerGame: React.FC<PokerGameProps> = ({ gameId, user }) => {
    const [selectedCard, setSelectedCard] = useState<CardValue | null>(null);

    // Fetch game data
    const { data: gameData, isLoading: gameLoading } = useGame(gameId);

    // Check if votes are revealed based on game status
    const showResults = gameData?.status?.name === "revealed";
    const { data: players = [], isLoading: playersLoading } =
        usePlayers(gameId);
    const { data: pointValues = [], isLoading: pointValuesLoading } =
        usePointValues();

    // Fetch votes for current game
    const { data: votes = [] as Vote[], refetch: refetchVotes } =
        useVotes(gameId);

    // Mutations
    const submitVoteMutation = useSubmitVote(gameId);
    const revealVotesMutation = useRevealVotes(gameId);
    const resetVotesMutation = useResetVotes(gameId);

    // Check if current user has voted
    const userVote = votes.find(
        (v: Vote) => v.player_id.toString() === user.id
    );
    const hasVoted = !!userVote;

    // Loading state
    const isLoading = gameLoading || playersLoading || pointValuesLoading;

    if (isLoading) {
        return (
            <div className="flex items-center justify-center h-64">
                <div className="text-lg">Loading game data...</div>
            </div>
        );
    }

    const handleCardSelect = (value: CardValue): void => {
        if (hasVoted && !showResults) return;
        setSelectedCard(value);
    };

    const submitVote = async (): Promise<void> => {
        if (selectedCard !== null && !hasVoted) {
            try {
                const pointValue = pointValues.find(
                    (pv: PointValue) => pv.value === selectedCard.toString()
                );
                if (!pointValue) return;

                await submitVoteMutation.mutateAsync({
                    game_id: parseInt(gameId),
                    point_value_id: pointValue.id,
                    player_id: parseInt(user.id),
                });
                refetchVotes();
            } catch (error) {
                console.error("Failed to submit vote:", error);
            }
        }
    };

    const revealResults = async (): Promise<void> => {
        try {
            await revealVotesMutation.mutateAsync();
            refetchVotes();
        } catch (error) {
            console.error("Failed to reveal results:", error);
        }
    };

    const startNewVoting = (): void => {
        setSelectedCard(null);
        resetVotesMutation.mutateAsync();
    };

    const totalVotes = votes.length;
    const totalPlayers = players.length;
    const votingProgress =
        totalPlayers > 0 ? Math.round((totalVotes / totalPlayers) * 100) : 0;
    const activePointValues = pointValues.filter(
        (pv: PointValue) => pv.is_active
    );

    return (
        <div className="max-w-6xl mx-auto p-6">
            {/* Game Header */}
            <div className="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div className="text-center">
                    <h1 className="text-2xl font-bold text-gray-800 mb-2">
                        {gameData?.name || "Agile AF Game"}
                    </h1>
                    <div className="text-sm text-gray-600">
                        Game Code:{" "}
                        <span className="font-mono font-bold">
                            {gameData?.game_code}
                        </span>
                    </div>
                </div>
            </div>

            {/* Voting Progress */}
            <div className="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div className="flex justify-between items-center mb-4">
                    <h3 className="text-lg font-semibold text-gray-800">
                        Voting Progress
                    </h3>
                    <span className="text-sm text-gray-600">
                        {totalVotes} of {totalPlayers} votes
                    </span>
                </div>
                <div className="w-full bg-gray-200 rounded-full h-3">
                    <div
                        className="bg-blue-500 h-3 rounded-full transition-all duration-500"
                        style={{ width: `${votingProgress}%` }}
                    ></div>
                </div>

                <div className="mt-4 flex flex-wrap gap-2">
                    {players.map((player: Player) => {
                        const playerVote = votes.find(
                            (v: Vote) => v.player_id === player.id
                        );
                        const hasPlayerVoted = !!playerVote;

                        return (
                            <div
                                key={player.id}
                                className={`px-3 py-1 rounded-full text-sm ${
                                    hasPlayerVoted
                                        ? "bg-green-100 text-green-800"
                                        : "bg-gray-100 text-gray-600"
                                }`}
                            >
                                {player.name}
                                {hasPlayerVoted && (
                                    <span className="ml-1">✓</span>
                                )}
                            </div>
                        );
                    })}
                </div>
            </div>

            {/* Voting Cards */}
            <div className="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h3 className="text-lg font-semibold text-gray-800 mb-4">
                    Select Your Estimate
                </h3>
                <div className="grid grid-cols-6 md:grid-cols-11 gap-3">
                    {activePointValues.map((pointValue: PointValue) => (
                        <Card
                            key={pointValue.id}
                            value={pointValue.value}
                            isSelected={
                                hasVoted
                                    ? userVote?.point_value?.value ===
                                      pointValue.value
                                    : selectedCard === pointValue.value
                            }
                            onClick={() => handleCardSelect(pointValue.value)}
                            disabled={hasVoted && !showResults}
                            className={pointValue.color_class as string}
                        />
                    ))}
                </div>

                <div className="mt-6 text-center">
                    {!hasVoted && selectedCard !== null && (
                        <button
                            onClick={submitVote}
                            disabled={submitVoteMutation.isPending}
                            className="bg-blue-500 hover:bg-blue-600 disabled:bg-blue-300 text-white font-semibold py-2 px-6 rounded-lg transition-colors"
                        >
                            {submitVoteMutation.isPending
                                ? "Submitting..."
                                : "Submit Vote"}
                        </button>
                    )}

                    {hasVoted && !showResults && (
                        <div className="text-center">
                            <p className="text-green-600 font-semibold mb-4">
                                ✓ You voted: {userVote?.point_value?.value}
                            </p>
                            <p className="text-gray-600">
                                Waiting for other team members...
                            </p>
                        </div>
                    )}
                </div>
            </div>

            {/* Moderator Controls */}
            {user.is_moderator && totalVotes > 0 && (
                <div className="bg-white rounded-lg shadow-lg p-6 mb-6">
                    <h3 className="text-lg font-semibold text-gray-800 mb-4">
                        Moderator Controls
                    </h3>
                    <div className="flex gap-4 justify-center">
                        {!showResults && (
                            <button
                                onClick={revealResults}
                                disabled={revealVotesMutation.isPending}
                                className="bg-green-500 hover:bg-green-600 disabled:bg-green-300 text-white font-semibold py-2 px-6 rounded-lg transition-colors"
                            >
                                {revealVotesMutation.isPending
                                    ? "Revealing..."
                                    : "Reveal Votes"}
                            </button>
                        )}
                        {showResults && (
                            <button
                                onClick={startNewVoting}
                                className="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg transition-colors"
                            >
                                Start New Voting
                            </button>
                        )}
                    </div>
                </div>
            )}

            {/* Results */}
            {showResults && (
                <div className="bg-white rounded-lg shadow-lg p-6">
                    <h3 className="text-lg font-semibold text-gray-800 mb-4">
                        Voting Results
                    </h3>
                    <div className="space-y-3">
                        {votes.map((vote: Vote, index: number) => {
                            const player = players.find(
                                (p: Player) => p.id === vote.player_id
                            );
                            return (
                                <div
                                    key={index}
                                    className="flex justify-between items-center p-3 bg-gray-50 rounded-lg"
                                >
                                    <span className="font-medium text-gray-800">
                                        {player?.name || "Unknown Player"}
                                    </span>
                                    <span className="text-lg font-bold text-blue-600">
                                        {vote.point_value?.value || "Unknown"}
                                    </span>
                                </div>
                            );
                        })}
                    </div>

                    <div className="mt-6 p-4 bg-blue-50 rounded-lg">
                        <h4 className="font-semibold text-blue-800 mb-2">
                            Statistics
                        </h4>
                        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div className="text-center">
                                <div className="text-lg font-bold text-blue-600">
                                    {totalVotes}
                                </div>
                                <div className="text-blue-700">Total Votes</div>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default PokerGame;
