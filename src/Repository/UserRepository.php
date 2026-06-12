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
        $user['profile'] = $this->profileFromRoles($user['roles']);

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

    public function updateProfileRoles(int $userId, string $profile): array
    {
        $this->connection->beginTransaction();

        try {
            $this->attachRole($userId, 'ROLE_USER');

            $delete = $this->connection->prepare(
                'DELETE ur
                 FROM utilisateur_role ur
                 INNER JOIN role r ON r.id = ur.role_id
                 WHERE ur.utilisateur_id = :user_id
                   AND r.code IN ("ROLE_PASSAGER", "ROLE_CHAUFFEUR")'
            );
            $delete->execute(['user_id' => $userId]);

            if ($profile === 'passager' || $profile === 'passager_chauffeur') {
                $this->attachRole($userId, 'ROLE_PASSAGER');
            }

            if ($profile === 'chauffeur' || $profile === 'passager_chauffeur') {
                $this->attachRole($userId, 'ROLE_CHAUFFEUR');
            }

            $this->connection->commit();

            return $this->rolesForUser($userId);
        } catch (\Throwable $exception) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }

            throw $exception;
        }
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

    private function profileFromRoles(array $roles): string
    {
        $isPassenger = in_array('ROLE_PASSAGER', $roles, true);
        $isDriver = in_array('ROLE_CHAUFFEUR', $roles, true);

        if ($isPassenger && $isDriver) {
            return 'passager_chauffeur';
        }

        if ($isDriver) {
            return 'chauffeur';
        }

        return 'passager';
    }
}
