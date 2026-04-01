<?php

namespace App\Services;

use App\Repositories\AuditRepository;
use App\Repositories\VoteRepository;

class AuditService
{
    private AuditRepository $auditRepository;
    private VoteRepository $voteRepository;

    public function __construct(AuditRepository $auditRepository, VoteRepository $voteRepository)
    {
        $this->auditRepository = $auditRepository;
        $this->voteRepository = $voteRepository;
    }

    /**
     * Enregistre un changement dans l'audit
     * Appelé automatiquement lors de la sauvegarde d'un vote
     */
    public function logChange(int $userId, int $stateId, int $electionId, int $candidateId, ?int $oldValue, int $newValue): void
    {
        $this->auditRepository->insert($userId, $stateId, $electionId, $candidateId, $oldValue, $newValue);
    }

    /**
     * Retourne l'historique formaté pour la vue
     */
    public function getHistoryByState(int $stateId): array
    {
        $entries = $this->auditRepository->getByState($stateId);
        
        foreach ($entries as &$entry) {
            $entry['changed_at_formatted'] = date('d/m/Y H:i:s', strtotime($entry['changed_at']));
        }

        return $entries;
    }

    /**
     * Retourne tout l'historique formaté
     */
    public function getAllHistory(): array
    {
        $entries = $this->auditRepository->getAll();
        
        foreach ($entries as &$entry) {
            $entry['changed_at_formatted'] = date('d/m/Y H:i:s', strtotime($entry['changed_at']));
        }

        return $entries;
    }

    /**
     * Effectue un rollback sur une entrée d'audit
     * Réécrit les votes à l'ancienne valeur
     */
    public function rollback(int $entryId): bool
    {
        $entry = $this->auditRepository->getEntryById($entryId);
        if (!$entry) {
            return false;
        }

        // Réinsérer le vote avec l'ancienne valeur
        return $this->voteRepository->upsertVote(
            $entry['state_id'],
            $entry['election_id'],
            $entry['candidate_id'],
            $entry['old_value'] ?? 0
        );
    }

    /**
     * Génère un CSV de l'historique
     */
    public function exportHistoryCSV(): string
    {
        $entries = $this->auditRepository->getAll();
        
        $csv = "Date,Utilisateur,État,Candidat,Ancienne valeur,Nouvelle valeur\n";

        foreach ($entries as $entry) {
            $date = date('d/m/Y H:i:s', strtotime($entry['changed_at']));
            $oldValue = $entry['old_value'] ?? '-';
            
            $csv .= sprintf(
                '"%s","%s","%s","%s","%s","%s"' . "\n",
                $date,
                $entry['username'],
                $entry['state_name'],
                $entry['candidate_name'],
                $oldValue,
                $entry['new_value']
            );
        }

        return $csv;
    }
}
