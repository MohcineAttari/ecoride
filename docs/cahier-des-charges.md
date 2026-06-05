# Cahier des charges simplifie - EcoRide

## 1. Contexte

EcoRide est une startup francaise qui souhaite reduire l'impact environnemental des deplacements en encourageant le covoiturage en voiture.

L'objectif du projet est de realiser une application web permettant aux visiteurs de rechercher des trajets, aux utilisateurs de participer ou proposer des covoiturages, aux employes de moderer les avis et incidents, et a l'administrateur de piloter la plateforme.

Le projet est realise dans le cadre de l'ECF Developpeur Web et Web Mobile.

## 2. Objectifs principaux

- Proposer une plateforme de covoiturage simple, claire et orientee ecologie.
- Permettre la recherche de trajets par ville de depart, ville d'arrivee et date.
- Mettre en avant les trajets ecologiques effectues avec une voiture electrique.
- Permettre aux utilisateurs de reserver une place avec un systeme de credits.
- Permettre aux chauffeurs de creer et gerer leurs trajets.
- Permettre la validation des avis et le traitement des trajets problematiques.
- Fournir un espace administrateur avec gestion des comptes et statistiques.

## 3. Roles utilisateurs

### Visiteur

Un visiteur peut :

- consulter la page d'accueil ;
- rechercher un covoiturage ;
- consulter la liste des trajets disponibles ;
- consulter le detail d'un trajet ;
- creer un compte ;
- se connecter ;
- acceder a la page contact et aux mentions legales.

### Utilisateur

Un utilisateur connecte peut :

- participer a un covoiturage ;
- consulter son espace personnel ;
- choisir son role d'usage : passager, chauffeur ou les deux ;
- consulter son historique ;
- annuler une participation ;
- deposer un avis apres un trajet.

### Chauffeur

Un chauffeur peut :

- renseigner un ou plusieurs vehicules ;
- definir ses preferences ;
- creer un covoiturage ;
- demarrer un trajet ;
- declarer l'arrivee a destination ;
- annuler un trajet.

### Employe

Un employe peut :

- valider ou refuser les avis ;
- consulter les trajets signales comme problematiques ;
- acceder aux informations utiles pour traiter un incident.

### Administrateur

Un administrateur peut :

- creer des comptes employes ;
- suspendre un compte utilisateur ou employe ;
- consulter le nombre de covoiturages par jour ;
- consulter les credits gagnes par la plateforme par jour ;
- consulter le total des credits gagnes par la plateforme.

## 4. Fonctionnalites attendues

### US 1 - Page d'accueil

La page d'accueil doit contenir :

- une presentation de l'entreprise ;
- quelques images en lien avec l'ecologie et le covoiturage ;
- une barre de recherche d'itineraire ;
- un pied de page avec l'email de l'entreprise et un lien vers les mentions legales.

### US 2 - Menu de navigation

Le menu doit contenir au minimum :

- accueil ;
- covoiturages ;
- connexion ;
- contact.

### US 3 - Vue des covoiturages

Le visiteur recherche un trajet avec :

- ville de depart ;
- ville d'arrivee ;
- date.

Les resultats affichent :

- pseudo du chauffeur ;
- photo du chauffeur ;
- note du chauffeur ;
- nombre de places restantes ;
- prix ;
- date et heure de depart ;
- date et heure d'arrivee ;
- indication trajet ecologique ou non ;
- bouton detail.

Seuls les trajets avec au moins une place disponible sont affiches.

Si aucun trajet n'est disponible a la date demandee, l'application doit proposer la date du trajet le plus proche.

### US 4 - Filtres des covoiturages

Les filtres disponibles sont :

- trajet ecologique ;
- prix maximum ;
- duree maximum ;
- note minimale du chauffeur.

### US 5 - Detail d'un covoiturage

La page detail affiche :

- les informations deja visibles dans la liste ;
- les avis du conducteur ;
- le modele du vehicule ;
- la marque du vehicule ;
- l'energie du vehicule ;
- les preferences du conducteur.

### US 6 - Participer a un covoiturage

Un utilisateur peut participer si :

- il est connecte ;
- il reste au moins une place ;
- il possede assez de credits.

La reservation necessite une double confirmation.

Apres confirmation :

- la participation est enregistree ;
- les credits du passager sont mis a jour ;
- le nombre de places restantes du trajet est mis a jour.

### US 7 - Creation de compte

Un visiteur peut creer un compte avec :

- pseudo ;
- email ;
- mot de passe securise.

A la creation du compte, l'utilisateur recoit 20 credits.

### US 8 - Espace utilisateur

L'utilisateur peut choisir d'etre :

