<?php

declare(strict_types=1);

namespace App\Service;

use App\Core\Database;
use App\Repository\UserRepository;
use Throwable;

final class UserService
{
    private array $demoUsers;

    public function __construct()
    {
        $this->demoUsers = [
            [
                'id' => 1,
                'pseudo' => 'clara',
                'email' => 'clara@example.com',
                'password_hash' => password_hash('Password123!', PASSWORD_DEFAULT),
                'credits' => 30,
                'roles' => ['ROLE_USER', 'ROLE_CHAUFFEUR'],
            ],
            [
                'id' => 2,
                'pseudo' => 'leo',
                'email' => 'leo@example.com',
                'password_hash' => password_hash('Password123!', PASSWORD_DEFAULT),
                'credits' => 20,
                'roles' => ['ROLE_USER', 'ROLE_PASSAGER'],
            ],
            [
                'id' => 3,
                'pseudo' => 'sophie',
                'email' => 'employe@ecoride.local',
                'password_hash' => password_hash('Password123!', PASSWORD_DEFAULT),
                'credits' => 0,
                'roles' => ['ROLE_EMPLOYE'],
            ],
            [
                'id' => 4,
                'pseudo' => 'admin',
                'email' => 'admin@ecoride.local',
                'password_hash' => password_hash('Password123!', PASSWORD_DEFAULT),
                'credits' => 0,
                'roles' => ['ROLE_ADMIN'],
            ],
        ];
    }

    public function authenticate(string $email, string $password): ?array
    {
        $sqlUser = $this->authenticateFromDatabase($email, $password);
        if ($sqlUser !== null) {
            return $sqlUser;
        }

        foreach ($this->demoUsers as $user) {
            if ($user['email'] === $email && password_verify($password, $user['password_hash'])) {
                unset($user['password_hash']);
                return $user;
            }
        }

        return null;
    }

    public function register(string $pseudo, string $email, string $password): array
    {
        try {
            $repository = new UserRepository(Database::connection());
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $userId = $repository->createUser($pseudo, $email, $passwordHash);

            return [
                'id' => $userId,
                'pseudo' => $pseudo,
                'email' => $email,
                'credits' => 20,
                'roles' => ['ROLE_USER', 'ROLE_PASSAGER'],
            ];
        } catch (Throwable) {
            // Le prototype continue de fonctionner en session si la base locale est indisponible.
        }

        return [
            'id' => random_int(1000, 9999),
            'pseudo' => $pseudo,
            'email' => $email,
            'credits' => 20,
            'roles' => ['ROLE_USER', 'ROLE_PASSAGER'],
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ];
    }

    public function validatePassword(string $password): ?string
    {
        if (strlen($password) < 8) {
            return 'Le mot de passe doit contenir au moins 8 caracteres.';
        }

        if (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            return 'Le mot de passe doit contenir une majuscule, une minuscule et un chiffre.';
        }

        return null;
    }

    private function authenticateFromDatabase(string $email, string $password): ?array
    {
        try {
            $repository = new UserRepository(Database::connection());
            $user = $repository->findByEmail($email);

            if ($user === null || $user['statut'] !== 'actif' || !password_verify($password, $user['password_hash'])) {
                return null;
            }

            unset($user['password_hash'], $user['statut']);

            return $user;
        } catch (Throwable) {
            return null;
        }
    }
}
