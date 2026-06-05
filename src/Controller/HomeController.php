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
}
