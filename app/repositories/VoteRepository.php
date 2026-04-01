<?php

namespace app\repositories;

class VoteRepository
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return array<int, array{id:int, name:string, electoral_votes:int}>
     */
    public function getStates(): array
    {
        $sql = 'SELECT id, name, electoral_votes FROM states ORDER BY name ASC';
        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * @return array<int, array{id:int, name:string, party:string|null}>
     */
    public function getCandidatesByElection(int $electionId): array
    {
        $sql = 'SELECT c.id, c.name, c.party
                FROM election_candidates ec
                JOIN candidates c ON c.id = ec.candidate_id
                WHERE ec.election_id = :election_id
                ORDER BY c.id ASC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':election_id', $electionId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * @return array<int, int> candidate_id => popular_votes
     */
    public function getVotesByState(int $stateId, int $electionId): array
    {
        $sql = 'SELECT candidate_id, popular_votes
                FROM votes
                WHERE state_id = :state_id AND election_id = :election_id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':state_id', $stateId, \PDO::PARAM_INT);
        $stmt->bindValue(':election_id', $electionId, \PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        $votes = [];
        foreach ($rows as $row) {
            $votes[(int) $row['candidate_id']] = (int) $row['popular_votes'];
        }

        return $votes;
    }

    public function upsertVote(int $stateId, int $candidateId, int $electionId, int $popularVotes): bool
    {
        $sql = 'INSERT INTO votes (state_id, candidate_id, election_id, popular_votes)
                VALUES (:state_id, :candidate_id, :election_id, :popular_votes)
                ON DUPLICATE KEY UPDATE popular_votes = VALUES(popular_votes)';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':state_id', $stateId, \PDO::PARAM_INT);
        $stmt->bindValue(':candidate_id', $candidateId, \PDO::PARAM_INT);
        $stmt->bindValue(':election_id', $electionId, \PDO::PARAM_INT);
        $stmt->bindValue(':popular_votes', $popularVotes, \PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function stateExists(int $stateId): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM states WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $stateId, \PDO::PARAM_INT);
        $stmt->execute();

        return (bool) $stmt->fetchColumn();
    }

    public function candidateExistsInElection(int $candidateId, int $electionId): bool
    {
        $sql = 'SELECT 1
                FROM election_candidates
                WHERE candidate_id = :candidate_id AND election_id = :election_id
                LIMIT 1';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':candidate_id', $candidateId, \PDO::PARAM_INT);
        $stmt->bindValue(':election_id', $electionId, \PDO::PARAM_INT);
        $stmt->execute();

        return (bool) $stmt->fetchColumn();
    }
    /**
     * @return array<int, array{state_id:int, state_name:string, candidate_id:int, candidate_name:string, popular_votes:int}>
     */
    public function getVotesByElection(int $electionId): array
    {
        $sql = 'SELECT v.state_id, s.name as state_name, v.candidate_id, c.name as candidate_name, v.popular_votes
                FROM votes v
                JOIN states s ON v.state_id = s.id
                JOIN candidates c ON v.candidate_id = c.id
                WHERE v.election_id = :election_id
                ORDER BY s.name, c.name';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':election_id', $electionId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }
}