<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class UserRepository
{
    public function __construct(private readonly PDO $connection)
    {
    }

    public function findByEmail(string $email): ?array
    {
        $statement = $this->connection->prepare(
            'SELECT id, pseudo, email, password_hash, credits, statut FROM utilisateur WHERE email = :email LIMIT 1'
        );
        $statement->execute(['email' => $email]);
        $user = $statement->fetch();

        if ($user === false) {
            return null;
        }

        $user['roles'] = $this->rolesForUser((int) $user['id']);

        return $user;
    }

    public function createUser(string $pseudo, string $email, string $passwordHash): int
    {
        $statement = $this->connection->prepare(
            'INSERT INTO utilisateur (pseudo, email, password_hash, credits, statut) VALUES (:pseudo, :email, :password_hash, 20, "actif")'
        );
        $statement->execute([
            'pseudo' => $pseudo,
            'email' => $email,
            'password_hash' => $passwordHash,
        ]);

        $userId = (int) $this->connection->lastInsertId();
        $this->attachRole($userId, 'ROLE_USER');
        $this->attachRole($userId, 'ROLE_PASSAGER');

        return $userId;
    }

    public function attachRole(int $userId, string $roleCode): void
    {
        $statement = $this->connection->prepare(
            'INSERT IGNORE INTO utilisateur_role (utilisateur_id, role_id)
             SELECT :user_id, id FROM role WHERE code = :role_code'
        );
        $statement->execute([
            'user_id' => $userId,
            'role_code' => $roleCode,
        ]);
    }

    private function rolesForUser(int $userId): array
    {
        $statement = $this->connection->prepare(
            'SELECT r.code
             FROM role r
             INNER JOIN utilisateur_role ur ON ur.role_id = r.id
             WHERE ur.utilisateur_id = :user_id'
        );
        $statement->execute(['user_id' => $userId]);

        return array_column($statement->fetchAll(), 'code');
    }
}
