<?php

namespace app\repositories;

class ResultRepository
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Interroge la vue SQL election_results pour obtenir
     * le total des grands électeurs par candidat pour une élection
     */
    public function getElectionResults(int $electionId): array
    {
        $query = <<<SQL
            SELECT 
                election_id,
                name,
                total_electors
            FROM election_results
            WHERE election_id = :election_id
            ORDER BY total_electors DESC
        SQL;

        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':election_id', $electionId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Interroge la vue SQL election_winner pour obtenir
     * le candidat gagnant d'une élection
     */
    public function getElectionWinner(int $electionId): ?array
    {
        $query = <<<SQL
            SELECT 
                election_id,
                name,
                total_electors
            FROM election_winner
            WHERE election_id = :election_id
        SQL;

        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':election_id', $electionId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }
}
