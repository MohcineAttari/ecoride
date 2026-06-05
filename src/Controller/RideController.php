<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\RideRepository;
use Throwable;

final class RideController
{
    public function index(): void
    {
        $filters = [
            'depart' => trim((string) ($_GET['depart'] ?? '')),
            'arrivee' => trim((string) ($_GET['arrivee'] ?? '')),
            'date' => trim((string) ($_GET['date'] ?? '')),
            'ecologique' => isset($_GET['ecologique']),
            'prix_max' => $this->optionalInt($_GET['prix_max'] ?? null),
            'duree_max' => $this->optionalInt($_GET['duree_max'] ?? null),
            'note_min' => $this->optionalFloat($_GET['note_min'] ?? null),
        ];

        $repository = new RideRepository();
        $rides = $repository->search($filters);
        $closestDate = $rides === [] ? $repository->findClosestDate($filters) : null;

        view('rides/index', [
            'title' => 'Covoiturages',
            'filters' => $filters,
            'rides' => $rides,
            'closestDate' => $closestDate,
        ]);
    }

    public function show(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $repository = new RideRepository();
        $ride = $repository->find($id);

        if ($ride === null) {
            http_response_code(404);
            view('errors/404', [
                'title' => 'Covoiturage introuvable',
            ]);
            return;
        }

        view('rides/show', [
            'title' => 'Detail du covoiturage',
            'ride' => $ride,
        ]);
    }

    public function confirmParticipation(): void
    {
        if (!is_authenticated()) {
            redirect('/connexion');
        }

        $ride = $this->findRideFromRequest();
        if ($ride === null) {
            return;
        }

        view('rides/confirm', [
            'title' => 'Confirmer la participation',
            'ride' => $ride,
            'error' => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_error']);
    }

    public function storeParticipation(): void
    {
        if (!is_authenticated()) {
            redirect('/connexion');
        }

        $ride = $this->findRideFromRequest();
        if ($ride === null) {
            return;
        }

        $confirmed = ($_POST['confirmation_credits'] ?? '') === '1'
            && ($_POST['confirmation_conditions'] ?? '') === '1';

        if (!$confirmed) {
            $_SESSION['flash_error'] = 'Veuillez cocher les deux confirmations pour participer.';
            redirect('/covoiturages/participer?id=' . $ride['id']);
        }

        if ($ride['available_seats'] < 1) {
            $_SESSION['flash_error'] = 'Ce covoiturage ne dispose plus de place.';
            redirect('/covoiturages/detail?id=' . $ride['id']);
        }

        if ((int) current_user()['credits'] < $ride['price']) {
            $_SESSION['flash_error'] = 'Votre solde de credits est insuffisant.';
            redirect('/covoiturages/participer?id=' . $ride['id']);
        }

        $repository = new RideRepository();
        try {
            $reservation = $repository->reserveSeat((int) $ride['id'], (int) current_user()['id']);

            if (!$reservation['success']) {
                $_SESSION['flash_error'] = $reservation['message'];
                redirect('/covoiturages/participer?id=' . $ride['id']);
            }

            $_SESSION['user']['credits'] = $reservation['credits_restants'];
            $_SESSION['reservations'][] = [
                'id' => $reservation['reservation_id'],
                'ride_id' => $ride['id'],
                'ride_label' => $ride['departure_city'] . ' vers ' . $ride['arrival_city'],
                'credits_payes' => $reservation['credits_payes'],
                'reserved_at' => date('Y-m-d H:i:s'),
                'statut' => 'confirmee',
            ];
            $_SESSION['flash_success'] = $reservation['message'];
        } catch (Throwable) {
            $_SESSION['user']['credits'] -= $ride['price'];
            $_SESSION['reservations'][] = [
                'ride_id' => $ride['id'],
                'ride_label' => $ride['departure_city'] . ' vers ' . $ride['arrival_city'],
                'credits_payes' => $ride['price'],
                'reserved_at' => date('Y-m-d H:i:s'),
                'statut' => 'confirmee',
            ];
            $_SESSION['flash_success'] = 'Votre participation est confirmee. ' . $ride['price'] . ' credits ont ete utilises.';
        }

        redirect('/covoiturages/detail?id=' . $ride['id']);
    }

    private function findRideFromRequest(): ?array
    {
        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
        $repository = new RideRepository();
        $ride = $repository->find($id);

        if ($ride === null) {
            http_response_code(404);
            view('errors/404', [
                'title' => 'Covoiturage introuvable',
            ]);
            return null;
        }

        return $ride;
    }

    private function optionalInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return max(0, (int) $value);
    }

    private function optionalFloat(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return max(0, (float) $value);
    }
}
