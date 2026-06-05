# Kanban initial - EcoRide

Ce document sert de base pour creer le tableau Kanban dans Trello, Notion ou Jira.

## Colonnes recommandees

1. Fonctionnalites prevues
2. A faire prochainement
3. En cours
4. Termine sur develop
5. Merge sur main

## Fonctionnalites prevues

### P0 - Organisation du projet

- Initialiser le depot GitHub.
- Creer les branches `main` et `develop`.
- Rediger le `README.md`.
- Choisir la stack technique finale.
- Creer la structure du projet.
- Creer le Kanban partage.

### P1 - Documentation et conception

- Rediger le cahier des charges simplifie.
- Rediger la documentation de gestion de projet.
- Rediger la documentation technique initiale.
- Realiser la charte graphique.
- Realiser 3 maquettes desktop.
- Realiser 3 maquettes mobile.
- Creer le MCD corrige.
- Creer le diagramme d'utilisation.
- Creer le diagramme de sequence reservation.

### P1 - Base de donnees

- Creer le script SQL de creation.
- Creer le script SQL de donnees de demonstration.
- Creer les tables utilisateurs et roles.
- Creer les tables vehicules, marques et energies.
- Creer la table covoiturages.
- Creer la table reservations.
- Creer la table avis.
- Creer la table preferences.
- Creer la gestion des credits.
- Prevoir les donnees NoSQL.

### P1 - Authentification

- Creer la page inscription.
- Verifier la securite du mot de passe.
- Ajouter les 20 credits a la creation du compte.
- Creer la page connexion.
- Creer la deconnexion.
- Proteger les pages connectees.
- Gerer les roles utilisateur, employe et administrateur.

### P1 - Frontend public

- Creer la page d'accueil.
- Creer le menu de navigation.
- Creer le pied de page.
- Creer la page contact.
- Creer la page mentions legales.
- Creer la page recherche des covoiturages.
- Creer la page detail d'un covoiturage.

### P1 - Recherche de covoiturage

- Rechercher par ville de depart.
- Rechercher par ville d'arrivee.
- Rechercher par date.
- Afficher uniquement les trajets avec places disponibles.
- Afficher la date la plus proche si aucun trajet n'est disponible.
- Afficher les informations du chauffeur.
- Afficher le prix, les places, les horaires et l'aspect ecologique.

### P2 - Filtres

- Filtrer par trajet ecologique.
- Filtrer par prix maximum.
- Filtrer par duree maximum.
- Filtrer par note minimale.

### P2 - Reservation

- Afficher le bouton participer.
- Rediriger vers connexion si le visiteur n'est pas connecte.
- Verifier les credits disponibles.
- Demander une double confirmation.
- Creer une reservation.
- Debiter les credits du passager.
- Mettre a jour les places restantes.

### P2 - Espace utilisateur

- Afficher les informations du compte.
- Choisir le profil passager, chauffeur ou les deux.
- Ajouter un vehicule.
- Modifier un vehicule.
- Ajouter des preferences.
- Consulter les credits.

### P2 - Espace chauffeur

- Creer un covoiturage.
- Selectionner un vehicule existant.
- Ajouter un nouveau vehicule pendant la creation du trajet.
- Definir le prix.
- Consulter les trajets crees.
- Annuler un trajet.

### P2 - Historique

- Afficher les trajets comme passager.
- Afficher les trajets comme chauffeur.
- Afficher les trajets termines.
- Annuler une participation.
- Rembourser les credits si necessaire.
- Notifier les participants si le chauffeur annule.

### P3 - Deroulement d'un trajet

- Bouton demarrer pour le chauffeur.
- Bouton arrivee a destination.
- Notifier les passagers apres l'arrivee.
- Demander la validation du bon deroulement.
- Verser les credits au chauffeur si tout est valide.
- Signaler un probleme si besoin.

### P3 - Avis

- Permettre a un passager de noter un chauffeur.
- Permettre a un passager de laisser un commentaire.
- Mettre l'avis en attente.
- Afficher uniquement les avis valides.
- Calculer la note moyenne du chauffeur.

### P3 - Espace employe

- Lister les avis en attente.
- Valider un avis.
- Refuser un avis.
- Lister les trajets problematiques.
- Afficher les informations des personnes concernees.

### P3 - Espace administrateur

- Creer un compte employe.
- Suspendre un utilisateur.
- Suspendre un employe.
- Afficher le nombre de covoiturages par jour.
- Afficher les credits gagnes par jour.
- Afficher le total des credits gagnes.

### P4 - Tests et securite

- Tester l'inscription.
- Tester la connexion.
- Tester la recherche.
- Tester la reservation.
- Tester les credits.
- Tester les droits par role.
- Tester les injections SQL simples.
- Tester l'affichage des donnees utilisateur contre XSS.

### P4 - Deploiement et rendu

- Preparer l'environnement de production.
- Deployer l'application.
- Verifier les variables de configuration.
- Rediger la documentation de deploiement.
- Finaliser le manuel utilisateur.
- Ajouter les identifiants de demonstration.
- Verifier tous les livrables.
