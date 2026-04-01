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

    public function getStateDetail($id = null): void
    {
        if (!$this->authService->requireAuth()) {
            \Flight::halt(401, 'Non authentifié');
            return;
        }

        // Récupérer l'ID du paramètre ou de l'URL
        if ($id === null) {
            $id = (int) (\Flight::request()->url_vars['id'] ?? 0);
        } else {
            $id = (int) $id;
        }

        if ($id <= 0) {
            \Flight::json(['error' => 'ID état invalide'], 400);
            return;
        }

        $electionId = 1;
        $votes = $this->voteRepository->getVotesByState($id, $electionId);

        // Récupérer les candidats pour cette élection
        $candidates = $this->voteRepository->getCandidatesByElection($electionId);
        $candidatesMap = [];
        foreach ($candidates as $candidate) {
            $candidatesMap[(int) $candidate['id']] = $candidate['name'];
        }

        // Formater les détails pour l'AJAX
        $details = [
            'state_id' => $id,
            'votes' => [],
        ];

        // Ajouter les votes s'il y en a
        if (!empty($votes)) {
            foreach ($votes as $candidateId => $voteCount) {
                if (isset($candidatesMap[$candidateId])) {
                    $details['votes'][] = [
                        'candidate' => $candidatesMap[$candidateId],
                        'votes' => (int) $voteCount,
                    ];
                }
            }
        } else {
            // Si pas de votes, retourner quand même les candidats avec 0 votes
            foreach ($candidatesMap as $candidateId => $candidateName) {
                $details['votes'][] = [
                    'candidate' => $candidateName,
                    'votes' => 0,
                ];
            }
        }

        \Flight::json($details, 200);
    }
}
