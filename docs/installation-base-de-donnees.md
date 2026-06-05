# Installation base de donnees - EcoRide

## Objectif

L'application EcoRide utilise une base relationnelle MariaDB via PDO pour les parcours principaux.

Les donnees de session restent presentes comme secours local, mais la base SQL est la source principale pour les utilisateurs, vehicules, covoiturages, reservations, avis, incidents, transactions et espaces employe/administrateur.

## Fichiers concernes

- `.env.example` : exemple de configuration locale.
- `config/database.php` : lit la configuration de connexion.
- `src/Core/Database.php` : cree la connexion PDO.
- `database/schema.sql` : cree la base et les tables.
- `database/seed.sql` : insere les donnees de demonstration.

## Configuration locale

Copier `.env.example` vers `.env`, puis adapter si besoin :

```txt
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecoride
DB_USERNAME=root
DB_PASSWORD=
```

## Import SQL prevu

Une fois MySQL ou MariaDB installe :

```bash
mysql -u root -p < database/schema.sql
mysql -u root -p ecoride < database/seed.sql
```

Sous PowerShell, vous pouvez aussi utiliser :

```powershell
& 'C:\Program Files\MariaDB 12.3\bin\mysql.exe' -u root -e "source C:/Users/Abrah/Ecoride/database/schema.sql"
& 'C:\Program Files\MariaDB 12.3\bin\mysql.exe' -u root -e "source C:/Users/Abrah/Ecoride/database/seed.sql"
```

## Demarrer la base locale

Un script est fourni :

```powershell
.\scripts\start-database.ps1
```

Le serveur PHP local peut ensuite etre lance avec :

```powershell
.\scripts\start-local.ps1
```

Puis ouvrir :

```txt
http://localhost:8000
```

## Comptes de test SQL

Tous les comptes de demonstration utilisent le mot de passe :

```txt
Password123!
```

Comptes :

- `admin@ecoride.local`
- `employe@ecoride.local`
- `clara@example.com`
- `leo@example.com`

## Remarque

Si PHP ne charge pas encore `pdo_mysql`, il faudra activer l'extension dans `php.ini` :

```ini
extension=pdo_mysql
```

Verification rapide :

```powershell
php -m
```

Le module `pdo_mysql` doit apparaitre dans la liste.
