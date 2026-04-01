<?php

namespace app\services;

use app\repositories\VoteRepository;

class VoteService
{
    private VoteRepository $voteRepository;
    private ?AuditService $auditService = null;

    public function __construct(VoteRepository $voteRepository, ?AuditService $auditService = null)
    {
        $this->voteRepository = $voteRepository;
        $this->auditService = $auditService;
    }

    /**
     * @param array<int|string, int|string> $votesByCandidate
     */
    public function saveVoteForState(int $stateId, int $electionId, array $votesByCandidate, ?int $userId = null): void
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

        // Récupérer les votes existants pour l'audit
        $existingVotes = $this->voteRepository->getVotesByState($stateId, $electionId);

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

            // Récupérer l'ancienne valeur pour l'audit
            $oldValue = $existingVotes[$candidateId] ?? null;
            
            // Enregistrer le changement dans l'audit si le service est disponible
            if ($this->auditService && $userId) {
                // N'enregistrer que si la valeur a changé ou si c'est une première entrée
                if ($oldValue === null || $oldValue != $popularVotes) {
                    $this->auditService->logChange($userId, $stateId, $electionId, $candidateId, $oldValue, (int) $popularVotes);
                }
            }

            $this->voteRepository->upsertVote($stateId, $candidateId, $electionId, (int) $popularVotes);
        }
    }

    /**
     * @return array<int, array{state_id:int, state_name:string, percentages:array<string, float>}>
     */
    public function computePercentages(int $electionId): array
    {
        $rawVotes = $this->voteRepository->getVotesByElection($electionId);

        if (empty($rawVotes)) {
            return [];
        }

        $stateData = [];
        foreach ($rawVotes as $row) {
            $stateId = (int) $row['state_id'];
            $stateName = (string) $row['state_name'];
            $candidateId = (int) $row['candidate_id'];
            $candidateName = (string) $row['candidate_name'];
            $popularVotes = (int) $row['popular_votes'];

            if (!isset($stateData[$stateId])) {
                $stateData[$stateId] = [
                    'state_id' => $stateId,
                    'state_name' => $stateName,
                    'candidates' => [],
                    'total_votes' => 0,
                ];
            }

            $stateData[$stateId]['candidates'][$candidateId] = [
                'name' => $candidateName,
                'votes' => $popularVotes,
            ];
            $stateData[$stateId]['total_votes'] += $popularVotes;
        }

        $result = [];
        foreach ($stateData as $state) {
            $stateId = $state['state_id'];
            $total = $state['total_votes'];
            $percentages = [];

            foreach ($state['candidates'] as $candidateId => $candidate) {
                $pct = $total > 0 ? ($candidate['votes'] / $total) * 100 : 0;
                $percentages[$candidate['name']] = round($pct, 2);
            }

            $result[$stateId] = [
                'state_id' => $state['state_id'],
                'state_name' => $state['state_name'],
                'percentages' => $percentages,
            ];
        }

        return array_values($result);
    }
}
