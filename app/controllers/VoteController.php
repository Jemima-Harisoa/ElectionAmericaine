<?php

namespace app\controllers;

use app\repositories\VoteRepository;
use app\services\AuthService;
use app\services\VoteService;
use app\services\ElectionService;

class VoteController
{
    private VoteRepository $voteRepository;
    private VoteService $voteService;
    private AuthService $authService;
    private ElectionService $electionService;

    public function __construct(VoteRepository $voteRepository, VoteService $voteService, AuthService $authService, ElectionService $electionService)
    {
        $this->voteRepository = $voteRepository;
        $this->voteService = $voteService;
        $this->authService = $authService;
        $this->electionService = $electionService;
    }

    /**
     * Get election ID from request or use current election
     */
    private function getElectionId(): int
    {
        $request = \Flight::request();
        $electionId = filter_var($request->query->election_id ?? $request->data->election_id ?? null, FILTER_VALIDATE_INT);

        if ($electionId && $electionId > 0) {
            // Verify election exists
            if ($this->electionService->getElection($electionId)) {
                return $electionId;
            }
        }

        // Fall back to current election
        $current = $this->electionService->getCurrentElection();
        return $current ? $current['id'] : 1;
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
        $electionId = $this->getElectionId();
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
        $allElections = $this->electionService->getAllElections();
        $currentElection = $this->electionService->getElection($electionId);

        \Flight::render('votes/saisie', [
            'states' => $states,
            'stateId' => $stateId,
            'currentStateName' => $currentStateName,
            'electionId' => $electionId,
            'candidates' => $candidates,
            'existingVotes' => $existingVotes,
            'success' => (int) ($request->query->success ?? 0) === 1,
            'error' => (string) ($request->query->error ?? ''),
            'allElections' => $allElections,
            'currentElection' => $currentElection,
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
        $electionId = $this->getElectionId();
        $votesByCandidate = (array) ($request->data->votes ?? []);

        // Récupérer l'ID de l'utilisateur actuel pour l'audit
        $currentUser = $this->authService->getCurrentUser();
        $userId = $currentUser['id'] ?? null;

        try {
            $this->voteService->saveVoteForState($stateId, $electionId, $votesByCandidate, $userId);
            \Flight::redirect('/tableau?success=1&election_id=' . $electionId);
            return;
        } catch (\Throwable $e) {
            $message = rawurlencode($e->getMessage());
            \Flight::redirect('/saisie?state_id=' . $stateId . '&election_id=' . $electionId . '&error=' . $message);
            return;
        }
    }

    public function showTableau(): void
    {
        if (!$this->authService->requireAuth()) {
            \Flight::redirect('/login');
            return;
        }

        $electionId = $this->getElectionId();
        $percentages = $this->voteService->computePercentages($electionId);

        $request = \Flight::request();
        $success = (int) ($request->query->success ?? 0) === 1;
        
        $allElections = $this->electionService->getAllElections();
        $currentElection = $this->electionService->getElection($electionId);

        \Flight::render('votes/tableau', [
            'percentages' => $percentages,
            'success' => $success,
            'allElections' => $allElections,
            'currentElection' => $currentElection,
            'electionId' => $electionId,
        ], 'content');

        \Flight::render('layout/layout', [
            'pageTitle' => 'Tableau des votes',
            'showNavbar' => true,
            'currentUser' => $this->authService->getCurrentUser(),
        ]);
    }
}
