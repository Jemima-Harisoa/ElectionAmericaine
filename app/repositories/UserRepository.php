<?php

namespace app\repositories;

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
     * @param array{username: string, password: string, role: string} $data
     * @return bool
     * @throws \InvalidArgumentException Si les données sont manquantes
     */
    public function save(array $data): bool
    {
        if (empty($data['username']) || empty($data['password']) || empty($data['role'])) {
            throw new \InvalidArgumentException("Données utilisateur incomplètes.");
        }

        $allowedRoles = ['admin', 'observer'];
        if (in_array($data['role'], $allowedRoles, true) === false) {
            throw new \InvalidArgumentException("Rôle invalide. Valeurs autorisées: admin, observer.");
        }

        $sql = "INSERT INTO users (username, password_hash, role)
                VALUES (:username, :password_hash, :role)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':username',      $data['username'],                   \PDO::PARAM_STR);
        $stmt->bindValue(':password_hash', $this->hashPassword($data['password']), \PDO::PARAM_STR);
        $stmt->bindValue(':role',          $data['role'],                       \PDO::PARAM_STR);

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
        $sql = "SELECT id, username, password_hash, role, role AS role_name
            FROM users
                WHERE username = :username";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':username', $username, \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Alias pour compatibilité avec le nom prévu dans la TODO.
     *
     * @param string $username
     * @return array<string, mixed>|false
     */
    public function findByUsername(string $username): array|false
    {
        return $this->getByUsername($username);
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