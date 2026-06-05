# Dictionnaire des donnees - EcoRide

Ce dictionnaire prepare la creation du script SQL.

Les types exacts pourront etre ajustes selon le SGBD choisi.

## Table `utilisateur`

| Champ | Type | Contraintes | Description |
| --- | --- | --- | --- |
| id | INT | PK, AUTO_INCREMENT | Identifiant utilisateur |
| pseudo | VARCHAR(50) | NOT NULL, UNIQUE | Pseudo public |
| nom | VARCHAR(100) | NULL | Nom |
| prenom | VARCHAR(100) | NULL | Prenom |
| email | VARCHAR(150) | NOT NULL, UNIQUE | Email de connexion |
| password_hash | VARCHAR(255) | NOT NULL | Mot de passe hashe |
| telephone | VARCHAR(30) | NULL | Telephone |
| adresse | VARCHAR(255) | NULL | Adresse |
| date_naissance | DATE | NULL | Date de naissance |
| photo | VARCHAR(255) | NULL | Chemin ou URL de photo |
| credits | INT | NOT NULL, DEFAULT 20 | Solde de credits |
| statut | VARCHAR(30) | NOT NULL, DEFAULT 'actif' | actif ou suspendu |
| created_at | DATETIME | NOT NULL | Date de creation |
| updated_at | DATETIME | NULL | Date de mise a jour |

## Table `role`

| Champ | Type | Contraintes | Description |
| --- | --- | --- | --- |
| id | INT | PK, AUTO_INCREMENT | Identifiant role |
| code | VARCHAR(50) | NOT NULL, UNIQUE | Code du role |
| libelle | VARCHAR(100) | NOT NULL | Libelle du role |

## Table `utilisateur_role`

| Champ | Type | Contraintes | Description |
| --- | --- | --- | --- |
| utilisateur_id | INT | PK, FK | Utilisateur |
| role_id | INT | PK, FK | Role |

## Table `marque`

| Champ | Type | Contraintes | Description |
| --- | --- | --- | --- |
| id | INT | PK, AUTO_INCREMENT | Identifiant marque |
| libelle | VARCHAR(100) | NOT NULL, UNIQUE | Nom de la marque |

## Table `energie`

| Champ | Type | Contraintes | Description |
| --- | --- | --- | --- |
| id | INT | PK, AUTO_INCREMENT | Identifiant energie |
| libelle | VARCHAR(100) | NOT NULL, UNIQUE | Type d'energie |
| est_ecologique | BOOLEAN | NOT NULL, DEFAULT false | Indique si l'energie rend le trajet ecologique |

## Table `vehicule`

| Champ | Type | Contraintes | Description |
| --- | --- | --- | --- |
| id | INT | PK, AUTO_INCREMENT | Identifiant vehicule |
| utilisateur_id | INT | FK, NOT NULL | Proprietaire chauffeur |
| marque_id | INT | FK, NOT NULL | Marque |
| energie_id | INT | FK, NOT NULL | Energie |
| modele | VARCHAR(100) | NOT NULL | Modele |
| immatriculation | VARCHAR(30) | NOT NULL, UNIQUE | Plaque |
| couleur | VARCHAR(50) | NOT NULL | Couleur |
| date_premiere_immatriculation | DATE | NOT NULL | Premiere immatriculation |
| nb_places | INT | NOT NULL | Nombre de places disponibles pour les passagers |
| created_at | DATETIME | NOT NULL | Date de creation |

## Table `preference`

| Champ | Type | Contraintes | Description |
| --- | --- | --- | --- |
| id | INT | PK, AUTO_INCREMENT | Identifiant preference |
| libelle | VARCHAR(150) | NOT NULL | Texte de la preference |
| type | VARCHAR(50) | NULL | Type : standard ou personnalisee |

## Table `utilisateur_preference`

| Champ | Type | Contraintes | Description |
| --- | --- | --- | --- |
| utilisateur_id | INT | PK, FK | Chauffeur |
| preference_id | INT | PK, FK | Preference |

## Table `covoiturage`

