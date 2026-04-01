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

    /**
     * Interroge la vue SQL state_winners pour obtenir
     * le gagnant dans chaque état (avec détails)
     */
    public function getStateWinners(int $electionId): array
    {
        $query = <<<SQL
            SELECT 
                sw.state_id,
                s.name as state_name,
                s.electoral_votes,
                sw.candidate_id,
                c.name as candidate_name
            FROM state_winners sw
            JOIN states s ON sw.state_id = s.id
            JOIN candidates c ON sw.candidate_id = c.id
            WHERE sw.election_id = :election_id
            ORDER BY s.name ASC
        SQL;

        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':election_id', $electionId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
