CREATE DATABASE IF NOT EXISTS ecoride
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE ecoride;

CREATE TABLE utilisateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pseudo VARCHAR(50) NOT NULL UNIQUE,
    nom VARCHAR(100) NULL,
    prenom VARCHAR(100) NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    telephone VARCHAR(30) NULL,
    adresse VARCHAR(255) NULL,
    date_naissance DATE NULL,
    photo VARCHAR(255) NULL,
    credits INT NOT NULL DEFAULT 20,
    statut VARCHAR(30) NOT NULL DEFAULT 'actif',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL
);

CREATE TABLE role (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    libelle VARCHAR(100) NOT NULL
);

CREATE TABLE utilisateur_role (
    utilisateur_id INT NOT NULL,
    role_id INT NOT NULL,
    PRIMARY KEY (utilisateur_id, role_id),
    CONSTRAINT fk_utilisateur_role_utilisateur FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    CONSTRAINT fk_utilisateur_role_role FOREIGN KEY (role_id) REFERENCES role(id) ON DELETE CASCADE
);

CREATE TABLE marque (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE energie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(100) NOT NULL UNIQUE,
    est_ecologique BOOLEAN NOT NULL DEFAULT FALSE
);

CREATE TABLE vehicule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    marque_id INT NOT NULL,
    energie_id INT NOT NULL,
    modele VARCHAR(100) NOT NULL,
    immatriculation VARCHAR(30) NOT NULL UNIQUE,
    couleur VARCHAR(50) NOT NULL,
    date_premiere_immatriculation DATE NOT NULL,
    nb_places INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_vehicule_utilisateur FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id),
    CONSTRAINT fk_vehicule_marque FOREIGN KEY (marque_id) REFERENCES marque(id),
    CONSTRAINT fk_vehicule_energie FOREIGN KEY (energie_id) REFERENCES energie(id)
);

CREATE TABLE preference (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(150) NOT NULL,
    type VARCHAR(50) NULL
);

CREATE TABLE utilisateur_preference (
    utilisateur_id INT NOT NULL,
    preference_id INT NOT NULL,
    PRIMARY KEY (utilisateur_id, preference_id),
    CONSTRAINT fk_utilisateur_preference_utilisateur FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id) ON DELETE CASCADE,
    CONSTRAINT fk_utilisateur_preference_preference FOREIGN KEY (preference_id) REFERENCES preference(id) ON DELETE CASCADE
);

CREATE TABLE covoiturage (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chauffeur_id INT NOT NULL,
    vehicule_id INT NOT NULL,
    ville_depart VARCHAR(100) NOT NULL,
    lieu_depart VARCHAR(255) NULL,
    ville_arrivee VARCHAR(100) NOT NULL,
    lieu_arrivee VARCHAR(255) NULL,
    date_depart DATETIME NOT NULL,
    date_arrivee DATETIME NOT NULL,
    prix_personne INT NOT NULL,
    nb_places_total INT NOT NULL,
    nb_places_restantes INT NOT NULL,
    statut VARCHAR(30) NOT NULL DEFAULT 'ouvert',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL,
    CONSTRAINT fk_covoiturage_chauffeur FOREIGN KEY (chauffeur_id) REFERENCES utilisateur(id),
    CONSTRAINT fk_covoiturage_vehicule FOREIGN KEY (vehicule_id) REFERENCES vehicule(id)
);

CREATE TABLE reservation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    covoiturage_id INT NOT NULL,
    passager_id INT NOT NULL,
    credits_payes INT NOT NULL,
    statut VARCHAR(30) NOT NULL DEFAULT 'confirmee',
    confirmation_passager BOOLEAN NOT NULL DEFAULT FALSE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL,
    CONSTRAINT fk_reservation_covoiturage FOREIGN KEY (covoiturage_id) REFERENCES covoiturage(id),
    CONSTRAINT fk_reservation_passager FOREIGN KEY (passager_id) REFERENCES utilisateur(id)
);

CREATE TABLE avis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reservation_id INT NOT NULL UNIQUE,
    chauffeur_id INT NOT NULL,
    passager_id INT NOT NULL,
    note INT NOT NULL,
    commentaire TEXT NULL,
    statut VARCHAR(30) NOT NULL DEFAULT 'en_attente',
    valide_par INT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    moderated_at DATETIME NULL,
    CONSTRAINT fk_avis_reservation FOREIGN KEY (reservation_id) REFERENCES reservation(id),
    CONSTRAINT fk_avis_chauffeur FOREIGN KEY (chauffeur_id) REFERENCES utilisateur(id),
    CONSTRAINT fk_avis_passager FOREIGN KEY (passager_id) REFERENCES utilisateur(id),
    CONSTRAINT fk_avis_valide_par FOREIGN KEY (valide_par) REFERENCES utilisateur(id)
);

CREATE TABLE incident (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reservation_id INT NOT NULL UNIQUE,
    covoiturage_id INT NOT NULL,
    signale_par INT NOT NULL,
    employe_id INT NULL,
    description TEXT NOT NULL,
    statut VARCHAR(30) NOT NULL DEFAULT 'ouvert',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    resolved_at DATETIME NULL,
    CONSTRAINT fk_incident_reservation FOREIGN KEY (reservation_id) REFERENCES reservation(id),
    CONSTRAINT fk_incident_covoiturage FOREIGN KEY (covoiturage_id) REFERENCES covoiturage(id),
    CONSTRAINT fk_incident_signale_par FOREIGN KEY (signale_par) REFERENCES utilisateur(id),
    CONSTRAINT fk_incident_employe FOREIGN KEY (employe_id) REFERENCES utilisateur(id)
);

CREATE TABLE transaction_credit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_source_id INT NULL,
    utilisateur_cible_id INT NULL,
    covoiturage_id INT NULL,
    reservation_id INT NULL,
    montant INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_transaction_source FOREIGN KEY (utilisateur_source_id) REFERENCES utilisateur(id),
    CONSTRAINT fk_transaction_cible FOREIGN KEY (utilisateur_cible_id) REFERENCES utilisateur(id),
    CONSTRAINT fk_transaction_covoiturage FOREIGN KEY (covoiturage_id) REFERENCES covoiturage(id),
    CONSTRAINT fk_transaction_reservation FOREIGN KEY (reservation_id) REFERENCES reservation(id)
);
