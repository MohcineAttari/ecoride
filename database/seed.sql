USE ecoride;

INSERT INTO role (code, libelle) VALUES
('ROLE_USER', 'Utilisateur'),
('ROLE_PASSAGER', 'Passager'),
('ROLE_CHAUFFEUR', 'Chauffeur'),
('ROLE_EMPLOYE', 'Employe'),
('ROLE_ADMIN', 'Administrateur');

INSERT INTO marque (libelle) VALUES
('Renault'),
('Peugeot'),
('Tesla'),
('Toyota');

INSERT INTO energie (libelle, est_ecologique) VALUES
('electrique', TRUE),
('essence', FALSE),
('diesel', FALSE),
('hybride', FALSE);

INSERT INTO preference (libelle, type) VALUES
('Fumeur accepte', 'standard'),
('Animaux acceptes', 'standard'),
('Musique acceptee', 'standard'),
('Discussion acceptee', 'standard');

INSERT INTO utilisateur (pseudo, nom, prenom, email, password_hash, credits, statut) VALUES
('admin', 'Admin', 'EcoRide', 'admin@ecoride.local', '$2y$12$PT/ZDjWL1zBPhZqN3rE4IO.NQxpWai0Blm72qSDllchntV2g1I3E2', 20, 'actif'),
('employe', 'Martin', 'Sophie', 'employe@ecoride.local', '$2y$12$PT/ZDjWL1zBPhZqN3rE4IO.NQxpWai0Blm72qSDllchntV2g1I3E2', 20, 'actif'),
('clara', 'Durand', 'Clara', 'clara@example.com', '$2y$12$PT/ZDjWL1zBPhZqN3rE4IO.NQxpWai0Blm72qSDllchntV2g1I3E2', 30, 'actif'),
('leo', 'Bernard', 'Leo', 'leo@example.com', '$2y$12$PT/ZDjWL1zBPhZqN3rE4IO.NQxpWai0Blm72qSDllchntV2g1I3E2', 20, 'actif');

INSERT INTO utilisateur_role (utilisateur_id, role_id) VALUES
(1, 5),
(2, 4),
(3, 1),
(3, 3),
(4, 1),
(4, 2);

INSERT INTO vehicule (utilisateur_id, marque_id, energie_id, modele, immatriculation, couleur, date_premiere_immatriculation, nb_places) VALUES
(3, 3, 1, 'Model 3', 'AA-123-AA', 'Blanc', '2022-04-12', 3);

INSERT INTO utilisateur_preference (utilisateur_id, preference_id) VALUES
(3, 2),
(3, 4);

INSERT INTO covoiturage (chauffeur_id, vehicule_id, ville_depart, lieu_depart, ville_arrivee, lieu_arrivee, date_depart, date_arrivee, prix_personne, nb_places_total, nb_places_restantes, statut) VALUES
(3, 1, 'Lyon', 'Gare Part-Dieu', 'Marseille', 'Gare Saint-Charles', '2026-06-05 08:30:00', '2026-06-05 12:00:00', 12, 3, 3, 'ouvert');
