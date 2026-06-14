# Deploiement et preparation du rendu - EcoRide

Ce document decrit les etapes pour preparer EcoRide avant le rendu de l'ECF.

## Prerequis

- PHP 8.4 ou version compatible avec PDO MySQL
- MariaDB ou MySQL
- Git
- Navigateur recent
- Acces au depot GitHub du projet

## Variables d'environnement

Le projet utilise un fichier `.env` local non versionne.

1. Copier `.env.example` vers `.env`.
2. Adapter les valeurs selon l'environnement.

Exemple local :

```txt
APP_NAME=EcoRide
APP_EMAIL=contact@ecoride.local
APP_URL=http://localhost:8000

DB_DRIVER=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecoride
DB_USERNAME=root
DB_PASSWORD=
DB_CHARSET=utf8mb4
```

Important :

- ne pas versionner `.env` ;
- ne pas versionner `mariadb-data/` ;
- conserver uniquement `.env.example` dans Git.

## Installation de la base de donnees

1. Demarrer MariaDB.
2. Executer le script de schema :

```bash
mysql -u root < database/schema.sql
```

3. Charger les donnees de demonstration :

```bash
mysql -u root < database/seed.sql
```

La documentation detaillee est disponible dans :

```txt
docs/installation-base-de-donnees.md
```

## Lancement en local

Depuis la racine du projet :

```bash
php -S localhost:8000 -t public
```

Puis ouvrir :

```txt
http://localhost:8000
```

Scripts utiles sous Windows :

```powershell
.\scripts\start-database.ps1
.\scripts\start-local.ps1
```

## Verification fonctionnelle

Avant le rendu, executer les parcours principaux :

- accueil et recherche de covoiturage ;
- connexion passager ;
- participation a un trajet ;
- annulation de reservation ;
- espace chauffeur ;
- creation et modification de vehicule ;
- creation de trajet ;
- espace employe ;
- moderation d'avis ;
- suivi incident ;
- espace admin ;
- creation employe ;
- suspension de compte ;
- page contact ;
- page mentions legales ;
- page 404.

Le plan detaille est disponible dans :

```txt
docs/plan-tests.md
```

## Branches Git

Organisation recommandee :

- `develop` : branche de travail ;
- `main` : branche stable pour le rendu final.

Avant le rendu final :

1. Verifier que `develop` est propre.
2. Verifier que les tests principaux sont valides.
3. Fusionner `develop` dans `main`.
4. Pousser `main` sur GitHub.

Commandes indicatives :

```bash
git switch main
git merge develop
git push origin main
```

## Checklist avant rendu

- Le site demarre en local.
- La base de donnees est recreable avec `schema.sql` et `seed.sql`.
- Le fichier `.env.example` est present.
- Le fichier `.env` n'est pas versionne.
- Le dossier `mariadb-data/` n'est pas versionne.
- Le README explique comment lancer le projet.
- Le MCD est present.
- Le dictionnaire de donnees est present.
- Le plan de tests est present.
- Le manuel utilisateur est present.
- Les comptes de demonstration sont documentes.
- Les dernieres modifications sont poussees sur GitHub.
- La branche `main` contient la version stable finale.

## Limites du prototype

- Les messages de contact sont conserves en session dans le prototype.
- Les donnees NoSQL sont documentees comme extension prevue.
- Les notifications email sont simulees par des messages applicatifs.
- Le serveur PHP integre est utilise pour la demonstration locale.
