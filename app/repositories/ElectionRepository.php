<?php

namespace app\repositories;

class ElectionRepository
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Get all elections ordered by year DESC
     * @return array<int, array{id:int, year:int}>
     */
    public function getAll(): array
    {
        $sql = 'SELECT id, year FROM elections ORDER BY year DESC';
        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Get election by ID
     * @return array{id:int, year:int}|null
     */
    public function getById(int $electionId): ?array
    {
        $sql = 'SELECT id, year FROM elections WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $electionId, \PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Get election by year
     * @return array{id:int, year:int}|null
     */
    public function getByYear(int $year): ?array
    {
        $sql = 'SELECT id, year FROM elections WHERE year = :year';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':year', $year, \PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Check if election exists
     */
    public function exists(int $electionId): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM elections WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $electionId, \PDO::PARAM_INT);
        $stmt->execute();

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Check if election year exists
     */
    public function yearExists(int $year): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM elections WHERE year = :year LIMIT 1');
        $stmt->bindValue(':year', $year, \PDO::PARAM_INT);
        $stmt->execute();

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Create new election with given year
     * Also initializes all votes to 0
     */
    public function create(int $year): int
    {
        // Create election entry
        $sql = 'INSERT INTO elections (year) VALUES (:year)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':year', $year, \PDO::PARAM_INT);
        $stmt->execute();

        $electionId = (int) $this->pdo->lastInsertId();

        // Get candidates for this election from existing elections first
        // For new election, copy candidates from most recent election
        $latestElection = $this->getLatestBefore($year);
        if ($latestElection) {
            $this->copyCandidatesFromElection($latestElection['id'], $electionId);
        } else {
            // If no previous election, add Biden and Trump
            $this->ensureDefaultCandidates($electionId);
        }

        return $electionId;
    }

    /**
     * Delete election and all related votes/audit entries
     */
    public function delete(int $electionId): bool
    {
        // Delete in order: audit entries, votes, election_candidates, election
        $this->pdo->beginTransaction();

        try {
            // Delete audit entries
            $sql = 'DELETE FROM audit_log WHERE election_id = :election_id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':election_id', $electionId, \PDO::PARAM_INT);
            $stmt->execute();

            // Delete votes
            $sql = 'DELETE FROM votes WHERE election_id = :election_id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':election_id', $electionId, \PDO::PARAM_INT);
            $stmt->execute();

            // Delete election_candidates
            $sql = 'DELETE FROM election_candidates WHERE election_id = :election_id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':election_id', $electionId, \PDO::PARAM_INT);
            $stmt->execute();

            // Delete election
            $sql = 'DELETE FROM elections WHERE id = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $electionId, \PDO::PARAM_INT);
            $stmt->execute();

            $this->pdo->commit();
            return true;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    /**
     * Get latest election before given year
     * @return array{id:int, year:int}|null
     */
    private function getLatestBefore(int $year): ?array
    {
        $sql = 'SELECT id, year FROM elections WHERE year < :year ORDER BY year DESC LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':year', $year, \PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Copy candidates from one election to another
     */
    private function copyCandidatesFromElection(int $sourceElectionId, int $targetElectionId): void
    {
        $sql = 'INSERT INTO election_candidates (election_id, candidate_id)
                SELECT :target_election_id, candidate_id
                FROM election_candidates
                WHERE election_id = :source_election_id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':source_election_id', $sourceElectionId, \PDO::PARAM_INT);
        $stmt->bindValue(':target_election_id', $targetElectionId, \PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Ensure default candidates (Biden, Trump) exist for election
     */
    private function ensureDefaultCandidates(int $electionId): void
    {
        // Check if Biden and Trump exist
        $sql = 'SELECT id FROM candidates WHERE name IN ("Joe Biden", "Donald Trump")';
        $stmt = $this->pdo->query($sql);
        $candidates = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (count($candidates) === 2) {
            // Link them to the election
            for ($i = 1; $i <= 2; $i++) {
                $sql = 'INSERT INTO election_candidates (election_id, candidate_id)
                        VALUES (:election_id, :candidate_id)
                        ON DUPLICATE KEY UPDATE election_id = election_id';
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindValue(':election_id', $electionId, \PDO::PARAM_INT);
                $stmt->bindValue(':candidate_id', $i, \PDO::PARAM_INT);
                $stmt->execute();
            }
        }
    }
}
