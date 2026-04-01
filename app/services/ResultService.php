<?php

namespace app\services;

use app\repositories\ResultRepository;

class ResultService
{
    private ResultRepository $resultRepository;

    public function __construct(ResultRepository $resultRepository)
    {
        $this->resultRepository = $resultRepository;
    }

    /**
     * Retourne un résumé complet des résultats électoraux :
     * - Liste de tous les candidats avec leurs grands électeurs
     * - Le candidat gagnant
     */
    public function getSummaryByElection(int $electionId): array
    {
        $results = $this->resultRepository->getElectionResults($electionId);
        $winner = $this->resultRepository->getElectionWinner($electionId);

        return [
            'candidates' => $results,
            'winner' => $winner,
        ];
    }
}
