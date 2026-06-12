<?php

declare(strict_types=1);

namespace App\Controller;

final class HomeController
{
    public function index(): void
    {
        view('home/index', [
            'title' => 'Accueil',
        ]);
    }

    public function legal(): void
    {
        view('home/legal', [
            'title' => 'Mentions legales',
        ]);
    }

    public function contact(): void
    {
        view('home/contact', [
            'title' => 'Contact',
            'success' => $_SESSION['flash_success'] ?? null,
            'error' => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);
    }

    public function storeContact(): void
    {
        $name = trim((string) ($_POST['nom'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $subject = trim((string) ($_POST['sujet'] ?? ''));
        $message = trim((string) ($_POST['message'] ?? ''));

        if ($name === '' || $email === '' || $subject === '' || $message === '') {
            $_SESSION['flash_error'] = 'Veuillez remplir tous les champs du formulaire de contact.';
            redirect('/contact');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = 'Veuillez saisir une adresse email valide.';
            redirect('/contact');
        }

        $_SESSION['contact_messages'][] = [
            'nom' => $name,
            'email' => $email,
            'sujet' => $subject,
            'message' => $message,
            'status' => 'nouveau',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $_SESSION['flash_success'] = 'Votre message a bien ete transmis a l equipe EcoRide.';
        redirect('/contact');
    }
}
