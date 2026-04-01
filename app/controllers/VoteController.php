<?php

namespace app\controllers;

use app\repositories\VoteRepository;
use app\services\AuthService;
use app\services\VoteService;

class VoteController
{
    private VoteRepository $voteRepository;
    private VoteService $voteService;
    private AuthService $authService;

    public function __construct(VoteRepository $voteRepository, VoteService $voteService, AuthService $authService)
    {
        $this->voteRepository = $voteRepository;
        $this->voteService = $voteService;
        $this->authService = $authService;
    }

    public function showSaisie(): void
    {
        if (!$this->authService->requireAuth()) {
            \Flight::redirect('/login');
            return;
        }

        if (!$this->authService->requireAdmin()) {
            \Flight::halt(403, 'Acces refuse.');
            return;
        }

        $request = \Flight::request();
        $electionId = 1;
        $states = $this->voteRepository->getStates();

        if (empty($states)) {
            \Flight::halt(500, 'Aucun etat disponible.');
            return;
        }

        // No default state - only use if explicitly provided
        $stateId = (int) ($request->query->state_id ?? 0);
        if ($stateId > 0 && !$this->voteRepository->stateExists($stateId)) {
            $stateId = 0;
        }

        $currentStateName = '';
        if ($stateId > 0) {
            foreach ($states as $state) {
                if ((int) $state['id'] === $stateId) {
                    $currentStateName = $state['name'] . ' (' . ((int) $state['electoral_votes']) . ' GE)';
                    break;
                }
            }
        }

        $candidates = $this->voteRepository->getCandidatesByElection($electionId);
        $existingVotes = $stateId > 0 ? $this->voteRepository->getVotesByState($stateId, $electionId) : [];

        \Flight::render('votes/saisie', [
            'states' => $states,
            'stateId' => $stateId,
            'currentStateName' => $currentStateName,
            'electionId' => $electionId,
            'candidates' => $candidates,
            'existingVotes' => $existingVotes,
            'success' => (int) ($request->query->success ?? 0) === 1,
            'error' => (string) ($request->query->error ?? ''),
        ], 'content');

        \Flight::render('layout/layout', [
            'pageTitle' => 'Saisie des votes',
            'showNavbar' => true,
            'currentUser' => $this->authService->getCurrentUser(),
        ]);
    }

    public function handleSaisie(): void
    {
        if (!$this->authService->requireAuth()) {
            \Flight::redirect('/login');
            return;
        }

        if (!$this->authService->requireAdmin()) {
            \Flight::halt(403, 'Acces refuse.');
            return;
        }

        $request = \Flight::request();
        $stateId = (int) ($request->data->state_id ?? 0);
        $electionId = (int) ($request->data->election_id ?? 1);
        $votesByCandidate = (array) ($request->data->votes ?? []);

        // Récupérer l'ID de l'utilisateur actuel pour l'audit
        $currentUser = $this->authService->getCurrentUser();
        $userId = $currentUser['id'] ?? null;

        try {
            $this->voteService->saveVoteForState($stateId, $electionId, $votesByCandidate, $userId);
            \Flight::redirect('/tableau?success=1');
            return;
        } catch (\Throwable $e) {
            $message = rawurlencode($e->getMessage());
            \Flight::redirect('/saisie?state_id=' . $stateId . '&error=' . $message);
            return;
        }
    }

    public function showTableau(): void
    {
        if (!$this->authService->requireAuth()) {
            \Flight::redirect('/login');
            return;
        }

        $electionId = 1;
        $percentages = $this->voteService->computePercentages($electionId);

        $request = \Flight::request();
        $success = (int) ($request->query->success ?? 0) === 1;

        \Flight::render('votes/tableau', [
            'percentages' => $percentages,
            'success' => $success,
        ], 'content');

        \Flight::render('layout/layout', [
            'pageTitle' => 'Tableau des votes',
            'showNavbar' => true,
            'currentUser' => $this->authService->getCurrentUser(),
        ]);
    }
}