| Champ | Type | Contraintes | Description |
| --- | --- | --- | --- |
| id | INT | PK, AUTO_INCREMENT | Identifiant covoiturage |
| chauffeur_id | INT | FK, NOT NULL | Utilisateur chauffeur |
| vehicule_id | INT | FK, NOT NULL | Vehicule utilise |
| ville_depart | VARCHAR(100) | NOT NULL | Ville de depart |
| lieu_depart | VARCHAR(255) | NULL | Adresse ou lieu precis de depart |
| ville_arrivee | VARCHAR(100) | NOT NULL | Ville d'arrivee |
| lieu_arrivee | VARCHAR(255) | NULL | Adresse ou lieu precis d'arrivee |
| date_depart | DATETIME | NOT NULL | Date et heure de depart |
| date_arrivee | DATETIME | NOT NULL | Date et heure d'arrivee |
| prix_personne | INT | NOT NULL | Prix en credits par passager |
| nb_places_total | INT | NOT NULL | Nombre initial de places |
| nb_places_restantes | INT | NOT NULL | Places restantes |
| statut | VARCHAR(30) | NOT NULL, DEFAULT 'ouvert' | Statut du trajet |
| created_at | DATETIME | NOT NULL | Date de creation |
| updated_at | DATETIME | NULL | Date de mise a jour |

## Table `reservation`

| Champ | Type | Contraintes | Description |
| --- | --- | --- | --- |
| id | INT | PK, AUTO_INCREMENT | Identifiant reservation |
| covoiturage_id | INT | FK, NOT NULL | Covoiturage reserve |
| passager_id | INT | FK, NOT NULL | Utilisateur passager |
| credits_payes | INT | NOT NULL | Credits debites au passager |
| statut | VARCHAR(30) | NOT NULL, DEFAULT 'confirmee' | Statut de reservation |
| confirmation_passager | BOOLEAN | NOT NULL, DEFAULT false | Validation apres trajet |
| created_at | DATETIME | NOT NULL | Date de reservation |
| updated_at | DATETIME | NULL | Date de mise a jour |

## Table `avis`

| Champ | Type | Contraintes | Description |
| --- | --- | --- | --- |
| id | INT | PK, AUTO_INCREMENT | Identifiant avis |
| reservation_id | INT | FK, NOT NULL, UNIQUE | Reservation associee |
| chauffeur_id | INT | FK, NOT NULL | Chauffeur evalue |
| passager_id | INT | FK, NOT NULL | Passager auteur |
| note | INT | NOT NULL | Note de 1 a 5 |
| commentaire | TEXT | NULL | Commentaire |
| statut | VARCHAR(30) | NOT NULL, DEFAULT 'en_attente' | en_attente, valide, refuse |
| valide_par | INT | FK, NULL | Employe moderateur |
| created_at | DATETIME | NOT NULL | Date de creation |
| moderated_at | DATETIME | NULL | Date de moderation |

## Table `incident`

| Champ | Type | Contraintes | Description |
| --- | --- | --- | --- |
| id | INT | PK, AUTO_INCREMENT | Identifiant incident |
| reservation_id | INT | FK, NOT NULL, UNIQUE | Reservation concernee |
| covoiturage_id | INT | FK, NOT NULL | Covoiturage concerne |
| signale_par | INT | FK, NOT NULL | Utilisateur declarant |
| employe_id | INT | FK, NULL | Employe charge du traitement |
| description | TEXT | NOT NULL | Description du probleme |
| statut | VARCHAR(30) | NOT NULL, DEFAULT 'ouvert' | Statut de traitement |
| created_at | DATETIME | NOT NULL | Date du signalement |
| resolved_at | DATETIME | NULL | Date de resolution |

## Table `transaction_credit`

| Champ | Type | Contraintes | Description |
| --- | --- | --- | --- |
| id | INT | PK, AUTO_INCREMENT | Identifiant transaction |
| utilisateur_source_id | INT | FK, NULL | Utilisateur debite |
| utilisateur_cible_id | INT | FK, NULL | Utilisateur credite |
| covoiturage_id | INT | FK, NULL | Covoiturage associe |
| reservation_id | INT | FK, NULL | Reservation associee |
| montant | INT | NOT NULL | Nombre de credits |
| type | VARCHAR(50) | NOT NULL | participation, annulation_reservation, annulation_covoiturage, paiement_chauffeur |
| created_at | DATETIME | NOT NULL | Date de transaction |

## Donnees NoSQL proposees

### Collection `activity_logs`

| Champ | Description |
| --- | --- |
| user_id | Identifiant utilisateur si connu |
| action | Action realisee |
| context | Donnees complementaires |
| created_at | Date de creation |

### Collection `contact_messages`

| Champ | Description |
| --- | --- |
| email | Email de l'expediteur |
| sujet | Sujet du message |
| message | Contenu du message |
| status | Statut de traitement |
| created_at | Date d'envoi |

### Collection `incident_notes`

| Champ | Description |
| --- | --- |
| incident_id | Identifiant incident relationnel |
| employe_id | Employe auteur de la note |
| note | Note interne |
| created_at | Date de creation |
