<?php

namespace App\Services;

use App\Exceptions\VotingException;
use App\Models\Photo;
use App\Models\Vote;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VotingService
{
    /**
     * Vote for a photo.
     */
    public function vote(Photo $photo): Vote
    {
        $user = Auth::user();

        return DB::transaction(function () use ($user, $photo) {
            // Validate business rules
            $this->validateVote($photo, $user->id);

            // Create vote
            $vote = Vote::create([
                'user_id' => $user->id,
                'photo_id' => $photo->id,
            ]);
            
            return $vote;
        });
    }

    /**
     * Remove vote from a photo.
     */
    public function unvote(Photo $photo): bool
    {
        $user = Auth::user();

        return DB::transaction(function () use ($user, $photo) {
            $vote = Vote::where('user_id', $user->id)
                ->where('photo_id', $photo->id)
                ->first();

            if (! $vote) {
                throw VotingException::notVoted();
            }

            return $vote->delete();
        });
    }

    /**
     * Get user's votes.
     */
    public function getUserVotes(int $userId)
    {
        return Vote::where('user_id', $userId)
            ->with('photo')
            ->get();
    }

    /**
     * Get user's vote count.
     */
    public function getUserVoteCount(int $userId): int
    {
        return Vote::where('user_id', $userId)->count();
    }

    /**
     * Check if user has voted for a photo.
     */
    public function hasUserVoted(int $userId, int $photoId): bool
    {
        return Vote::where('user_id', $userId)
            ->where('photo_id', $photoId)
            ->exists();
    }

    /**
     * Validate vote business rules.
     */
    private function validateVote(Photo $photo, int $userId): void
    {
        // Check if project is active
        if (!$photo->project->is_active) {
            throw VotingException::inactiveProject();
        }

        // Check if user already voted for this photo
        if ($this->hasUserVoted($userId, $photo->id)) {
            throw VotingException::alreadyVoted();
        }

        // Check if user has reached the maximum number of votes (10)
        if ($this->getUserVoteCount($userId) >= 10) {
            throw VotingException::voteLimit();
        }
    }
}
