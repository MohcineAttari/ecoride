# EcoRide

Application web de covoiturage realisee dans le cadre de l'ECF Developpeur Web et Web Mobile.

## Stack prevue

- Frontend : HTML, CSS, JavaScript
- Backend : PHP avec PDO
- Base relationnelle : MySQL ou MariaDB
- Base NoSQL : MongoDB

## Etat actuel

Le prototype utilise maintenant MariaDB pour les principaux parcours :

- connexion et inscription utilisateur ;
- recherche et detail des covoiturages ;
- participation a un covoiturage ;
- annulation et remboursement d'une reservation ;
- ajout des vehicules et preferences chauffeur ;
- creation, annulation, demarrage et fin d'un covoiturage chauffeur ;
- validation passager apres trajet et creation d'un avis en attente ;
- signalement d'un probleme et creation d'un incident ;
- moderation des avis et suivi des incidents par l'employe ;
- creation d'employes, suspension de comptes et statistiques administrateur.

Certaines donnees de session restent conservees comme secours pour garder le prototype utilisable si la base locale n'est pas demarree.

## Lancer le projet en local

Depuis la racine du projet :

```bash
php -S localhost:8000 -t public
```

Puis ouvrir :

```txt
http://localhost:8000
```

Si la commande `php` n'est pas reconnue juste apres installation, fermer puis rouvrir le terminal VS Code.

Vous pouvez aussi lancer le serveur avec le script fourni :

```powershell
.\scripts\start-local.ps1
```

Pour demarrer la base MariaDB locale :

```powershell
.\scripts\start-database.ps1
```

## Pages utiles

```txt
http://localhost:8000
http://localhost:8000/covoiturages
http://localhost:8000/covoiturages?depart=Lyon&arrivee=Marseille&date=2026-06-05
http://localhost:8000/covoiturages/detail?id=1
http://localhost:8000/mon-espace
http://localhost:8000/employe
http://localhost:8000/admin
```

## Parcours de participation

1. Se connecter avec le compte `leo@example.com`.
2. Ouvrir `http://localhost:8000/covoiturages/detail?id=1`.
3. Cliquer sur `Participer`.
4. Cocher les deux confirmations.

## Parcours chauffeur

1. Se connecter avec `clara@example.com`.
2. Dans `Mon espace`, ajouter un vehicule ou utiliser un vehicule existant.
3. Remplir le formulaire `Saisir un voyage`.
4. Le trajet apparait ensuite dans la recherche des covoiturages.

## Parcours employe

1. Se connecter avec `employe@ecoride.local`.
2. Ouvrir `http://localhost:8000/employe`.
3. Moderer les avis en attente ou consulter les incidents ouverts.

## Parcours administrateur

1. Se connecter avec `admin@ecoride.local`.
2. Ouvrir `http://localhost:8000/admin`.
3. Creer un compte employe, suspendre un compte ou consulter les statistiques.

## Structure

```txt
Ecoride/
config/
database/
docs/
public/
  assets/
  index.php
src/
  Controller/
  Core/
  Repository/
  Service/
  View/
```

## Base de donnees

La documentation de preparation SQL se trouve dans :

```txt
docs/installation-base-de-donnees.md
```

## Comptes de demonstration

Compte utilisateur :

```txt
Email : clara@example.com
Mot de passe : Password123!
```

Compte employe :

```txt
Email : employe@ecoride.local
Mot de passe : Password123!
```

Compte administrateur :

```txt
Email : admin@ecoride.local
Mot de passe : Password123!
```

Les donnees SQL de demonstration sont aussi preparees dans `database/seed.sql`.
