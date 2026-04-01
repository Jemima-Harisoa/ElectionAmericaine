<?php

namespace app\services;

use app\repositories\VoteRepository;

class VoteService
{
    private VoteRepository $voteRepository;

    public function __construct(VoteRepository $voteRepository)
    {
        $this->voteRepository = $voteRepository;
    }

    /**
     * @param array<int|string, int|string> $votesByCandidate
     */
    public function saveVoteForState(int $stateId, int $electionId, array $votesByCandidate): void
    {
        if ($stateId <= 0 || $electionId <= 0) {
            throw new \InvalidArgumentException('Etat ou election invalide.');
        }

        if (!$this->voteRepository->stateExists($stateId)) {
            throw new \InvalidArgumentException('Etat introuvable.');
        }

        if (empty($votesByCandidate)) {
            throw new \InvalidArgumentException('Aucun vote fourni.');
        }

        foreach ($votesByCandidate as $candidateIdRaw => $popularVotesRaw) {
            $candidateId = (int) $candidateIdRaw;
            $popularVotes = filter_var($popularVotesRaw, FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 0],
            ]);

            if ($candidateId <= 0) {
                throw new \InvalidArgumentException('Candidat invalide.');
            }

            if ($popularVotes === false) {
                throw new \InvalidArgumentException('Les votes doivent etre des entiers positifs ou nuls.');
            }

            if (!$this->voteRepository->candidateExistsInElection($candidateId, $electionId)) {
                throw new \InvalidArgumentException('Candidat non inscrit a cette election.');
            }

            $this->voteRepository->upsertVote($stateId, $candidateId, $electionId, (int) $popularVotes);
        }
    }
}
