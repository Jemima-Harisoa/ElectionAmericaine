<?php

namespace app\controllers;

use app\services\AuthService;
use app\services\ResultService;
use app\services\PdfService;

class ResultController
{
    private ResultService $resultService;
    private PdfService $pdfService;
    private AuthService $authService;

    public function __construct(ResultService $resultService, PdfService $pdfService, AuthService $authService)
    {
        $this->resultService = $resultService;
        $this->pdfService = $pdfService;
        $this->authService = $authService;
    }

    public function showResults(): void
    {
        if (!$this->authService->requireAuth()) {
            \Flight::redirect('/login');
            return;
        }

        $electionId = 1;
        $summary = $this->resultService->getSummaryByElection($electionId);

        \Flight::render('results/resultats', [
            'candidates' => $summary['candidates'],
            'winner' => $summary['winner'],
        ], 'content');

        \Flight::render('layout/layout', [
            'pageTitle' => 'Résultats de l\'élection',
            'showNavbar' => true,
            'currentUser' => $this->authService->getCurrentUser(),
        ]);
    }

    public function exportPDF(): void
    {
        if (!$this->authService->requireAuth()) {
            \Flight::redirect('/login');
            return;
        }

        $electionId = 1;
        $this->pdfService->exportResultsPDF($electionId);
    }
}
