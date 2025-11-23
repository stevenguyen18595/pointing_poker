import React, { useState } from "react";
import Card from "./Card";

type CardValue = number | string;

interface User {
    id: string;
    name: string;
}

interface Story {
    id: string;
    title: string;
    description: string;
}

interface Vote {
    userId: string;
    userName: string;
    value: CardValue;
}

interface PokerGameProps {
    gameId: string;
    user: User;
}

const PokerGame: React.FC<PokerGameProps> = ({ gameId, user }) => {
    const [currentStory, setCurrentStory] = useState<Story>({
        id: "1",
        title: "User Authentication System",
        description:
            "Implement login, logout, and user registration functionality with email verification.",
    });

    const [selectedCard, setSelectedCard] = useState<CardValue | null>(null);
    const [hasVoted, setHasVoted] = useState<boolean>(false);
    const [showResults, setShowResults] = useState<boolean>(false);
    const [votes, setVotes] = useState<Vote[]>([]);
    const [teamMembers] = useState<User[]>([
        { id: user.id, name: user.name },
        { id: "2", name: "Alice Johnson" },
        { id: "3", name: "Bob Smith" },
        { id: "4", name: "Carol Davis" },
    ]);

    const cardValues: CardValue[] = [0, 1, 2, 3, 5, 8, 13, 21, 34, "?", "☕"];

    const handleCardSelect = (value: CardValue): void => {
        if (hasVoted && !showResults) return;
        setSelectedCard(value);
    };

    const submitVote = (): void => {
        if (selectedCard !== null && !hasVoted) {
            const newVote: Vote = {
                userId: user.id,
                userName: user.name,
                value: selectedCard,
            };

            setVotes((prev) => [
                ...prev.filter((v) => v.userId !== user.id),
                newVote,
            ]);
            setHasVoted(true);

            // Simulate other team members voting
            if (votes.length === 0) {
                setTimeout(() => {
                    const otherVotes: Vote[] = [
                        { userId: "2", userName: "Alice Johnson", value: 5 },
                        { userId: "3", userName: "Bob Smith", value: 8 },
                        { userId: "4", userName: "Carol Davis", value: 5 },
                    ];
                    setVotes((prev) => [...prev, ...otherVotes]);
                }, 1500);
            }
        }
    };

    const revealResults = (): void => {
        setShowResults(true);
    };

    const startNewVoting = (): void => {
        setSelectedCard(null);
        setHasVoted(false);
        setShowResults(false);
        setVotes([]);

        // Simulate new story
        const stories = [
            {
                id: "2",
                title: "Shopping Cart Feature",
                description:
                    "Add items to cart, modify quantities, and proceed to checkout.",
            },
            {
                id: "3",
                title: "Payment Integration",
                description:
                    "Integrate with Stripe for secure payment processing.",
            },
            {
                id: "4",
                title: "Order History",
                description:
                    "Allow users to view their past orders and reorder items.",
            },
        ];

        const randomStory = stories[Math.floor(Math.random() * stories.length)];
        setCurrentStory(randomStory);
    };

    const getVoteStats = () => {
        if (votes.length === 0) return null;

        const numericVotes = votes
            .map((v) => v.value)
            .filter((v) => typeof v === "number") as number[];

        if (numericVotes.length === 0) return null;

        const avg =
            numericVotes.reduce((a, b) => a + b, 0) / numericVotes.length;
        const min = Math.min(...numericVotes);
        const max = Math.max(...numericVotes);

        return { avg: avg.toFixed(1), min, max };
    };

    const allMembersVoted = votes.length === teamMembers.length;

    return (
        <div className="max-w-6xl mx-auto p-6">
            {/* Game Info */}
            <div className="text-center mb-6">
                <h1 className="text-2xl font-bold text-gray-800">
                    Planning Poker Session
                </h1>
                <p className="text-gray-600">Game ID: {gameId}</p>
            </div>

            {/* Current Story Section */}
            <div className="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 className="text-2xl font-bold text-gray-800 mb-4">
                    Current Story
                </h2>
                <div className="bg-blue-50 p-4 rounded-lg">
                    <h3 className="text-lg font-semibold text-blue-800 mb-2">
                        {currentStory.title}
                    </h3>
                    <p className="text-blue-700">{currentStory.description}</p>
                </div>
            </div>

            {/* Team Members & Voting Status */}
            <div className="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h3 className="text-xl font-semibold text-gray-800 mb-4">
                    Team Members ({votes.length}/{teamMembers.length} voted)
                </h3>
                <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                    {teamMembers.map((member) => {
                        const memberVote = votes.find(
                            (v) => v.userId === member.id
                        );
                        return (
                            <div
                                key={member.id}
                                className={`p-3 rounded-lg text-center ${
                                    memberVote
                                        ? "bg-green-100 border-2 border-green-300"
                                        : "bg-gray-100 border-2 border-gray-300"
                                }`}
                            >
                                <div className="font-semibold text-gray-800">
                                    {member.name}
                                </div>
                                <div className="text-sm mt-1">
                                    {memberVote ? (
                                        showResults ? (
                                            <span className="font-bold text-green-600">
                                                {memberVote.value}
                                            </span>
                                        ) : (
                                            <span className="text-green-600">
                                                ✓ Voted
                                            </span>
                                        )
                                    ) : (
                                        <span className="text-gray-500">
                                            Waiting...
                                        </span>
                                    )}
                                </div>
                            </div>
                        );
                    })}
                </div>
            </div>

            {/* Voting Cards */}
            {!showResults && (
                <div className="bg-white rounded-lg shadow-lg p-6 mb-6">
                    <h3 className="text-xl font-semibold text-gray-800 mb-4 text-center">
                        {hasVoted
                            ? "Waiting for other team members..."
                            : "Choose your estimate"}
                    </h3>

                    <div className="grid grid-cols-5 md:grid-cols-11 gap-3 justify-items-center mb-6">
                        {cardValues.map((value) => (
                            <Card
                                key={value}
                                value={value}
                                isSelected={selectedCard === value}
                                onClick={() => handleCardSelect(value)}
                            />
                        ))}
                    </div>

                    <div className="text-center">
                        {selectedCard !== null && !hasVoted && (
                            <button
                                onClick={submitVote}
                                className="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg transition-colors mr-4"
                            >
                                Submit Vote: {selectedCard}
                            </button>
                        )}

                        {allMembersVoted && (
                            <button
                                onClick={revealResults}
                                className="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-6 rounded-lg transition-colors animate-pulse"
                            >
                                Reveal Results
                            </button>
                        )}
                    </div>
                </div>
            )}

            {/* Results Section */}
            {showResults && (
                <div className="bg-white rounded-lg shadow-lg p-6">
                    <h3 className="text-xl font-semibold text-gray-800 mb-4 text-center">
                        Voting Results
                    </h3>

                    <div className="mb-6">
                        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                            {votes.map((vote) => (
                                <div
                                    key={vote.userId}
                                    className="bg-gray-50 p-3 rounded-lg text-center"
                                >
                                    <div className="font-semibold text-gray-800">
                                        {vote.userName}
                                    </div>
                                    <div className="text-2xl font-bold text-blue-600 mt-1">
                                        {vote.value}
                                    </div>
                                </div>
                            ))}
                        </div>

                        {getVoteStats() && (
                            <div className="bg-blue-50 p-4 rounded-lg">
                                <h4 className="font-semibold text-blue-800 mb-2">
                                    Statistics
                                </h4>
                                <div className="grid grid-cols-3 gap-4 text-center">
                                    <div>
                                        <div className="text-sm text-blue-600">
                                            Average
                                        </div>
                                        <div className="text-lg font-bold text-blue-800">
                                            {getVoteStats()?.avg}
                                        </div>
                                    </div>
                                    <div>
                                        <div className="text-sm text-blue-600">
                                            Min
                                        </div>
                                        <div className="text-lg font-bold text-blue-800">
                                            {getVoteStats()?.min}
                                        </div>
                                    </div>
                                    <div>
                                        <div className="text-sm text-blue-600">
                                            Max
                                        </div>
                                        <div className="text-lg font-bold text-blue-800">
                                            {getVoteStats()?.max}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>

                    <div className="text-center">
                        <button
                            onClick={startNewVoting}
                            className="bg-purple-500 hover:bg-purple-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors"
                        >
                            Start New Story
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
};

export default PokerGame;
