<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Database;
use App\Repository\RideRepository;
use App\Repository\VehicleRepository;
use Throwable;

final class UserController
{
    public function dashboard(): void
    {
        if (!is_authenticated()) {
            redirect('/connexion');
        }

        view('user/dashboard', [
            'title' => 'Mon espace',
            'user' => current_user(),
            'reservations' => $this->reservationsForCurrentUser(),
            'vehicles' => $this->vehiclesForCurrentUser(),
            'preferences' => $this->preferencesForCurrentUser(),
            'driverRides' => $this->driverRidesForCurrentUser(),
            'incidents' => $_SESSION['incidents'] ?? [],
            'success' => $_SESSION['flash_success'] ?? null,
            'error' => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);
    }

    private function reservationsForCurrentUser(): array
    {
        $sessionReservations = $_SESSION['reservations'] ?? [];

        try {
            $repository = new RideRepository();
            $sqlReservations = $repository->reservationsForPassenger((int) current_user()['id']);
        } catch (Throwable) {
            return $sessionReservations;
        }

        $knownReservationIds = [];
        foreach ($sqlReservations as $reservation) {
            $knownReservationIds[] = (int) $reservation['id'];
        }

        foreach ($sessionReservations as $reservation) {
            if (isset($reservation['id']) && in_array((int) $reservation['id'], $knownReservationIds, true)) {
                continue;
            }

            $sqlReservations[] = $reservation;
        }

        return $sqlReservations;
    }

    private function vehiclesForCurrentUser(): array
    {
        $sessionVehicles = $_SESSION['vehicles'] ?? [];

        try {
            $repository = new VehicleRepository(Database::connection());
            $sqlVehicles = $repository->vehiclesForUser((int) current_user()['id']);
        } catch (Throwable) {
            return $sessionVehicles;
        }

        $knownVehicleIds = [];
        foreach ($sqlVehicles as $vehicle) {
            $knownVehicleIds[] = (int) $vehicle['id'];
        }

        foreach ($sessionVehicles as $vehicle) {
            if (isset($vehicle['id']) && in_array((int) $vehicle['id'], $knownVehicleIds, true)) {
                continue;
            }

            $sqlVehicles[] = $vehicle;
        }

        return $sqlVehicles;
    }

    private function preferencesForCurrentUser(): array
    {
        $sessionPreferences = $_SESSION['driver_preferences'] ?? [];

        try {
            $repository = new VehicleRepository(Database::connection());
            $sqlPreferences = $repository->preferencesForUser((int) current_user()['id']);
        } catch (Throwable) {
            return $sessionPreferences;
        }

        return array_values(array_unique(array_merge($sqlPreferences, $sessionPreferences)));
    }

    private function driverRidesForCurrentUser(): array
    {
        $sessionRides = $_SESSION['driver_rides'] ?? [];

        try {
            $repository = new RideRepository();
            $sqlRides = $repository->driverRidesForDriver((int) current_user()['id']);
        } catch (Throwable) {
            return $sessionRides;
        }

        $knownRideIds = [];
        foreach ($sqlRides as $ride) {
            $knownRideIds[] = (int) $ride['id'];
        }

        foreach ($sessionRides as $ride) {
            if (isset($ride['id']) && in_array((int) $ride['id'], $knownRideIds, true)) {
                continue;
            }

            $sqlRides[] = $ride;
        }

        return $sqlRides;
    }

    public function updateProfile(): void
    {
        if (!is_authenticated()) {
            redirect('/connexion');
        }

        $profile = (string) ($_POST['profile'] ?? '');
        $allowedProfiles = ['passager', 'chauffeur', 'passager_chauffeur'];

        if (!in_array($profile, $allowedProfiles, true)) {
            $_SESSION['flash_error'] = 'Veuillez choisir un profil valide.';
            redirect('/mon-espace');
        }

        $_SESSION['user']['profile'] = $profile;
        $_SESSION['user']['roles'] = ['ROLE_USER'];

        if ($profile === 'passager' || $profile === 'passager_chauffeur') {
            $_SESSION['user']['roles'][] = 'ROLE_PASSAGER';
        }

        if ($profile === 'chauffeur' || $profile === 'passager_chauffeur') {
            $_SESSION['user']['roles'][] = 'ROLE_CHAUFFEUR';
        }

        $_SESSION['flash_success'] = 'Votre profil a ete mis a jour.';
        redirect('/mon-espace');
    }

    public function storeVehicle(): void
    {
        if (!is_authenticated()) {
            redirect('/connexion');
        }

        $required = ['immatriculation', 'date_premiere_immatriculation', 'marque', 'modele', 'couleur', 'nb_places'];
        foreach ($required as $field) {
            if (trim((string) ($_POST[$field] ?? '')) === '') {
                $_SESSION['flash_error'] = 'Veuillez remplir toutes les informations obligatoires du vehicule.';
                redirect('/mon-espace');
            }
        }

        $seats = max(1, (int) $_POST['nb_places']);

        $vehicle = [
            'immatriculation' => strtoupper(trim((string) $_POST['immatriculation'])),
            'date_premiere_immatriculation' => trim((string) $_POST['date_premiere_immatriculation']),
            'marque' => trim((string) $_POST['marque']),
            'modele' => trim((string) $_POST['modele']),
            'couleur' => trim((string) $_POST['couleur']),
            'energie' => trim((string) ($_POST['energie'] ?? 'essence')),
            'nb_places' => $seats,
        ];

        $preferences = [];
        if (isset($_POST['fumeur'])) {
            $preferences[] = 'Fumeur accepte';
        } else {
            $preferences[] = 'Non fumeur';
        }

        if (isset($_POST['animaux'])) {
            $preferences[] = 'Animaux acceptes';
        } else {
            $preferences[] = 'Pas d animal';
        }

        $customPreference = trim((string) ($_POST['preference_custom'] ?? ''));
        if ($customPreference !== '') {
            $preferences[] = $customPreference;
        }

        try {
            $repository = new VehicleRepository(Database::connection());
            $vehicleId = $repository->createVehicle((int) current_user()['id'], $vehicle, $preferences);
            $vehicle['id'] = $vehicleId;
            $vehicle['source'] = 'sql';
        } catch (Throwable) {
            // Le prototype garde un mode session si la base locale est indisponible.
        }

        $_SESSION['vehicles'][] = $vehicle;

        $_SESSION['driver_preferences'] = array_values(array_unique(array_merge(
            $_SESSION['driver_preferences'] ?? [],
            $preferences
        )));

        $_SESSION['flash_success'] = 'Vehicule et preferences chauffeur enregistres.';
        redirect('/mon-espace');
    }

    public function storeRide(): void
    {
        if (!is_authenticated()) {
            redirect('/connexion');
        }

        if (!in_array('ROLE_CHAUFFEUR', current_user()['roles'], true)) {
            $_SESSION['flash_error'] = 'Vous devez etre chauffeur pour saisir un voyage.';
            redirect('/mon-espace');
        }

        $required = ['ville_depart', 'ville_arrivee', 'date_depart', 'date_arrivee', 'prix_personne'];
        foreach ($required as $field) {
            if (trim((string) ($_POST[$field] ?? '')) === '') {
                $_SESSION['flash_error'] = 'Veuillez remplir toutes les informations obligatoires du voyage.';
                redirect('/mon-espace');
            }
        }

        $vehicles = $this->vehiclesForCurrentUser();
        $vehicleChoice = (string) ($_POST['vehicle_choice'] ?? 'existing');
        $vehicle = null;

        if ($vehicleChoice === 'new') {
            $vehicle = $this->buildVehicleFromRideForm();
            if ($vehicle === null) {
                redirect('/mon-espace');
            }

            try {
                $vehicleRepository = new VehicleRepository(Database::connection());
                $vehicleId = $vehicleRepository->createVehicle((int) current_user()['id'], $vehicle, []);
                $vehicle['id'] = $vehicleId;
                $vehicle['source'] = 'sql';
            } catch (Throwable) {
                // Le prototype garde un mode session si la base locale est indisponible.
            }

            $_SESSION['vehicles'][] = $vehicle;
        } else {
            $vehicleIndex = (int) ($_POST['vehicle_index'] ?? -1);
            if (!isset($vehicles[$vehicleIndex])) {
                $_SESSION['flash_error'] = 'Veuillez selectionner un vehicule existant ou proposer un nouveau vehicule.';
                redirect('/mon-espace');
            }
            $vehicle = $vehicles[$vehicleIndex];
        }

        $price = max(1, (int) $_POST['prix_personne']);
        $seats = max(1, min((int) $vehicle['nb_places'], (int) ($_POST['nb_places'] ?? $vehicle['nb_places'])));

        $driverRide = [
            'id' => random_int(1000, 9999),
            'ville_depart' => trim((string) $_POST['ville_depart']),
            'lieu_depart' => trim((string) ($_POST['lieu_depart'] ?? '')),
            'ville_arrivee' => trim((string) $_POST['ville_arrivee']),
            'lieu_arrivee' => trim((string) ($_POST['lieu_arrivee'] ?? '')),
            'date_depart' => $this->normalizeDateTime((string) $_POST['date_depart']),
            'date_arrivee' => $this->normalizeDateTime((string) $_POST['date_arrivee']),
            'prix_personne' => $price,
            'commission_plateforme' => 2,
            'gain_chauffeur_par_passager' => max(0, $price - 2),
            'nb_places' => $seats,
            'vehicule' => $vehicle,
            'statut' => 'ouvert',
        ];

        try {
            if (isset($vehicle['id'])) {
                $rideRepository = new RideRepository();
                $driverRide['id'] = $rideRepository->createDriverRide(
                    (int) current_user()['id'],
                    (int) $vehicle['id'],
                    $driverRide
                );
                $driverRide['source'] = 'sql';
            }
        } catch (Throwable) {
            // Le prototype garde un mode session si la base locale est indisponible.
        }

        $_SESSION['driver_rides'][] = $driverRide;

        $_SESSION['flash_success'] = 'Voyage enregistre. La plateforme prendra 2 credits sur le prix par passager.';
        redirect('/mon-espace');
    }

    public function cancelReservation(): void
    {
        if (!is_authenticated()) {
            redirect('/connexion');
        }

        $reservationId = (int) ($_POST['reservation_id'] ?? 0);
        if ($reservationId > 0) {
            try {
                $repository = new RideRepository();
                $cancellation = $repository->cancelReservation($reservationId, (int) current_user()['id']);

                if (!$cancellation['success']) {
                    $_SESSION['flash_error'] = $cancellation['message'];
                    redirect('/mon-espace');
                }

                $_SESSION['user']['credits'] = $cancellation['credits_restants'];
                foreach ($_SESSION['reservations'] ?? [] as $index => $reservation) {
                    if (isset($reservation['id']) && (int) $reservation['id'] === $reservationId) {
                        $_SESSION['reservations'][$index]['statut'] = 'annulee';
                        $_SESSION['reservations'][$index]['cancelled_at'] = date('Y-m-d H:i:s');
                    }
                }

                $_SESSION['flash_success'] = $cancellation['message'];
                redirect('/mon-espace');
            } catch (Throwable) {
                // Si la base est indisponible, le prototype poursuit avec les donnees de session.
            }
        }

        $index = (int) ($_POST['index'] ?? -1);
        if (!isset($_SESSION['reservations'][$index])) {
            $_SESSION['flash_error'] = 'Reservation introuvable.';
            redirect('/mon-espace');
        }

        if (($_SESSION['reservations'][$index]['statut'] ?? 'confirmee') === 'annulee') {
            $_SESSION['flash_error'] = 'Cette reservation est deja annulee.';
            redirect('/mon-espace');
        }

        $refund = (int) $_SESSION['reservations'][$index]['credits_payes'];
        $_SESSION['reservations'][$index]['statut'] = 'annulee';
        $_SESSION['reservations'][$index]['cancelled_at'] = date('Y-m-d H:i:s');
        $_SESSION['user']['credits'] += $refund;

        $_SESSION['flash_success'] = 'Reservation annulee. ' . $refund . ' credits ont ete rembourses.';
        redirect('/mon-espace');
    }

    public function cancelDriverRide(): void
    {
        if (!is_authenticated()) {
            redirect('/connexion');
        }

        $rideId = (int) ($_POST['ride_id'] ?? 0);
        if ($rideId > 0) {
            try {
                $repository = new RideRepository();
                $cancellation = $repository->cancelDriverRide($rideId, (int) current_user()['id']);

                if (!$cancellation['success']) {
                    $_SESSION['flash_error'] = $cancellation['message'];
                    redirect('/mon-espace');
                }

                $this->updateSessionDriverRideStatus($rideId, 'annule', 'Mail envoye aux participants si le trajet avait des reservations.');
                $_SESSION['flash_success'] = $cancellation['message'];
                redirect('/mon-espace');
            } catch (Throwable) {
                // Si la base est indisponible, le prototype poursuit avec les donnees de session.
            }
        }

        $index = (int) ($_POST['index'] ?? -1);
        if (!isset($_SESSION['driver_rides'][$index])) {
            $_SESSION['flash_error'] = 'Voyage introuvable.';
            redirect('/mon-espace');
        }

        if ($_SESSION['driver_rides'][$index]['statut'] === 'annule') {
            $_SESSION['flash_error'] = 'Ce voyage est deja annule.';
            redirect('/mon-espace');
        }

        $_SESSION['driver_rides'][$index]['statut'] = 'annule';
        $_SESSION['driver_rides'][$index]['cancelled_at'] = date('Y-m-d H:i:s');
        $_SESSION['driver_rides'][$index]['notification'] = 'Mail envoye aux participants si le trajet avait des reservations.';

        $_SESSION['flash_success'] = 'Voyage chauffeur annule. Les participants seront notifies par email.';
        redirect('/mon-espace');
    }

    public function startDriverRide(): void
    {
        if (!is_authenticated()) {
            redirect('/connexion');
        }

        $rideId = (int) ($_POST['ride_id'] ?? 0);
        if ($rideId > 0) {
            try {
                $repository = new RideRepository();
                $update = $repository->updateDriverRideStatus($rideId, (int) current_user()['id'], 'ouvert', 'demarre');

                if (!$update['success']) {
                    $_SESSION['flash_error'] = $update['message'];
                    redirect('/mon-espace');
                }

                $this->updateSessionDriverRideStatus($rideId, 'demarre');
                $_SESSION['flash_success'] = $update['message'];
                redirect('/mon-espace');
            } catch (Throwable) {
                // Si la base est indisponible, le prototype poursuit avec les donnees de session.
            }
        }

        $index = (int) ($_POST['index'] ?? -1);
        if (!isset($_SESSION['driver_rides'][$index])) {
            $_SESSION['flash_error'] = 'Voyage introuvable.';
            redirect('/mon-espace');
        }

        if ($_SESSION['driver_rides'][$index]['statut'] !== 'ouvert') {
            $_SESSION['flash_error'] = 'Seul un voyage ouvert peut etre demarre.';
            redirect('/mon-espace');
        }

        $_SESSION['driver_rides'][$index]['statut'] = 'demarre';
        $_SESSION['driver_rides'][$index]['started_at'] = date('Y-m-d H:i:s');
        $_SESSION['flash_success'] = 'Le covoiturage a ete demarre.';

        redirect('/mon-espace');
    }

    public function finishDriverRide(): void
    {
        if (!is_authenticated()) {
            redirect('/connexion');
        }

        $rideId = (int) ($_POST['ride_id'] ?? 0);
        if ($rideId > 0) {
            try {
                $repository = new RideRepository();
                $update = $repository->updateDriverRideStatus($rideId, (int) current_user()['id'], 'demarre', 'termine');

                if (!$update['success']) {
                    $_SESSION['flash_error'] = $update['message'];
                    redirect('/mon-espace');
                }

                $this->updateSessionDriverRideStatus(
                    $rideId,
                    'termine',
                    'Mail envoye aux participants pour confirmer le bon deroulement du trajet.'
                );
                $_SESSION['flash_success'] = $update['message'];
                redirect('/mon-espace');
            } catch (Throwable) {
                // Si la base est indisponible, le prototype poursuit avec les donnees de session.
            }
        }

        $index = (int) ($_POST['index'] ?? -1);
        if (!isset($_SESSION['driver_rides'][$index])) {
            $_SESSION['flash_error'] = 'Voyage introuvable.';
            redirect('/mon-espace');
        }

        if ($_SESSION['driver_rides'][$index]['statut'] !== 'demarre') {
            $_SESSION['flash_error'] = 'Seul un voyage demarre peut etre declare arrive a destination.';
            redirect('/mon-espace');
        }

        $_SESSION['driver_rides'][$index]['statut'] = 'termine';
        $_SESSION['driver_rides'][$index]['finished_at'] = date('Y-m-d H:i:s');
        $_SESSION['driver_rides'][$index]['notification'] = 'Mail envoye aux participants pour confirmer le bon deroulement du trajet.';
        $_SESSION['flash_success'] = 'Le covoiturage est arrive a destination. Les participants seront invites a valider le trajet.';

        redirect('/mon-espace');
    }

    public function validateReservation(): void
    {
        if (!is_authenticated()) {
            redirect('/connexion');
        }

        $reservationId = (int) ($_POST['reservation_id'] ?? 0);
        if ($reservationId > 0) {
            try {
                $repository = new RideRepository();
                $validation = $repository->validatePassengerReservation(
                    $reservationId,
                    (int) current_user()['id'],
                    max(1, min(5, (int) ($_POST['note'] ?? 5))),
                    trim((string) ($_POST['commentaire'] ?? 'Trajet valide par le passager.'))
                );

                if (!$validation['success']) {
                    $_SESSION['flash_error'] = $validation['message'];
                    redirect('/mon-espace');
                }

                $this->updateSessionReservationStatus($reservationId, 'validee');
                $_SESSION['flash_success'] = $validation['message'];
                redirect('/mon-espace');
            } catch (Throwable) {
                // Si la base est indisponible, le prototype poursuit avec les donnees de session.
            }
        }

        $index = (int) ($_POST['index'] ?? -1);
        if (!isset($_SESSION['reservations'][$index])) {
            $_SESSION['flash_error'] = 'Reservation introuvable.';
            redirect('/mon-espace');
        }

        if (($_SESSION['reservations'][$index]['statut'] ?? 'confirmee') === 'annulee') {
            $_SESSION['flash_error'] = 'Une reservation annulee ne peut pas etre validee.';
            redirect('/mon-espace');
        }

        $_SESSION['reservations'][$index]['statut'] = 'validee';
        $_SESSION['reservations'][$index]['validated_at'] = date('Y-m-d H:i:s');
        $_SESSION['pending_reviews'][] = [
            'reservation_index' => $index,
            'ride_label' => $_SESSION['reservations'][$index]['ride_label'],
            'passager_pseudo' => current_user()['pseudo'],
            'chauffeur_pseudo' => 'Chauffeur du trajet',
            'note' => max(1, min(5, (int) ($_POST['note'] ?? 5))),
            'commentaire' => trim((string) ($_POST['commentaire'] ?? 'Trajet valide par le passager.')),
            'statut' => 'en_attente',
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $_SESSION['flash_success'] = 'Trajet valide. Les credits du chauffeur peuvent etre mis a jour.';

        redirect('/mon-espace');
    }

    public function reportReservation(): void
    {
        if (!is_authenticated()) {
            redirect('/connexion');
        }

        $reservationId = (int) ($_POST['reservation_id'] ?? 0);
        $index = (int) ($_POST['index'] ?? -1);
        $comment = trim((string) ($_POST['commentaire'] ?? ''));

        if ($reservationId > 0) {
            if ($comment === '') {
                $_SESSION['flash_error'] = 'Veuillez indiquer un commentaire pour signaler le probleme.';
                redirect('/mon-espace');
            }

            try {
                $repository = new RideRepository();
                $report = $repository->reportPassengerReservation($reservationId, (int) current_user()['id'], $comment);

                if (!$report['success']) {
                    $_SESSION['flash_error'] = $report['message'];
                    redirect('/mon-espace');
                }

                $this->updateSessionReservationStatus($reservationId, 'probleme');
                $_SESSION['flash_success'] = $report['message'];
                redirect('/mon-espace');
            } catch (Throwable) {
                // Si la base est indisponible, le prototype poursuit avec les donnees de session.
            }
        }

        if (!isset($_SESSION['reservations'][$index])) {
            $_SESSION['flash_error'] = 'Reservation introuvable.';
            redirect('/mon-espace');
        }

        if ($comment === '') {
            $_SESSION['flash_error'] = 'Veuillez indiquer un commentaire pour signaler le probleme.';
            redirect('/mon-espace');
        }

        $_SESSION['reservations'][$index]['statut'] = 'probleme';
        $_SESSION['reservations'][$index]['reported_at'] = date('Y-m-d H:i:s');
        $_SESSION['incidents'][] = [
            'reservation_index' => $index,
            'ride_label' => $_SESSION['reservations'][$index]['ride_label'],
            'passager_pseudo' => current_user()['pseudo'],
            'passager_email' => current_user()['email'],
            'commentaire' => $comment,
            'statut' => 'ouvert',
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $_SESSION['flash_success'] = 'Probleme signale. Un employe devra contacter le chauffeur avant la mise a jour des credits.';

        redirect('/mon-espace');
    }

    private function buildVehicleFromRideForm(): ?array
    {
        $required = [
            'new_immatriculation',
            'new_date_premiere_immatriculation',
            'new_marque',
            'new_modele',
            'new_couleur',
            'new_nb_places',
        ];

        foreach ($required as $field) {
            if (trim((string) ($_POST[$field] ?? '')) === '') {
                $_SESSION['flash_error'] = 'Veuillez remplir le nouveau vehicule ou choisir un vehicule existant.';
                return null;
            }
        }

        return [
            'immatriculation' => strtoupper(trim((string) $_POST['new_immatriculation'])),
            'date_premiere_immatriculation' => trim((string) $_POST['new_date_premiere_immatriculation']),
            'marque' => trim((string) $_POST['new_marque']),
            'modele' => trim((string) $_POST['new_modele']),
            'couleur' => trim((string) $_POST['new_couleur']),
            'energie' => trim((string) ($_POST['new_energie'] ?? 'essence')),
            'nb_places' => max(1, (int) $_POST['new_nb_places']),
        ];
    }

    private function normalizeDateTime(string $value): string
    {
        return str_replace('T', ' ', trim($value));
    }

    private function updateSessionDriverRideStatus(int $rideId, string $status, ?string $notification = null): void
    {
        foreach ($_SESSION['driver_rides'] ?? [] as $index => $ride) {
            if (!isset($ride['id']) || (int) $ride['id'] !== $rideId) {
                continue;
            }

            $_SESSION['driver_rides'][$index]['statut'] = $status;

            if ($status === 'annule') {
                $_SESSION['driver_rides'][$index]['cancelled_at'] = date('Y-m-d H:i:s');
            }

            if ($status === 'demarre') {
                $_SESSION['driver_rides'][$index]['started_at'] = date('Y-m-d H:i:s');
            }

            if ($status === 'termine') {
                $_SESSION['driver_rides'][$index]['finished_at'] = date('Y-m-d H:i:s');
            }

            if ($notification !== null) {
                $_SESSION['driver_rides'][$index]['notification'] = $notification;
            }
        }
    }

    private function updateSessionReservationStatus(int $reservationId, string $status): void
    {
        foreach ($_SESSION['reservations'] ?? [] as $index => $reservation) {
            if (!isset($reservation['id']) || (int) $reservation['id'] !== $reservationId) {
                continue;
            }

            $_SESSION['reservations'][$index]['statut'] = $status;

            if ($status === 'validee') {
                $_SESSION['reservations'][$index]['validated_at'] = date('Y-m-d H:i:s');
            }

            if ($status === 'probleme') {
                $_SESSION['reservations'][$index]['reported_at'] = date('Y-m-d H:i:s');
            }
        }
    }
}
