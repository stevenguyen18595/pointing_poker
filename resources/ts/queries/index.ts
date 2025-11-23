// Export all query keys
export { queryKeys } from "./keys";

// Export game-related queries
export {
    useGameStatuses,
    useGame,
    useGames,
    useCreateGame,
    useJoinGame,
    useUpdateGameStatus,
} from "./games";

// Export player-related queries
export {
    usePlayers,
    usePlayer,
    useUpdatePlayer,
    useUpdatePlayerActivity,
    useRemovePlayer,
} from "./players";

// Export story-related queries
export {
    useStories,
    useStory,
    useCreateStory,
    useUpdateStory,
    useDeleteStory,
    useSetCurrentStory,
    useCompleteStory,
} from "./stories";

// Export vote-related queries
export {
    useVotes,
    useVote,
    useSubmitVote,
    useUpdateVote,
    useDeleteVote,
    useRevealVotes,
    useStartVoting,
    useResetVotes,
} from "./votes";

// Export point values queries
export { usePointValues, useActivePointValues } from "./pointValues";
