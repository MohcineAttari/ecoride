<?php

declare(strict_types=1);

namespace App\Repository;

use App\Core\Database;
use PDO;
use Throwable;

final class RideRepository
{
    /**
     * Donnees temporaires pour travailler le parcours avant le branchement SQL.
     */
    private array $rides = [
        [
            'id' => 1,
            'driver_pseudo' => 'Clara',
            'driver_rating' => 4.8,
            'driver_photo' => '/assets/images/default-driver.svg',
            'departure_city' => 'Lyon',
            'departure_place' => 'Gare Part-Dieu',
            'arrival_city' => 'Marseille',
            'arrival_place' => 'Gare Saint-Charles',
            'departure_at' => '2026-06-05 08:30',
            'arrival_at' => '2026-06-05 12:00',
            'available_seats' => 3,
            'price' => 12,
            'vehicle_brand' => 'Tesla',
            'vehicle_model' => 'Model 3',
            'energy' => 'electrique',
            'is_ecological' => true,
            'preferences' => ['Animaux acceptes', 'Discussion acceptee', 'Pause possible sur demande'],
            'reviews' => [
                ['author' => 'Leo', 'rating' => 5, 'comment' => 'Trajet agreable, conduite souple et horaires respectes.'],
                ['author' => 'Mina', 'rating' => 4, 'comment' => 'Voiture propre et conductrice ponctuelle.'],
            ],
        ],
        [
            'id' => 2,
            'driver_pseudo' => 'Nadia',
            'driver_rating' => 4.5,
            'driver_photo' => '/assets/images/default-driver.svg',
            'departure_city' => 'Paris',
            'departure_place' => 'Porte d Orleans',
            'arrival_city' => 'Lille',
            'arrival_place' => 'Lille Flandres',
            'departure_at' => '2026-06-06 09:00',
            'arrival_at' => '2026-06-06 11:40',
            'available_seats' => 2,
            'price' => 9,
            'vehicle_brand' => 'Toyota',
            'vehicle_model' => 'Corolla',
            'energy' => 'hybride',
            'is_ecological' => false,
            'preferences' => ['Non fumeur', 'Musique douce', 'Bagage cabine uniquement'],
            'reviews' => [
                ['author' => 'Samir', 'rating' => 5, 'comment' => 'Tres bonne communication avant le depart.'],
            ],
        ],
        [
            'id' => 3,
            'driver_pseudo' => 'Hugo',
            'driver_rating' => 4.1,
            'driver_photo' => '/assets/images/default-driver.svg',
            'departure_city' => 'Lyon',
            'departure_place' => 'Perrache',
            'arrival_city' => 'Grenoble',
            'arrival_place' => 'Gare de Grenoble',
            'departure_at' => '2026-06-05 17:45',
            'arrival_at' => '2026-06-05 19:10',
            'available_seats' => 1,
            'price' => 6,
            'vehicle_brand' => 'Renault',
            'vehicle_model' => 'Zoe',
            'energy' => 'electrique',
            'is_ecological' => true,
            'preferences' => ['Non fumeur', 'Animaux acceptes'],
            'reviews' => [],
        ],
    ];

    public function find(int $id): ?array
    {
        try {
            $ride = $this->findFromDatabase($id);

            if ($ride !== null) {
                return $ride;
            }
        } catch (Throwable) {
            return $this->findFromFallback($id);
        }

        return null;
    }

    private function findFromFallback(int $id): ?array
    {
        foreach ($this->rides as $ride) {
            if ($ride['id'] === $id) {
                return $ride;
            }
        }

        return null;
    }

    public function search(array $filters): array
    {
        try {
            return $this->searchFromDatabase($filters);
        } catch (Throwable) {
            return $this->searchFromFallback($filters);
        }
    }

    private function searchFromFallback(array $filters): array
    {
        $results = array_filter($this->rides, function (array $ride) use ($filters): bool {
            if ($ride['available_seats'] < 1) {
                return false;
            }

            if ($filters['depart'] !== '' && !$this->matchesCity($ride['departure_city'], $filters['depart'])) {
                return false;
            }

            if ($filters['arrivee'] !== '' && !$this->matchesCity($ride['arrival_city'], $filters['arrivee'])) {
                return false;
            }

            if ($filters['date'] !== '' && substr($ride['departure_at'], 0, 10) !== $filters['date']) {
                return false;
            }

            if ($filters['ecologique'] && !$ride['is_ecological']) {
                return false;
            }

            if ($filters['prix_max'] !== null && $ride['price'] > $filters['prix_max']) {
                return false;
            }

            if ($filters['duree_max'] !== null && $this->durationInMinutes($ride) > $filters['duree_max']) {
                return false;
            }

            if ($filters['note_min'] !== null && $ride['driver_rating'] < $filters['note_min']) {
                return false;
            }

            return true;
        });

        return array_values($results);
    }

