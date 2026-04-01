<?php

namespace App\Repositories;

use PDO;

class AuditRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Insère une entrée d'audit
     */
    public function insert(int $userId, int $stateId, int $electionId, int $candidateId, ?int $oldValue, int $newValue): bool
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO audit_log (user_id, state_id, election_id, candidate_id, old_value, new_value)
            VALUES (:user_id, :state_id, :election_id, :candidate_id, :old_value, :new_value)'
        );

        return $stmt->execute([
            ':user_id' => $userId,
            ':state_id' => $stateId,
            ':election_id' => $electionId,
            ':candidate_id' => $candidateId,
            ':old_value' => $oldValue,
            ':new_value' => $newValue
        ]);
    }

    /**
     * Retourne tout l'historique d'audit trié par date décroissante
     */
    public function getAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT 
                a.id,
                a.user_id,
                u.username,
                a.state_id,
                s.name as state_name,
                a.election_id,
                a.candidate_id,
                c.name as candidate_name,
                a.old_value,
                a.new_value,
                a.changed_at
            FROM audit_log a
            JOIN users u ON a.user_id = u.id
            JOIN states s ON a.state_id = s.id
            JOIN candidates c ON a.candidate_id = c.id
            ORDER BY a.changed_at DESC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Filtre l'historique par état
     */
    public function getByState(int $stateId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT 
                a.id,
                a.user_id,
                u.username,
                a.state_id,
                s.name as state_name,
                a.election_id,
                a.candidate_id,
                c.name as candidate_name,
                a.old_value,
                a.new_value,
                a.changed_at
            FROM audit_log a
            JOIN users u ON a.user_id = u.id
            JOIN states s ON a.state_id = s.id
            JOIN candidates c ON a.candidate_id = c.id
            WHERE a.state_id = :state_id
            ORDER BY a.changed_at DESC'
        );

        $stmt->execute([':state_id' => $stateId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Retourne une entrée d'audit précise par ID
     */
    public function getEntryById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT 
                a.id,
                a.user_id,
                a.state_id,
                a.election_id,
                a.candidate_id,
                a.old_value,
                a.new_value,
                a.changed_at
            FROM audit_log a
            WHERE a.id = :id'
        );

        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
}
