<?php

namespace app\services;

use app\repositories\ElectionRepository;
use app\repositories\VoteRepository;

class ElectionService
{
    private ElectionRepository $electionRepository;
    private VoteRepository $voteRepository;

    public function __construct(ElectionRepository $electionRepository, VoteRepository $voteRepository)
    {
        $this->electionRepository = $electionRepository;
        $this->voteRepository = $voteRepository;
    }

    /**
     * Get all available elections
     * @return array<int, array{id:int, year:int}>
     */
    public function getAllElections(): array
    {
        return $this->electionRepository->getAll();
    }

    /**
     * Get election by ID
     * @return array{id:int, year:int}|null
     */
    public function getElection(int $electionId): ?array
    {
        return $this->electionRepository->getById($electionId);
    }

    /**
     * Get election by year
     * @return array{id:int, year:int}|null
     */
    public function getElectionByYear(int $year): ?array
    {
        return $this->electionRepository->getByYear($year);
    }

    /**
     * Create election for given year (if not exists)
     * Returns election ID
     */
    public function getOrCreateElection(int $year): int
    {
        // Check if already exists
        $election = $this->electionRepository->getByYear($year);
        if ($election) {
            return $election['id'];
        }

        // Create new election
        return $this->electionRepository->create($year);
    }

    /**
     * Delete entire election and related data
     * Returns true if successful
     */
    public function deleteElection(int $electionId): bool
    {
        // Ensure election exists
        if (!$this->electionRepository->exists($electionId)) {
            return false;
        }

        return $this->electionRepository->delete($electionId);
    }

    /**
     * Get current active election (latest one created)
     * @return array{id:int, year:int}|null
     */
    public function getCurrentElection(): ?array
    {
        $elections = $this->electionRepository->getAll();
        return !empty($elections) ? $elections[0] : null;
    }

    /**
     * Get election summary with vote data
     * @return array{election: array{id:int, year:int}, votes: array, states_with_votes: int}
     */
    public function getElectionSummary(int $electionId): array
    {
        $election = $this->electionRepository->getById($electionId);
        if (!$election) {
            return ['election' => null, 'votes' => [], 'states_with_votes' => 0];
        }

        $votes = $this->voteRepository->getVotesByElection($electionId);
        
        // Count states with at least one vote
        $statesWithVotes = count(array_unique(array_column($votes, 'state_id')));

        return [
            'election' => $election,
            'votes' => $votes,
            'states_with_votes' => $statesWithVotes,
        ];
    }

    /**
     * Check if election has any votes
     */
    public function hasVotes(int $electionId): bool
    {
        $votes = $this->voteRepository->getVotesByElection($electionId);
        return !empty($votes);
    }
}