    public function findClosestDate(array $filters): ?string
    {
        try {
            $closestDate = $this->findClosestDateFromDatabase($filters);

            if ($closestDate !== null) {
                return $closestDate;
            }
        } catch (Throwable) {
            return $this->findClosestDateFromFallback($filters);
        }

        return null;
    }

    public function reserveSeat(int $rideId, int $passengerId): array
    {
        $connection = $this->connection();
        $connection->beginTransaction();

        try {
            $rideStatement = $connection->prepare(
                'SELECT c.id, c.chauffeur_id, c.prix_personne, c.nb_places_restantes, c.statut, u.credits
                 FROM covoiturage c
                 INNER JOIN utilisateur u ON u.id = :passenger_id
                 WHERE c.id = :ride_id
                 FOR UPDATE'
            );
            $rideStatement->execute([
                'ride_id' => $rideId,
                'passenger_id' => $passengerId,
            ]);
            $ride = $rideStatement->fetch();

            if ($ride === false) {
                $connection->rollBack();
                return ['success' => false, 'message' => 'Covoiturage introuvable.'];
            }

            if ((int) $ride['chauffeur_id'] === $passengerId) {
                $connection->rollBack();
                return ['success' => false, 'message' => 'Vous ne pouvez pas participer a votre propre covoiturage.'];
            }

            if ((string) $ride['statut'] !== 'ouvert' || (int) $ride['nb_places_restantes'] < 1) {
                $connection->rollBack();
                return ['success' => false, 'message' => 'Ce covoiturage ne dispose plus de place.'];
            }

            $duplicateStatement = $connection->prepare(
                'SELECT COUNT(*)
                 FROM reservation
                 WHERE covoiturage_id = :ride_id
                   AND passager_id = :passenger_id
                   AND statut <> "annulee"'
            );
            $duplicateStatement->execute([
                'ride_id' => $rideId,
                'passenger_id' => $passengerId,
            ]);

            if ((int) $duplicateStatement->fetchColumn() > 0) {
                $connection->rollBack();
                return ['success' => false, 'message' => 'Vous participez deja a ce covoiturage.'];
            }

            $price = (int) $ride['prix_personne'];
            if ((int) $ride['credits'] < $price) {
                $connection->rollBack();
                return ['success' => false, 'message' => 'Votre solde de credits est insuffisant.'];
            }

            $connection->prepare(
                'UPDATE utilisateur
                 SET credits = credits - :price, updated_at = NOW()
                 WHERE id = :passenger_id'
            )->execute([
                'price' => $price,
                'passenger_id' => $passengerId,
            ]);

            $connection->prepare(
                'UPDATE covoiturage
                 SET nb_places_restantes = nb_places_restantes - 1, updated_at = NOW()
                 WHERE id = :ride_id'
            )->execute(['ride_id' => $rideId]);

            $reservationStatement = $connection->prepare(
                'INSERT INTO reservation (covoiturage_id, passager_id, credits_payes, statut)
                 VALUES (:ride_id, :passenger_id, :credits_payes, "confirmee")'
            );
            $reservationStatement->execute([
                'ride_id' => $rideId,
                'passenger_id' => $passengerId,
                'credits_payes' => $price,
            ]);
            $reservationId = (int) $connection->lastInsertId();

            $connection->prepare(
                'INSERT INTO transaction_credit
                    (utilisateur_source_id, covoiturage_id, reservation_id, montant, type)
                 VALUES
                    (:passenger_id, :ride_id, :reservation_id, :amount, "participation")'
            )->execute([
                'passenger_id' => $passengerId,
                'ride_id' => $rideId,
                'reservation_id' => $reservationId,
                'amount' => -$price,
            ]);

            $connection->commit();

            return [
                'success' => true,
                'message' => 'Votre participation est confirmee. ' . $price . ' credits ont ete utilises.',
                'reservation_id' => $reservationId,
                'credits_restants' => (int) $ride['credits'] - $price,
                'credits_payes' => $price,
            ];
        } catch (Throwable $exception) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            throw $exception;
        }
    }

    public function reservationsForPassenger(int $passengerId): array
    {
        $statement = $this->connection()->prepare(
            'SELECT
                r.id,
                r.covoiturage_id,
                r.credits_payes,
                r.statut,
                r.created_at,
                c.ville_depart,
                c.ville_arrivee,
                c.date_depart,
                c.date_arrivee,
                c.statut AS covoiturage_statut,
                chauffeur.pseudo AS chauffeur_pseudo
             FROM reservation r
             INNER JOIN covoiturage c ON c.id = r.covoiturage_id
             INNER JOIN utilisateur chauffeur ON chauffeur.id = c.chauffeur_id
             WHERE r.passager_id = :passenger_id
             ORDER BY r.created_at DESC'
        );
        $statement->execute(['passenger_id' => $passengerId]);

        return array_map(static fn (array $reservation): array => [
            'id' => (int) $reservation['id'],
            'ride_id' => (int) $reservation['covoiturage_id'],
            'ride_label' => $reservation['ville_depart'] . ' vers ' . $reservation['ville_arrivee'],
            'credits_payes' => (int) $reservation['credits_payes'],
            'reserved_at' => (string) $reservation['created_at'],
            'statut' => (string) $reservation['statut'],
            'date_depart' => (string) $reservation['date_depart'],
            'date_arrivee' => (string) $reservation['date_arrivee'],
            'covoiturage_statut' => (string) $reservation['covoiturage_statut'],
            'chauffeur_pseudo' => (string) $reservation['chauffeur_pseudo'],
            'source' => 'sql',
        ], $statement->fetchAll());
    }

    public function cancelReservation(int $reservationId, int $passengerId): array
    {
        $connection = $this->connection();
        $connection->beginTransaction();

        try {
            $statement = $connection->prepare(
                'SELECT id, covoiturage_id, passager_id, credits_payes, statut
                 FROM reservation
                 WHERE id = :reservation_id
                   AND passager_id = :passenger_id
                 FOR UPDATE'
            );
            $statement->execute([
                'reservation_id' => $reservationId,
                'passenger_id' => $passengerId,
            ]);
            $reservation = $statement->fetch();

            if ($reservation === false) {
                $connection->rollBack();
                return ['success' => false, 'message' => 'Reservation introuvable.'];
            }

            if ((string) $reservation['statut'] === 'annulee') {
                $connection->rollBack();
                return ['success' => false, 'message' => 'Cette reservation est deja annulee.'];
            }

            $refund = (int) $reservation['credits_payes'];
            $rideId = (int) $reservation['covoiturage_id'];

            $connection->prepare(
                'UPDATE reservation
                 SET statut = "annulee", updated_at = NOW()
                 WHERE id = :reservation_id'
            )->execute(['reservation_id' => $reservationId]);

            $connection->prepare(
                'UPDATE utilisateur
                 SET credits = credits + :refund, updated_at = NOW()
                 WHERE id = :passenger_id'
            )->execute([
                'refund' => $refund,
                'passenger_id' => $passengerId,
            ]);

            $connection->prepare(
                'UPDATE covoiturage
                 SET nb_places_restantes = nb_places_restantes + 1, updated_at = NOW()
                 WHERE id = :ride_id'
            )->execute(['ride_id' => $rideId]);

            $connection->prepare(
                'INSERT INTO transaction_credit
                    (utilisateur_cible_id, covoiturage_id, reservation_id, montant, type)
                 VALUES
                    (:passenger_id, :ride_id, :reservation_id, :amount, "annulation_reservation")'
            )->execute([
                'passenger_id' => $passengerId,
                'ride_id' => $rideId,
                'reservation_id' => $reservationId,
                'amount' => $refund,
            ]);

            $creditsStatement = $connection->prepare('SELECT credits FROM utilisateur WHERE id = :passenger_id');
            $creditsStatement->execute(['passenger_id' => $passengerId]);
            $credits = (int) $creditsStatement->fetchColumn();

            $connection->commit();

            return [
                'success' => true,
                'message' => 'Reservation annulee. ' . $refund . ' credits ont ete rembourses.',
                'credits_restants' => $credits,
                'credits_rembourses' => $refund,
            ];
        } catch (Throwable $exception) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            throw $exception;
        }
    }

    public function createDriverRide(int $driverId, int $vehicleId, array $ride): int
    {
        $statement = $this->connection()->prepare(
            'INSERT INTO covoiturage
                (chauffeur_id, vehicule_id, ville_depart, lieu_depart, ville_arrivee, lieu_arrivee,
                 date_depart, date_arrivee, prix_personne, nb_places_total, nb_places_restantes, statut)
             VALUES
                (:driver_id, :vehicle_id, :ville_depart, :lieu_depart, :ville_arrivee, :lieu_arrivee,
                 :date_depart, :date_arrivee, :prix_personne, :nb_places_total, :nb_places_restantes, "ouvert")'
        );
        $statement->execute([
            'driver_id' => $driverId,
            'vehicle_id' => $vehicleId,
            'ville_depart' => $ride['ville_depart'],
            'lieu_depart' => $ride['lieu_depart'],
            'ville_arrivee' => $ride['ville_arrivee'],
            'lieu_arrivee' => $ride['lieu_arrivee'],
            'date_depart' => $ride['date_depart'],
            'date_arrivee' => $ride['date_arrivee'],
            'prix_personne' => $ride['prix_personne'],
            'nb_places_total' => $ride['nb_places'],
            'nb_places_restantes' => $ride['nb_places'],
        ]);

        return (int) $this->connection()->lastInsertId();
    }

    public function driverRidesForDriver(int $driverId): array
    {
        $statement = $this->connection()->prepare(
            'SELECT
                c.id,
                c.ville_depart,
                c.lieu_depart,
                c.ville_arrivee,
                c.lieu_arrivee,
                c.date_depart,
                c.date_arrivee,
                c.prix_personne,
                c.nb_places_total,
                c.nb_places_restantes,
                c.statut,
                v.immatriculation,
                v.modele,
                v.couleur,
                v.nb_places AS vehicule_nb_places,
                m.libelle AS marque,
                e.libelle AS energie
             FROM covoiturage c
             INNER JOIN vehicule v ON v.id = c.vehicule_id
             INNER JOIN marque m ON m.id = v.marque_id
             INNER JOIN energie e ON e.id = v.energie_id
             WHERE c.chauffeur_id = :driver_id
             ORDER BY c.date_depart DESC'
        );
        $statement->execute(['driver_id' => $driverId]);

        return array_map(static fn (array $ride): array => [
            'id' => (int) $ride['id'],
            'ville_depart' => (string) $ride['ville_depart'],
            'lieu_depart' => (string) ($ride['lieu_depart'] ?? ''),
            'ville_arrivee' => (string) $ride['ville_arrivee'],
            'lieu_arrivee' => (string) ($ride['lieu_arrivee'] ?? ''),
            'date_depart' => (string) $ride['date_depart'],
            'date_arrivee' => (string) $ride['date_arrivee'],
            'prix_personne' => (int) $ride['prix_personne'],
            'commission_plateforme' => 2,
            'gain_chauffeur_par_passager' => max(0, (int) $ride['prix_personne'] - 2),
            'nb_places' => (int) $ride['nb_places_total'],
            'nb_places_restantes' => (int) $ride['nb_places_restantes'],
            'vehicule' => [
                'immatriculation' => (string) $ride['immatriculation'],
                'marque' => (string) $ride['marque'],
                'modele' => (string) $ride['modele'],
                'couleur' => (string) $ride['couleur'],
                'energie' => (string) $ride['energie'],
                'nb_places' => (int) $ride['vehicule_nb_places'],
            ],
            'statut' => (string) $ride['statut'],
            'source' => 'sql',
        ], $statement->fetchAll());
    }

    public function cancelDriverRide(int $rideId, int $driverId): array
    {
        $connection = $this->connection();
        $connection->beginTransaction();

        try {
            $rideStatement = $connection->prepare(
                'SELECT id, statut
                 FROM covoiturage
                 WHERE id = :ride_id
                   AND chauffeur_id = :driver_id
                 FOR UPDATE'
            );
            $rideStatement->execute([
                'ride_id' => $rideId,
                'driver_id' => $driverId,
            ]);
            $ride = $rideStatement->fetch();

            if ($ride === false) {
                $connection->rollBack();
                return ['success' => false, 'message' => 'Voyage introuvable.'];
            }

            if ((string) $ride['statut'] === 'annule') {
                $connection->rollBack();
                return ['success' => false, 'message' => 'Ce voyage est deja annule.'];
            }

            if ((string) $ride['statut'] === 'termine') {
                $connection->rollBack();
                return ['success' => false, 'message' => 'Un voyage termine ne peut pas etre annule.'];
            }

            $reservationsStatement = $connection->prepare(
                'SELECT id, passager_id, credits_payes
                 FROM reservation
                 WHERE covoiturage_id = :ride_id
                   AND statut <> "annulee"
                 FOR UPDATE'
            );
            $reservationsStatement->execute(['ride_id' => $rideId]);
            $reservations = $reservationsStatement->fetchAll();

            foreach ($reservations as $reservation) {
                $refund = (int) $reservation['credits_payes'];
                $passengerId = (int) $reservation['passager_id'];
                $reservationId = (int) $reservation['id'];

                $connection->prepare(
                    'UPDATE utilisateur
                     SET credits = credits + :refund, updated_at = NOW()
                     WHERE id = :passenger_id'
                )->execute([
                    'refund' => $refund,
                    'passenger_id' => $passengerId,
                ]);

                $connection->prepare(
                    'UPDATE reservation
                     SET statut = "annulee", updated_at = NOW()
                     WHERE id = :reservation_id'
                )->execute(['reservation_id' => $reservationId]);

                $connection->prepare(
                    'INSERT INTO transaction_credit
                        (utilisateur_cible_id, covoiturage_id, reservation_id, montant, type)
                     VALUES
                        (:passenger_id, :ride_id, :reservation_id, :amount, "annulation_covoiturage")'
                )->execute([
                    'passenger_id' => $passengerId,
                    'ride_id' => $rideId,
                    'reservation_id' => $reservationId,
                    'amount' => $refund,
                ]);
            }

            $connection->prepare(
                'UPDATE covoiturage
                 SET statut = "annule", updated_at = NOW()
                 WHERE id = :ride_id'
            )->execute(['ride_id' => $rideId]);

            $connection->commit();

            return [
                'success' => true,
                'message' => 'Voyage chauffeur annule. Les participants seront notifies par email.',
                'statut' => 'annule',
                'reservations_annulees' => count($reservations),
            ];
        } catch (Throwable $exception) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            throw $exception;
        }
    }

    public function updateDriverRideStatus(int $rideId, int $driverId, string $expectedStatus, string $newStatus): array
    {
        $connection = $this->connection();
        $connection->beginTransaction();

        try {
            $statement = $connection->prepare(
                'SELECT id, statut
                 FROM covoiturage
                 WHERE id = :ride_id
                   AND chauffeur_id = :driver_id
                 FOR UPDATE'
            );
            $statement->execute([
                'ride_id' => $rideId,
                'driver_id' => $driverId,
            ]);
            $ride = $statement->fetch();

            if ($ride === false) {
                $connection->rollBack();
                return ['success' => false, 'message' => 'Voyage introuvable.'];
            }

            if ((string) $ride['statut'] !== $expectedStatus) {
                $connection->rollBack();
                return ['success' => false, 'message' => 'Le statut actuel du voyage ne permet pas cette action.'];
            }

            $connection->prepare(
                'UPDATE covoiturage
                 SET statut = :new_status, updated_at = NOW()
                 WHERE id = :ride_id'
            )->execute([
                'new_status' => $newStatus,
                'ride_id' => $rideId,
            ]);

            $connection->commit();

            return [
                'success' => true,
                'message' => $newStatus === 'demarre'
                    ? 'Le covoiturage a ete demarre.'
                    : 'Le covoiturage est arrive a destination. Les participants seront invites a valider le trajet.',
                'statut' => $newStatus,
            ];
        } catch (Throwable $exception) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            throw $exception;
        }
    }

    public function validatePassengerReservation(int $reservationId, int $passengerId, int $rating, string $comment): array
    {
        $connection = $this->connection();
        $connection->beginTransaction();

        try {
            $statement = $connection->prepare(
                'SELECT
                    r.id,
                    r.covoiturage_id,
                    r.credits_payes,
                    r.statut,
                    c.chauffeur_id,
                    c.statut AS covoiturage_statut
                 FROM reservation r
                 INNER JOIN covoiturage c ON c.id = r.covoiturage_id
                 WHERE r.id = :reservation_id
                   AND r.passager_id = :passenger_id
                 FOR UPDATE'
            );
            $statement->execute([
                'reservation_id' => $reservationId,
                'passenger_id' => $passengerId,
            ]);
            $reservation = $statement->fetch();

            if ($reservation === false) {
                $connection->rollBack();
                return ['success' => false, 'message' => 'Reservation introuvable.'];
            }

            if ((string) $reservation['statut'] === 'annulee') {
                $connection->rollBack();
                return ['success' => false, 'message' => 'Une reservation annulee ne peut pas etre validee.'];
            }

            if ((string) $reservation['statut'] === 'validee') {
                $connection->rollBack();
                return ['success' => false, 'message' => 'Cette reservation est deja validee.'];
            }

            if ((string) $reservation['covoiturage_statut'] !== 'termine') {
                $connection->rollBack();
                return ['success' => false, 'message' => 'Le trajet doit etre termine avant validation.'];
            }

            $reviewStatement = $connection->prepare('SELECT COUNT(*) FROM avis WHERE reservation_id = :reservation_id');
            $reviewStatement->execute(['reservation_id' => $reservationId]);
            if ((int) $reviewStatement->fetchColumn() > 0) {
                $connection->rollBack();
                return ['success' => false, 'message' => 'Un avis existe deja pour cette reservation.'];
            }

            $driverCredit = max(0, (int) $reservation['credits_payes'] - 2);
            $driverId = (int) $reservation['chauffeur_id'];
            $rideId = (int) $reservation['covoiturage_id'];

            $connection->prepare(
                'UPDATE reservation
                 SET statut = "validee", confirmation_passager = 1, updated_at = NOW()
                 WHERE id = :reservation_id'
            )->execute(['reservation_id' => $reservationId]);

            if ($driverCredit > 0) {
                $connection->prepare(
                    'UPDATE utilisateur
                     SET credits = credits + :amount, updated_at = NOW()
                     WHERE id = :driver_id'
                )->execute([
                    'amount' => $driverCredit,
                    'driver_id' => $driverId,
                ]);

                $connection->prepare(
                    'INSERT INTO transaction_credit
                        (utilisateur_cible_id, covoiturage_id, reservation_id, montant, type)
                     VALUES
                        (:driver_id, :ride_id, :reservation_id, :amount, "paiement_chauffeur")'
                )->execute([
                    'driver_id' => $driverId,
                    'ride_id' => $rideId,
                    'reservation_id' => $reservationId,
                    'amount' => $driverCredit,
                ]);
            }

            $connection->prepare(
                'INSERT INTO avis
                    (reservation_id, chauffeur_id, passager_id, note, commentaire, statut)
                 VALUES
                    (:reservation_id, :driver_id, :passenger_id, :rating, :comment, "en_attente")'
            )->execute([
                'reservation_id' => $reservationId,
                'driver_id' => $driverId,
                'passenger_id' => $passengerId,
                'rating' => max(1, min(5, $rating)),
                'comment' => $comment,
            ]);

            $connection->commit();

            return [
                'success' => true,
                'message' => 'Trajet valide. Votre avis est en attente de moderation.',
                'statut' => 'validee',
                'credit_chauffeur' => $driverCredit,
            ];
        } catch (Throwable $exception) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            throw $exception;
        }
    }

    public function reportPassengerReservation(int $reservationId, int $passengerId, string $description): array
    {
        $connection = $this->connection();
        $connection->beginTransaction();

        try {
            $statement = $connection->prepare(
                'SELECT
                    r.id,
                    r.covoiturage_id,
                    r.statut
                 FROM reservation r
                 INNER JOIN covoiturage c ON c.id = r.covoiturage_id
                 WHERE r.id = :reservation_id
                   AND r.passager_id = :passenger_id
                 FOR UPDATE'
            );
            $statement->execute([
                'reservation_id' => $reservationId,
                'passenger_id' => $passengerId,
            ]);
            $reservation = $statement->fetch();

            if ($reservation === false) {
                $connection->rollBack();
                return ['success' => false, 'message' => 'Reservation introuvable.'];
            }

            if ((string) $reservation['statut'] === 'annulee') {
                $connection->rollBack();
                return ['success' => false, 'message' => 'Une reservation annulee ne peut pas etre signalee.'];
            }

            if ((string) $reservation['statut'] === 'probleme') {
                $connection->rollBack();
                return ['success' => false, 'message' => 'Un probleme a deja ete signale pour cette reservation.'];
            }

            $rideId = (int) $reservation['covoiturage_id'];

            $connection->prepare(
                'UPDATE reservation
                 SET statut = "probleme", updated_at = NOW()
                 WHERE id = :reservation_id'
            )->execute(['reservation_id' => $reservationId]);

            $connection->prepare(
                'INSERT INTO incident
                    (reservation_id, covoiturage_id, signale_par, description, statut)
                 VALUES
                    (:reservation_id, :ride_id, :passenger_id, :description, "ouvert")'
            )->execute([
                'reservation_id' => $reservationId,
                'ride_id' => $rideId,
                'passenger_id' => $passengerId,
                'description' => $description,
            ]);

            $connection->commit();

            return [
                'success' => true,
                'message' => 'Probleme signale. Un employe devra contacter le chauffeur avant la mise a jour des credits.',
                'statut' => 'probleme',
                'incident_id' => (int) $connection->lastInsertId(),
            ];
        } catch (Throwable $exception) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            throw $exception;
        }
    }

    private function findClosestDateFromFallback(array $filters): ?string
    {
        if ($filters['depart'] === '' || $filters['arrivee'] === '') {
            return null;
        }

        $dates = [];

        foreach ($this->rides as $ride) {
            if (
                $ride['available_seats'] > 0
                && $this->matchesCity($ride['departure_city'], $filters['depart'])
                && $this->matchesCity($ride['arrival_city'], $filters['arrivee'])
            ) {
                $dates[] = substr($ride['departure_at'], 0, 10);
            }
        }

        sort($dates);

        return $dates[0] ?? null;
    }

    private function findFromDatabase(int $id): ?array
    {
        $statement = $this->connection()->prepare($this->baseRideQuery() . ' WHERE c.id = :id LIMIT 1');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        if ($row === false) {
            return null;
        }

        return $this->mapRide($row);
    }

    private function searchFromDatabase(array $filters): array
    {
        $where = ['c.statut = "ouvert"', 'c.nb_places_restantes > 0'];
        $parameters = [];

        if ($filters['depart'] !== '') {
            $where[] = 'LOWER(c.ville_depart) = LOWER(:depart)';
            $parameters['depart'] = trim((string) $filters['depart']);
        }

        if ($filters['arrivee'] !== '') {
            $where[] = 'LOWER(c.ville_arrivee) = LOWER(:arrivee)';
            $parameters['arrivee'] = trim((string) $filters['arrivee']);
        }

        if ($filters['date'] !== '') {
            $where[] = 'DATE(c.date_depart) = :date_depart';
            $parameters['date_depart'] = trim((string) $filters['date']);
        }

        if ($filters['ecologique']) {
            $where[] = 'energie.est_ecologique = 1';
        }

        if ($filters['prix_max'] !== null) {
            $where[] = 'c.prix_personne <= :prix_max';
            $parameters['prix_max'] = (int) $filters['prix_max'];
        }

        if ($filters['duree_max'] !== null) {
            $where[] = 'TIMESTAMPDIFF(MINUTE, c.date_depart, c.date_arrivee) <= :duree_max';
            $parameters['duree_max'] = (int) $filters['duree_max'];
        }

        if ($filters['note_min'] !== null) {
            $where[] = 'COALESCE(notes.note_moyenne, 0) >= :note_min';
            $parameters['note_min'] = (float) $filters['note_min'];
        }

        $statement = $this->connection()->prepare(
            $this->baseRideQuery()
            . ' WHERE ' . implode(' AND ', $where)
            . ' ORDER BY c.date_depart ASC'
        );
        $statement->execute($parameters);

        return array_map(fn (array $row): array => $this->mapRide($row), $statement->fetchAll());
    }

    private function findClosestDateFromDatabase(array $filters): ?string
    {
        if ($filters['depart'] === '' || $filters['arrivee'] === '') {
            return null;
        }

        $statement = $this->connection()->prepare(
            'SELECT DATE(c.date_depart) AS closest_date
             FROM covoiturage c
             WHERE c.statut = "ouvert"
               AND c.nb_places_restantes > 0
               AND LOWER(c.ville_depart) = LOWER(:depart)
               AND LOWER(c.ville_arrivee) = LOWER(:arrivee)
             ORDER BY c.date_depart ASC
             LIMIT 1'
        );
        $statement->execute([
            'depart' => trim((string) $filters['depart']),
            'arrivee' => trim((string) $filters['arrivee']),
        ]);
        $closestDate = $statement->fetchColumn();

        return $closestDate === false ? null : (string) $closestDate;
    }

    private function baseRideQuery(): string
    {
        return 'SELECT
                c.id,
                c.chauffeur_id,
                c.ville_depart,
                c.lieu_depart,
                c.ville_arrivee,
                c.lieu_arrivee,
                c.date_depart,
                c.date_arrivee,
                c.nb_places_restantes,
                c.prix_personne,
                chauffeur.pseudo AS chauffeur_pseudo,
                chauffeur.photo AS chauffeur_photo,
                marque.libelle AS marque,
                vehicule.modele,
                energie.libelle AS energie,
                energie.est_ecologique,
                COALESCE(notes.note_moyenne, 0) AS note_moyenne
            FROM covoiturage c
            INNER JOIN utilisateur chauffeur ON chauffeur.id = c.chauffeur_id
            INNER JOIN vehicule ON vehicule.id = c.vehicule_id
            INNER JOIN marque ON marque.id = vehicule.marque_id
            INNER JOIN energie ON energie.id = vehicule.energie_id
            LEFT JOIN (
                SELECT chauffeur_id, ROUND(AVG(note), 1) AS note_moyenne
                FROM avis
                WHERE statut = "valide"
                GROUP BY chauffeur_id
            ) notes ON notes.chauffeur_id = chauffeur.id';
    }

    private function mapRide(array $row): array
    {
        $driverId = (int) ($row['chauffeur_id'] ?? 0);
        $rideId = (int) $row['id'];

        return [
            'id' => $rideId,
            'driver_id' => (int) $row['chauffeur_id'],
            'driver_pseudo' => (string) $row['chauffeur_pseudo'],
            'driver_rating' => (float) $row['note_moyenne'],
            'driver_photo' => $row['chauffeur_photo'] ?: '/assets/images/default-driver.svg',
            'departure_city' => (string) $row['ville_depart'],
            'departure_place' => (string) ($row['lieu_depart'] ?? ''),
            'arrival_city' => (string) $row['ville_arrivee'],
            'arrival_place' => (string) ($row['lieu_arrivee'] ?? ''),
            'departure_at' => (string) $row['date_depart'],
            'arrival_at' => (string) $row['date_arrivee'],
            'available_seats' => (int) $row['nb_places_restantes'],
            'price' => (int) $row['prix_personne'],
            'vehicle_brand' => (string) $row['marque'],
            'vehicle_model' => (string) $row['modele'],
            'energy' => (string) $row['energie'],
            'is_ecological' => (bool) $row['est_ecologique'],
            'preferences' => $driverId > 0 ? $this->preferencesForDriver($driverId) : [],
            'reviews' => $this->reviewsForRide($rideId),
        ];
    }

    private function preferencesForDriver(int $driverId): array
    {
        $statement = $this->connection()->prepare(
            'SELECT p.libelle
             FROM preference p
             INNER JOIN utilisateur_preference up ON up.preference_id = p.id
             WHERE up.utilisateur_id = :driver_id
             ORDER BY p.libelle'
        );
        $statement->execute(['driver_id' => $driverId]);

        return array_column($statement->fetchAll(), 'libelle');
    }

    private function reviewsForRide(int $rideId): array
    {
        $statement = $this->connection()->prepare(
            'SELECT passager.pseudo AS author, avis.note AS rating, avis.commentaire AS comment
             FROM avis
             INNER JOIN reservation r ON r.id = avis.reservation_id
             INNER JOIN utilisateur passager ON passager.id = avis.passager_id
             WHERE r.covoiturage_id = :ride_id
               AND avis.statut = "valide"
             ORDER BY avis.created_at DESC'
        );
        $statement->execute(['ride_id' => $rideId]);

        return array_map(static fn (array $review): array => [
            'author' => (string) $review['author'],
            'rating' => (int) $review['rating'],
            'comment' => (string) ($review['comment'] ?? ''),
        ], $statement->fetchAll());
    }

    private function connection(): PDO
    {
        return Database::connection();
    }

    private function matchesCity(string $city, string $search): bool
    {
        return strtolower($city) === strtolower(trim($search));
    }

    private function durationInMinutes(array $ride): int
    {
        $departure = strtotime((string) $ride['departure_at']);
        $arrival = strtotime((string) $ride['arrival_at']);

        if ($departure === false || $arrival === false || $arrival < $departure) {
            return PHP_INT_MAX;
        }

        return (int) (($arrival - $departure) / 60);
    }
}
