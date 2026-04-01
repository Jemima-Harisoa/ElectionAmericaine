<?php

namespace app\controllers;

use app\services\AuthService;
use app\services\ElectionService;

class ElectionsController
{
    private ElectionService $electionService;
    private AuthService $authService;

    public function __construct(ElectionService $electionService, AuthService $authService)
    {
        $this->electionService = $electionService;
        $this->authService = $authService;
    }

    /**
     * Display all elections and option to create new one
     */
    public function showElections(): void
    {
        if (!$this->authService->requireAuth()) {
            \Flight::redirect('/login');
            return;
        }

        if (!$this->authService->requireAdmin()) {
            \Flight::halt(403, 'Acces refuse.');
            return;
        }

        $elections = $this->electionService->getAllElections();

        \Flight::render('elections/list', [
            'elections' => $elections,
        ], 'content');

        \Flight::render('layout/layout', [
            'pageTitle' => 'Gestion des élections',
            'showNavbar' => true,
            'currentUser' => $this->authService->getCurrentUser(),
        ]);
    }

    /**
     * Create new election
     * Expected POST data: year (int)
     */
    public function handleCreateElection(): void
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
        $year = filter_var($request->data->year, FILTER_VALIDATE_INT);

        if ($year === false || $year < 2000 || $year > 2100) {
            \Flight::json([
                'error' => 'Année invalide. Veuillez entrer une année entre 2000 et 2100.',
            ], 400);
            return;
        }

        // Check if year already exists
        $existing = $this->electionService->getElectionByYear($year);
        if ($existing) {
            \Flight::json([
                'error' => 'L\'élection de ' . $year . ' existe déjà.',
            ], 409);
            return;
        }

        try {
            $electionId = $this->electionService->getOrCreateElection($year);
            
            \Flight::json([
                'success' => true,
                'election_id' => $electionId,
                'year' => $year,
                'message' => 'Élection ' . $year . ' créée avec succès.',
            ]);
        } catch (\Exception $e) {
            \Flight::json([
                'error' => 'Erreur lors de la création : ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete election (admin only)
     * Expected: election_id in POST
     */
    public function handleDeleteElection(): void
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
        $electionId = filter_var($request->data->election_id, FILTER_VALIDATE_INT);

        if ($electionId === false || $electionId <= 0) {
            \Flight::json([
                'error' => 'ID élection invalide.',
            ], 400);
            return;
        }

        if ($this->electionService->deleteElection($electionId)) {
            \Flight::json([
                'success' => true,
                'message' => 'Élection supprimée avec succès.',
            ]);
        } else {
            \Flight::json([
                'error' => 'Erreur lors de la suppression.',
            ], 500);
        }
    }
}
