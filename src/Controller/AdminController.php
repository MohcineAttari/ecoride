<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Database;
use App\Repository\AdminRepository;
use App\Service\UserService;
use Throwable;

final class AdminController
{
    public function dashboard(): void
    {
        if (!$this->isAdmin()) {
            redirect('/connexion');
        }

        $stats = $this->buildStats();

        view('admin/dashboard', [
            'title' => 'Espace administrateur',
            'accounts' => $this->accounts(),
            'stats' => $stats,
            'success' => $_SESSION['flash_success'] ?? null,
            'error' => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);
    }

    public function storeEmployee(): void
    {
        if (!$this->isAdmin()) {
            redirect('/connexion');
        }

        $pseudo = trim((string) ($_POST['pseudo'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $service = new UserService();

        if ($pseudo === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = 'Veuillez saisir un pseudo et un email valide.';
            redirect('/admin');
        }

        $passwordError = $service->validatePassword($password);
        if ($passwordError !== null) {
            $_SESSION['flash_error'] = $passwordError;
            redirect('/admin');
        }

        $employee = [
            'id' => random_int(10000, 99999),
            'pseudo' => $pseudo,
            'email' => $email,
            'credits' => 0,
            'roles' => ['ROLE_EMPLOYE'],
            'statut' => 'actif',
        ];

        try {
            $repository = new AdminRepository(Database::connection());
            $employee['id'] = $repository->createEmployee($pseudo, $email, password_hash($password, PASSWORD_DEFAULT));
            $employee['source'] = 'sql';
        } catch (Throwable) {
            // Le prototype garde un mode session si la base locale est indisponible.
        }

        $_SESSION['admin_created_accounts'][] = $employee;

        $_SESSION['flash_success'] = 'Compte employe cree.';
        redirect('/admin');
    }

    public function suspendAccount(): void
    {
        if (!$this->isAdmin()) {
            redirect('/connexion');
        }

        $email = trim((string) ($_POST['email'] ?? ''));
        try {
            $repository = new AdminRepository(Database::connection());
            $updated = $repository->suspendAccountByEmail($email);

            if (!$updated) {
                $_SESSION['flash_error'] = 'Compte introuvable, deja suspendu ou non suspendable.';
                redirect('/admin');
            }
        } catch (Throwable) {
            // Le prototype garde un mode session si la base locale est indisponible.
        }

        $_SESSION['suspended_accounts'][$email] = true;
        $_SESSION['flash_success'] = 'Compte suspendu : ' . $email;

        redirect('/admin');
    }

    private function accounts(): array
    {
        try {
            $repository = new AdminRepository(Database::connection());
            $accounts = $repository->accounts();

            $knownEmails = array_column($accounts, 'email');
            foreach ($_SESSION['admin_created_accounts'] ?? [] as $account) {
                if (in_array($account['email'], $knownEmails, true)) {
                    continue;
                }

                $accounts[] = $account;
            }

            return $accounts;
        } catch (Throwable) {
            // Repli sur les donnees de demonstration ci-dessous.
        }

        $accounts = [
            ['pseudo' => 'clara', 'email' => 'clara@example.com', 'roles' => ['ROLE_USER', 'ROLE_CHAUFFEUR'], 'statut' => 'actif'],
            ['pseudo' => 'leo', 'email' => 'leo@example.com', 'roles' => ['ROLE_USER', 'ROLE_PASSAGER'], 'statut' => 'actif'],
            ['pseudo' => 'sophie', 'email' => 'employe@ecoride.local', 'roles' => ['ROLE_EMPLOYE'], 'statut' => 'actif'],
        ];

        $accounts = array_merge($accounts, $_SESSION['admin_created_accounts'] ?? []);
        foreach ($accounts as &$account) {
            if (isset($_SESSION['suspended_accounts'][$account['email']])) {
                $account['statut'] = 'suspendu';
            }
        }

        return $accounts;
    }

    private function buildStats(): array
    {
        try {
            $repository = new AdminRepository(Database::connection());
            $stats = $repository->stats();

            if ($stats['rides_by_day'] !== [] || $stats['credits_by_day'] !== []) {
                return [
                    'rides_by_day' => $stats['rides_by_day'] ?: ['Aucun' => 0],
                    'credits_by_day' => $stats['credits_by_day'] ?: ['Aucun' => 0],
                    'total_credits' => array_sum($stats['credits_by_day']),
                ];
            }
        } catch (Throwable) {
            // Repli sur les statistiques de demonstration ci-dessous.
        }

        $driverRides = $_SESSION['driver_rides'] ?? [];
        $reservations = $_SESSION['reservations'] ?? [];
        $ridesByDay = [];
        $creditsByDay = [];

        foreach ($driverRides as $ride) {
            $day = substr((string) $ride['date_depart'], 0, 10);
            $ridesByDay[$day] = ($ridesByDay[$day] ?? 0) + 1;
        }

        foreach ($reservations as $reservation) {
            $day = substr((string) $reservation['reserved_at'], 0, 10);
            if (($reservation['statut'] ?? 'confirmee') !== 'annulee') {
                $creditsByDay[$day] = ($creditsByDay[$day] ?? 0) + 2;
            }
        }

        if ($ridesByDay === []) {
            $ridesByDay = [
                '2026-06-05' => 2,
                '2026-06-06' => 1,
                '2026-06-10' => 3,
            ];
        }

        if ($creditsByDay === []) {
            $creditsByDay = [
                '2026-06-05' => 6,
                '2026-06-06' => 2,
                '2026-06-10' => 8,
            ];
        }

        return [
            'rides_by_day' => $ridesByDay,
            'credits_by_day' => $creditsByDay,
            'total_credits' => array_sum($creditsByDay),
        ];
    }

    private function isAdmin(): bool
    {
        return is_authenticated() && in_array('ROLE_ADMIN', current_user()['roles'], true);
    }
}
