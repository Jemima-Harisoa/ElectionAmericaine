<?php

namespace app\services;

use app\repositories\ResultRepository;
use app\repositories\VoteRepository;

class MapService
{
    private ResultRepository $resultRepository;
    private VoteRepository $voteRepository;

    public function __construct(ResultRepository $resultRepository, VoteRepository $voteRepository)
    {
        $this->resultRepository = $resultRepository;
        $this->voteRepository = $voteRepository;
    }

    /**
     * Enrichit les données state_winners avec les couleurs par candidat
     * Biden = bleu (#457bdd)
     * Trump = rouge (#d42121)
     * Pas de données = gris (#d5dfef)
     */
    public function getMapData(int $electionId): array
    {
        $stateWinners = $this->resultRepository->getStateWinners($electionId);
        $allStates = $this->voteRepository->getStates();

        // Créer une map pour lookups rapides
        $winnersMap = [];
        foreach ($stateWinners as $winner) {
            $winnersMap[(int) $winner['state_id']] = $winner;
        }

        // Enrichir chaque état avec les données et la couleur
        $mapData = [];
        foreach ($allStates as $state) {
            $stateId = (int) $state['id'];
            $stateName = $state['name'];
            $electoralVotes = (int) $state['electoral_votes'];

            if (isset($winnersMap[$stateId])) {
                $winner = $winnersMap[$stateId];
                $candidateName = $winner['candidate_name'];

                // Déterminer la couleur basée sur le candidat
                $color = strpos($candidateName, 'Biden') !== false ? '#457bdd' : '#d42121';

                $mapData[] = [
                    'state_id' => $stateId,
                    'state_name' => $stateName,
                    'electoral_votes' => $electoralVotes,
                    'candidate_id' => (int) $winner['candidate_id'],
                    'candidate_name' => $candidateName,
                    'color' => $color,
                ];
            } else {
                // Pas de données pour cet état
                $mapData[] = [
                    'state_id' => $stateId,
                    'state_name' => $stateName,
                    'electoral_votes' => $electoralVotes,
                    'candidate_id' => null,
                    'candidate_name' => '(À remplir)',
                    'color' => '#d5dfef', // Gris
                ];
            }
        }

        return $mapData;
    }
}
