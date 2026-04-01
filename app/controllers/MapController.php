<?php

namespace app\controllers;

use app\services\MapService;
use app\services\AuthService;
use app\repositories\VoteRepository;

class MapController
{
    private MapService $mapService;
    private VoteRepository $voteRepository;
    private AuthService $authService;

    public function __construct(MapService $mapService, VoteRepository $voteRepository, AuthService $authService)
    {
        $this->mapService = $mapService;
        $this->voteRepository = $voteRepository;
        $this->authService = $authService;
    }

    public function showMap(): void
    {
        if (!$this->authService->requireAuth()) {
            \Flight::redirect('/login');
            return;
        }

        $electionId = 1;
        $mapData = $this->mapService->getMapData($electionId);

        \Flight::render('map/carte', [
            'mapData' => $mapData,
        ], 'content');

        \Flight::render('layout/layout', [
            'pageTitle' => 'Carte des résultats',
            'showNavbar' => true,
            'currentUser' => $this->authService->getCurrentUser(),
        ]);
    }

    public function getStateDetail(): void
    {
        if (!$this->authService->requireAuth()) {
            \Flight::halt(401, 'Non authentifié');
            return;
        }

        $stateId = (int) \Flight::request()->url_vars['id'] ?? 0;

        if ($stateId <= 0) {
            \Flight::json(['error' => 'ID état invalide'], 400);
            return;
        }

        $electionId = 1;
        $votes = $this->voteRepository->getVotesByState($stateId, $electionId);

        if (empty($votes)) {
            \Flight::json(['error' => 'Aucune donnée pour cet état'], 404);
            return;
        }

        // Formater les détails pour l'AJAX
        $details = [
            'state_id' => $stateId,
            'votes' => [],
        ];

        // Récupérer les candidats pour cette élection
        $candidates = $this->voteRepository->getCandidatesByElection($electionId);
        $candidatesMap = [];
        foreach ($candidates as $candidate) {
            $candidatesMap[(int) $candidate['id']] = $candidate['name'];
        }

        foreach ($votes as $candidateId => $voteCount) {
            if (isset($candidatesMap[$candidateId])) {
                $details['votes'][] = [
                    'candidate' => $candidatesMap[$candidateId],
                    'votes' => (int) $voteCount,
                ];
            }
        }

        \Flight::json($details, 200);
    }
}
