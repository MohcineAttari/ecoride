<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class AdminRepository
{
    public function __construct(private readonly PDO $connection)
    {
    }

    public function accounts(): array
    {
        $statement = $this->connection->query(
            'SELECT u.id, u.pseudo, u.email, u.credits, u.statut, GROUP_CONCAT(r.code ORDER BY r.code SEPARATOR ",") AS roles
             FROM utilisateur u
             LEFT JOIN utilisateur_role ur ON ur.utilisateur_id = u.id
             LEFT JOIN role r ON r.id = ur.role_id
             GROUP BY u.id, u.pseudo, u.email, u.credits, u.statut
             ORDER BY u.created_at DESC'
        );

        return array_map(static fn (array $account): array => [
            'id' => (int) $account['id'],
            'pseudo' => (string) $account['pseudo'],
            'email' => (string) $account['email'],
            'credits' => (int) $account['credits'],
            'roles' => $account['roles'] !== null && $account['roles'] !== ''
                ? explode(',', (string) $account['roles'])
                : [],
            'statut' => (string) $account['statut'],
            'source' => 'sql',
        ], $statement->fetchAll());
    }

    public function createEmployee(string $pseudo, string $email, string $passwordHash): int
    {
        $statement = $this->connection->prepare(
            'INSERT INTO utilisateur (pseudo, email, password_hash, credits, statut)
             VALUES (:pseudo, :email, :password_hash, 0, "actif")'
        );
        $statement->execute([
            'pseudo' => $pseudo,
            'email' => $email,
            'password_hash' => $passwordHash,
        ]);

        $userId = (int) $this->connection->lastInsertId();
        $this->attachRole($userId, 'ROLE_EMPLOYE');

        return $userId;
    }

    public function suspendAccountByEmail(string $email): bool
    {
        $statement = $this->connection->prepare(
            'UPDATE utilisateur
             SET statut = "suspendu", updated_at = NOW()
             WHERE email = :email
               AND statut <> "suspendu"
               AND id NOT IN (
                   SELECT ur.utilisateur_id
                   FROM utilisateur_role ur
                   INNER JOIN role r ON r.id = ur.role_id
                   WHERE r.code = "ROLE_ADMIN"
               )'
        );
        $statement->execute(['email' => $email]);

        return $statement->rowCount() > 0;
    }

    public function stats(): array
    {
        return [
            'rides_by_day' => $this->ridesByDay(),
            'credits_by_day' => $this->platformCreditsByDay(),
        ];
    }

    private function attachRole(int $userId, string $roleCode): void
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

    private function ridesByDay(): array
    {
        $statement = $this->connection->query(
            'SELECT DATE(date_depart) AS day, COUNT(*) AS total
             FROM covoiturage
             GROUP BY DATE(date_depart)
             ORDER BY day'
        );

        $data = [];
        foreach ($statement->fetchAll() as $row) {
            $data[(string) $row['day']] = (int) $row['total'];
        }

        return $data;
    }

    private function platformCreditsByDay(): array
    {
        $statement = $this->connection->query(
            'SELECT DATE(created_at) AS day, COUNT(*) * 2 AS total
             FROM reservation
             WHERE statut = "validee"
             GROUP BY DATE(created_at)
             ORDER BY day'
        );

        $data = [];
        foreach ($statement->fetchAll() as $row) {
            $data[(string) $row['day']] = (int) $row['total'];
        }

        return $data;
    }
}
