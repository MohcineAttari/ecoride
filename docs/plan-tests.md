# Plan de tests - EcoRide

Ce document liste les tests fonctionnels et securite a effectuer avant la presentation de l'ECF.

## Environnement de test

- Application lancee en local sur `http://localhost:8000`
- Base MariaDB demarree avec `.\scripts\start-database.ps1`
- Jeu de donnees charge depuis `database/seed.sql`
- Navigateur recent : Chrome, Edge ou Firefox

## Comptes de test

| Profil | Email | Mot de passe | Objectif |
| --- | --- | --- | --- |
| Chauffeur | clara@example.com | Password123! | Creer et gerer des trajets |
| Passager | leo@example.com | Password123! | Reserver, annuler, valider et signaler |
| Employe | employe@ecoride.local | Password123! | Moderer les avis et incidents |
| Admin | admin@ecoride.local | Password123! | Gerer les comptes et statistiques |

## Tests fonctionnels

| ID | Parcours | Etapes | Resultat attendu |
| --- | --- | --- | --- |
| T01 | Accueil | Ouvrir `/` | La page d'accueil s'affiche avec la recherche et le style EcoRide |
| T02 | Recherche simple | Rechercher Lyon vers Marseille le 2026-06-05 | Le trajet de demonstration apparait |
| T03 | Filtres | Ajouter un prix maximum, une duree maximum ou trajet ecologique | La liste est filtree selon les criteres |
| T04 | Detail trajet | Ouvrir `/covoiturages/detail?id=1` | Les infos trajet, conducteur, vehicule, preferences et avis s'affichent |
| T05 | Connexion | Se connecter avec `leo@example.com` | L'utilisateur arrive sur son espace ou peut y acceder |
| T06 | Inscription | Creer un nouveau compte avec un mot de passe valide | Le compte est cree avec 20 credits |
| T07 | Mot de passe invalide | Essayer un mot de passe trop court | Un message d'erreur s'affiche |
| T08 | Participation | Connecte en passager, participer au trajet 1 | Les credits sont debites et la reservation est creee |
| T09 | Credits insuffisants | Tenter de reserver avec un solde insuffisant | La reservation est refusee |
| T10 | Annulation reservation | Annuler une reservation depuis `Mon espace` | Les credits sont rembourses et la place est liberee |
| T11 | Profil utilisateur | Passer de passager a chauffeur ou passager/chauffeur | Les roles sont mis a jour et conserves |
| T12 | Vehicule | Ajouter puis modifier un vehicule | Le vehicule apparait et les modifications sont conservees |
| T13 | Creation trajet | Connecte en chauffeur, saisir un voyage | Le trajet apparait dans l'espace chauffeur et la recherche |
| T14 | Deroulement trajet | Demarrer puis terminer un trajet chauffeur | Le statut passe de ouvert a demarre puis termine |
| T15 | Validation passager | Valider un trajet termine avec note/commentaire | Un avis est cree en attente de moderation |
| T16 | Signalement | Signaler un probleme sur une reservation | Un incident est cree pour l'employe |
| T17 | Moderation avis | Connecte employe, valider/refuser un avis | Le statut de l'avis change |
| T18 | Incident employe | Connecte employe, resoudre un incident | L'incident passe en resolu |
| T19 | Creation employe | Connecte admin, creer un employe | Le compte employe est cree avec le bon role |
| T20 | Suspension compte | Connecte admin, suspendre un compte non admin | Le compte ne peut plus se connecter |
| T21 | Statistiques admin | Ouvrir `/admin` | Les chiffres et graphiques s'affichent |
| T22 | Contact | Envoyer un message via `/contact` | Un message de confirmation s'affiche |
| T23 | Mentions legales | Ouvrir `/mentions-legales` | La page s'affiche avec les informations legales |
| T24 | Page 404 | Ouvrir une URL inexistante | La page 404 stylisee s'affiche avec le statut 404 |

## Tests de securite de base

| ID | Test | Etapes | Resultat attendu |
| --- | --- | --- | --- |
| S01 | Acces espace utilisateur | Ouvrir `/mon-espace` sans etre connecte | Redirection vers `/connexion` |
| S02 | Acces employe | Ouvrir `/employe` sans role employe | Acces refuse ou redirection |
| S03 | Acces admin | Ouvrir `/admin` sans role admin | Acces refuse ou redirection |
| S04 | Injection SQL simple | Saisir `' OR 1=1 --` dans un formulaire de recherche ou connexion | Aucune erreur SQL, aucun contournement |
| S05 | XSS simple | Saisir `<script>alert(1)</script>` dans un champ texte | Le texte est echappe, aucun script ne s'execute |
| S06 | Mot de passe hashe | Verifier la table `utilisateur` | Les mots de passe ne sont pas stockes en clair |
| S07 | Donnees sensibles Git | Verifier GitHub | `.env` et `mariadb-data` ne sont pas versionnes |

## Tests d'affichage

| ID | Page | Points a verifier |
| --- | --- | --- |
| A01 | Accueil | Header, hero, formulaire, cartes et footer alignes |
| A02 | Covoiturages | Filtres lisibles, cartes modernes, prix visible |
| A03 | Connexion / Inscription | Formulaire clair, panneau d'aide coherent |
| A04 | Contact / Mentions legales | Style coherent avec le reste du site |
| A05 | Mon espace | Sections lisibles sur desktop et mobile |
| A06 | Employe / Admin | Cartes, listes et statistiques lisibles |

## Validation avant rendu

- Tous les tests critiques T01 a T12 doivent etre valides.
- Les parcours employe et admin doivent etre demonstrables.
- Le depot GitHub doit contenir le code, les scripts SQL et la documentation.
- La branche `develop` contient les derniers developpements.
- La branche `main` doit etre mise a jour avec la version stable finale avant rendu.
