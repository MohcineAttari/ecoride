<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\UserService;

final class AuthController
{
    public function login(): void
    {
        view('auth/login', [
            'title' => 'Connexion',
            'error' => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_error']);
    }

    public function register(): void
    {
        view('auth/register', [
            'title' => 'Inscription',
            'error' => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_error']);
    }

    public function authenticate(): void
    {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        $user = (new UserService())->authenticate($email, $password);

        if ($user === null) {
            $_SESSION['flash_error'] = 'Identifiants invalides.';
            redirect('/connexion');
        }

        $_SESSION['user'] = $user;
        redirect('/covoiturages');
    }

    public function store(): void
    {
        $pseudo = trim((string) ($_POST['pseudo'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $service = new UserService();

        if ($pseudo === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = 'Veuillez saisir un pseudo et un email valide.';
            redirect('/inscription');
        }

        $passwordError = $service->validatePassword($password);
        if ($passwordError !== null) {
            $_SESSION['flash_error'] = $passwordError;
            redirect('/inscription');
        }

        $user = $service->register($pseudo, $email, $password);
        unset($user['password_hash']);
        $_SESSION['user'] = $user;

        redirect('/covoiturages');
    }

    public function logout(): void
    {
        unset($_SESSION['user']);
        redirect('/');
    }
}