- passager ;
- chauffeur ;
- passager et chauffeur.

Un chauffeur doit renseigner :

- plaque d'immatriculation ;
- date de premiere immatriculation ;
- modele ;
- couleur ;
- marque ;
- nombre de places disponibles ;
- preferences : fumeur, animaux, preferences personnalisees.

### US 9 - Saisir un voyage

Un chauffeur peut creer un trajet avec :

- ville ou adresse de depart ;
- ville ou adresse d'arrivee ;
- date et heure de depart ;
- date et heure d'arrivee ;
- prix par personne ;
- vehicule utilise.

La plateforme preleve 2 credits sur chaque covoiturage.

### US 10 - Historique des covoiturages

Un utilisateur peut consulter :

- les trajets qu'il conduit ;
- les trajets auxquels il participe ;
- l'historique des trajets termines.

Il peut annuler une participation ou un trajet.

En cas d'annulation :

- les credits sont rembourses si necessaire ;
- les places sont mises a jour ;
- si le chauffeur annule, les participants sont notifies par email.

### US 11 - Demarrer et arreter un covoiturage

Le chauffeur peut :

- demarrer un trajet ;
- declarer l'arrivee a destination.

Apres l'arrivee :

- les passagers recoivent une notification ;
- ils confirment si le trajet s'est bien passe ;
- ils peuvent laisser une note et un avis ;
- les credits du chauffeur sont verses apres validation du bon deroulement.

En cas de probleme, un employe traite l'incident avant le versement des credits.

### US 12 - Espace employe

L'employe peut :

- valider un avis ;
- refuser un avis ;
- consulter les trajets problematiques ;
- voir le numero du covoiturage ;
- voir le pseudo et l'email des personnes concernees ;
- voir les informations principales du trajet.

### US 13 - Espace administrateur

L'administrateur peut :

- creer des comptes employes ;
- suspendre des comptes ;
- voir un graphique du nombre de covoiturages par jour ;
- voir un graphique des credits gagnes par jour ;
- voir le total des credits gagnes par la plateforme.

## 5. Regles metier

- Un trajet est ecologique si le vehicule utilise est electrique.
- Un utilisateur recoit 20 credits a la creation de son compte.
- Un utilisateur ne peut reserver que s'il possede assez de credits.
- Une reservation diminue le nombre de places restantes.
- La plateforme prend 2 credits par covoiturage.
- Les credits du chauffeur sont verses apres validation du trajet par les passagers.
- Les avis ne sont visibles qu'apres validation par un employe.
- Un compte suspendu ne peut plus acceder aux fonctionnalites connectees.
- Le compte administrateur est cree en amont, pas depuis l'application.

## 6. Securite

Les mesures prevues sont :

- hashage des mots de passe avec `password_hash` ;
- verification des mots de passe avec `password_verify` ;
- requetes SQL preparees avec `PDO` ;
- validation des donnees cote serveur ;
- echappement HTML contre les attaques XSS ;
- controle des droits par role ;
- protection des pages utilisateur, employe et administrateur ;
- gestion des sessions ;
- messages d'erreur generiques pour la connexion ;
- non-stockage des mots de passe en clair.

## 7. Stack technique proposee

- Frontend : HTML5, CSS3, Bootstrap, JavaScript.
- Backend : PHP avec PDO.
- Base relationnelle : MySQL ou MariaDB.
- Base NoSQL : MongoDB.
- Graphiques : Chart.js.
- Versioning : Git et GitHub.
- Gestion de projet : Trello ou Notion.

## 8. Livrables

- Depot GitHub public.
- Application deployee.
- Lien du Kanban.
- Fichier `README.md`.
- Script SQL de creation de la base.
- Script SQL de donnees de demonstration.
- Manuel d'utilisation PDF.
- Charte graphique PDF.
- 3 maquettes desktop.
- 3 maquettes mobile.
- Documentation de gestion de projet.
- Documentation technique.
- Documentation de deploiement.

## 9. Priorites de developpement

### Priorite 1 - Base fonctionnelle

- Accueil.
- Recherche de covoiturages.
- Liste des resultats.
- Detail d'un covoiturage.
- Inscription.
- Connexion.

### Priorite 2 - Parcours utilisateur

- Reservation.
- Gestion des credits.
- Espace utilisateur.
- Ajout de vehicule.
- Creation de trajet.
- Historique.

### Priorite 3 - Gestion avancee

- Demarrage et fin de trajet.
- Avis.
- Incidents.
- Espace employe.
- Espace administrateur.
- Graphiques.

### Priorite 4 - Finalisation

- Securisation.
- Tests.
- Documentation.
- Donnees de demonstration.
- Deploiement.
