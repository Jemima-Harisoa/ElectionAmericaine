<?php

namespace app\repositories;

use flight\database\PdoWrapper;

class UserRepository
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Sauvegarde un nouvel utilisateur dans la base de données
     *
     * @param array{username: string, password: string, role_id: int} $data
     * @return bool
     * @throws \InvalidArgumentException Si les données sont manquantes
     */
    public function save(array $data): bool
    {
        if (empty($data['username']) || empty($data['password']) || !isset($data['role_id'])) {
            throw new \InvalidArgumentException("Données utilisateur incomplètes.");
        }

        $sql = "INSERT INTO users (username, password_hash, role_id) 
                VALUES (:username, :password_hash, :role_id)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':username',      $data['username'],                   \PDO::PARAM_STR);
        $stmt->bindValue(':password_hash', $this->hashPassword($data['password']), \PDO::PARAM_STR);
        $stmt->bindValue(':role_id',       $data['role_id'],                    \PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Récupère un utilisateur par son nom d'utilisateur
     *
     * @param string $username
     * @return array<string, mixed>|false  Tableau associatif ou false si non trouvé
     */
    public function getByUsername(string $username): array|false
    {
        $sql = "SELECT users.id as id, username, password_hash, role_id, role.name AS role_name 
                FROM users join role on users.role_id = role.id 
                WHERE username = :username";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':username', $username, \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Vérifie si un mot de passe correspond au hash stocké en base
     * 
     * @param string $password     Le mot de passe en clair saisi par l'utilisateur
     * @param string $storedHash   Le hash récupéré depuis la base de données
     * @return bool
     */
    public function verifyPassword(string $password, string $storedHash): bool
    {
        return password_verify($password, $storedHash);
    }

    /**
     * Hash un mot de passe avec bcrypt
     *
     * @param string $password Le mot de passe en clair
     * @return string          Le hash bcrypt
     */
    private function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}