<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class EmployeeRepository
{
    public function __construct(private readonly PDO $connection)
    {
    }

    public function pendingReviews(): array
    {
        $statement = $this->connection->query(
            'SELECT
                a.id,
                a.note,
                a.commentaire,
                a.statut,
                a.created_at,
                c.ville_depart,
                c.ville_arrivee,
                passager.pseudo AS passager_pseudo,
                chauffeur.pseudo AS chauffeur_pseudo
             FROM avis a
             INNER JOIN reservation r ON r.id = a.reservation_id
             INNER JOIN covoiturage c ON c.id = r.covoiturage_id
             INNER JOIN utilisateur passager ON passager.id = a.passager_id
             INNER JOIN utilisateur chauffeur ON chauffeur.id = a.chauffeur_id
             WHERE a.statut = "en_attente"
             ORDER BY a.created_at ASC'
        );

        return array_map(static fn (array $review): array => [
            'id' => (int) $review['id'],
            'ride_label' => $review['ville_depart'] . ' vers ' . $review['ville_arrivee'],
            'note' => (int) $review['note'],
            'commentaire' => (string) ($review['commentaire'] ?? ''),
            'passager_pseudo' => (string) $review['passager_pseudo'],
            'chauffeur_pseudo' => (string) $review['chauffeur_pseudo'],
            'statut' => (string) $review['statut'],
            'created_at' => (string) $review['created_at'],
            'source' => 'sql',
        ], $statement->fetchAll());
    }

    public function openIncidents(): array
    {
        $statement = $this->connection->query(
            'SELECT
                i.id,
                i.reservation_id,
                i.description,
                i.statut,
                i.created_at,
                c.ville_depart,
                c.ville_arrivee,
                passager.pseudo AS passager_pseudo,
                passager.email AS passager_email
             FROM incident i
             INNER JOIN covoiturage c ON c.id = i.covoiturage_id
             INNER JOIN utilisateur passager ON passager.id = i.signale_par
             WHERE i.statut = "ouvert"
             ORDER BY i.created_at ASC'
        );

        return array_map(static fn (array $incident): array => [
            'id' => (int) $incident['id'],
            'reservation_index' => (int) $incident['reservation_id'],
            'ride_label' => $incident['ville_depart'] . ' vers ' . $incident['ville_arrivee'],
            'commentaire' => (string) $incident['description'],
            'passager_pseudo' => (string) $incident['passager_pseudo'],
            'passager_email' => (string) $incident['passager_email'],
            'statut' => (string) $incident['statut'],
            'created_at' => (string) $incident['created_at'],
            'source' => 'sql',
        ], $statement->fetchAll());
    }

    public function moderateReview(int $reviewId, int $employeeId, string $status): bool
    {
        $statement = $this->connection->prepare(
            'UPDATE avis
             SET statut = :status, valide_par = :employee_id, moderated_at = NOW()
             WHERE id = :review_id
               AND statut = "en_attente"'
        );
        $statement->execute([
            'status' => $status,
            'employee_id' => $employeeId,
            'review_id' => $reviewId,
        ]);

        return $statement->rowCount() > 0;
    }

    public function resolveIncident(int $incidentId, int $employeeId): bool
    {
        $statement = $this->connection->prepare(
            'UPDATE incident
             SET statut = "resolu", employe_id = :employee_id, resolved_at = NOW()
             WHERE id = :incident_id
               AND statut = "ouvert"'
        );
        $statement->execute([
            'employee_id' => $employeeId,
            'incident_id' => $incidentId,
        ]);

        return $statement->rowCount() > 0;
    }
}
