<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class VehicleRepository
{
    public function __construct(private readonly PDO $connection)
    {
    }

    public function vehiclesForUser(int $userId): array
    {
        $statement = $this->connection->prepare(
            'SELECT
                v.id,
                v.immatriculation,
                v.date_premiere_immatriculation,
                v.modele,
                v.couleur,
                v.nb_places,
                m.libelle AS marque,
                e.libelle AS energie
             FROM vehicule v
             INNER JOIN marque m ON m.id = v.marque_id
             INNER JOIN energie e ON e.id = v.energie_id
             WHERE v.utilisateur_id = :user_id
             ORDER BY v.created_at DESC'
        );
        $statement->execute(['user_id' => $userId]);

        return array_map(static fn (array $vehicle): array => [
            'id' => (int) $vehicle['id'],
            'immatriculation' => (string) $vehicle['immatriculation'],
            'date_premiere_immatriculation' => (string) $vehicle['date_premiere_immatriculation'],
            'marque' => (string) $vehicle['marque'],
            'modele' => (string) $vehicle['modele'],
            'couleur' => (string) $vehicle['couleur'],
            'energie' => (string) $vehicle['energie'],
            'nb_places' => (int) $vehicle['nb_places'],
            'source' => 'sql',
        ], $statement->fetchAll());
    }

    public function preferencesForUser(int $userId): array
    {
        $statement = $this->connection->prepare(
            'SELECT p.libelle
             FROM preference p
             INNER JOIN utilisateur_preference up ON up.preference_id = p.id
             WHERE up.utilisateur_id = :user_id
             ORDER BY p.libelle'
        );
        $statement->execute(['user_id' => $userId]);

        return array_column($statement->fetchAll(), 'libelle');
    }

    public function createVehicle(int $userId, array $vehicle, array $preferences): int
    {
        $brandId = $this->findOrCreateReference('marque', (string) $vehicle['marque']);
        $energyId = $this->findOrCreateEnergy((string) $vehicle['energie']);

        $statement = $this->connection->prepare(
            'INSERT INTO vehicule
                (utilisateur_id, marque_id, energie_id, modele, immatriculation, couleur, date_premiere_immatriculation, nb_places)
             VALUES
                (:user_id, :brand_id, :energy_id, :modele, :immatriculation, :couleur, :date_premiere_immatriculation, :nb_places)'
        );
        $statement->execute([
            'user_id' => $userId,
            'brand_id' => $brandId,
            'energy_id' => $energyId,
            'modele' => $vehicle['modele'],
            'immatriculation' => $vehicle['immatriculation'],
            'couleur' => $vehicle['couleur'],
            'date_premiere_immatriculation' => $vehicle['date_premiere_immatriculation'],
            'nb_places' => $vehicle['nb_places'],
        ]);
        $vehicleId = (int) $this->connection->lastInsertId();

        foreach ($preferences as $preference) {
            $preferenceId = $this->findOrCreatePreference($preference);
            $this->connection->prepare(
                'INSERT IGNORE INTO utilisateur_preference (utilisateur_id, preference_id)
                 VALUES (:user_id, :preference_id)'
            )->execute([
                'user_id' => $userId,
                'preference_id' => $preferenceId,
            ]);
        }

        return $vehicleId;
    }

    private function findOrCreateReference(string $table, string $label): int
    {
        $statement = $this->connection->prepare('SELECT id FROM ' . $table . ' WHERE libelle = :label LIMIT 1');
        $statement->execute(['label' => $label]);
        $id = $statement->fetchColumn();

        if ($id !== false) {
            return (int) $id;
        }

        $insert = $this->connection->prepare('INSERT INTO ' . $table . ' (libelle) VALUES (:label)');
        $insert->execute(['label' => $label]);

        return (int) $this->connection->lastInsertId();
    }

    private function findOrCreateEnergy(string $label): int
    {
        $statement = $this->connection->prepare('SELECT id FROM energie WHERE libelle = :label LIMIT 1');
        $statement->execute(['label' => $label]);
        $id = $statement->fetchColumn();

        if ($id !== false) {
            return (int) $id;
        }

        $insert = $this->connection->prepare(
            'INSERT INTO energie (libelle, est_ecologique) VALUES (:label, :is_ecological)'
        );
        $insert->execute([
            'label' => $label,
            'is_ecological' => $label === 'electrique' ? 1 : 0,
        ]);

        return (int) $this->connection->lastInsertId();
    }

    private function findOrCreatePreference(string $label): int
    {
        $statement = $this->connection->prepare('SELECT id FROM preference WHERE libelle = :label LIMIT 1');
        $statement->execute(['label' => $label]);
        $id = $statement->fetchColumn();

        if ($id !== false) {
            return (int) $id;
        }

        $insert = $this->connection->prepare(
            'INSERT INTO preference (libelle, type) VALUES (:label, "chauffeur")'
        );
        $insert->execute(['label' => $label]);

        return (int) $this->connection->lastInsertId();
    }
}
