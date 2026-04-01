<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\AuditService;

class AuditController
{
    private AuthService $authService;
    private AuditService $auditService;

    public function __construct(AuthService $authService, AuditService $auditService)
    {
        $this->authService = $authService;
        $this->auditService = $auditService;
    }

    /**
     * Affiche tout l'historique d'audit
     */
    public function showAudit(): void
    {
        // Vérifier l'authentification et les permissions admin
        if (!$this->authService->requireAdmin()) {
            \Flight::redirect('/login');
            return;
        }

        $currentUser = $this->authService->getCurrentUser();
        $history = $this->auditService->getAllHistory();

        \Flight::render('audit/historique', [
            'pageTitle' => 'Audit & Historique',
            'currentUser' => $currentUser,
            'history' => $history,
            'filterState' => null
        ]);
    }

    /**
     * Affiche l'historique filtré par état
     */
    public function showAuditByState(int $stateId = null): void
    {
        // Vérifier l'authentification et les permissions admin
        if (!$this->authService->requireAdmin()) {
            \Flight::redirect('/login');
            return;
        }

        $currentUser = $this->authService->getCurrentUser();
        $history = $stateId ? $this->auditService->getHistoryByState($stateId) : [];

        \Flight::render('audit/historique', [
            'pageTitle' => 'Audit & Historique',
            'currentUser' => $currentUser,
            'history' => $history,
            'filterState' => $stateId
        ]);
    }

    /**
     * Traite le rollback d'une entrée d'audit
     */
    public function handleRollback(): void
    {
        // Vérifier l'authentification et les permissions admin
        if (!$this->authService->requireAdmin()) {
            \Flight::json(['error' => 'Accès non autorisé'], 403);
            return;
        }

        $entryId = $_POST['entry_id'] ?? null;
        
        if (!$entryId) {
            \Flight::json(['error' => 'ID entrée manquant'], 400);
            return;
        }

        if ($this->auditService->rollback((int)$entryId)) {
            \Flight::json(['success' => true, 'message' => 'Rollback effectué avec succès']);
        } else {
            \Flight::json(['error' => 'Erreur lors du rollback'], 500);
        }
    }

    /**
     * Déclenche le téléchargement d'un CSV de l'historique
     */
    public function exportCSV(): void
    {
        // Vérifier l'authentification et les permissions admin
        if (!$this->authService->requireAdmin()) {
            \Flight::redirect('/login');
            return;
        }

        $csv = $this->auditService->exportHistoryCSV();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="audit_' . date('Y-m-d_H-i-s') . '.csv"');
        echo $csv;
        exit;
    }
}
