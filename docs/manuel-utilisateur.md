# Manuel utilisateur - EcoRide

Ce manuel explique comment utiliser l'application EcoRide en local pour les differents profils prevus dans l'ECF.

## Acceder au site

1. Demarrer le serveur PHP.
2. Ouvrir le navigateur sur `http://localhost:8000`.
3. Utiliser le menu pour naviguer entre l'accueil, les covoiturages, la connexion et le contact.

## Visiteur non connecte

Un visiteur peut :

- consulter la page d'accueil ;
- rechercher un covoiturage ;
- filtrer les trajets par ville, date, prix, duree, note ou trajet ecologique ;
- consulter le detail d'un trajet ;
- consulter la page contact ;
- consulter les mentions legales ;
- creer un compte ;
- se connecter.

Un visiteur ne peut pas participer a un covoiturage sans connexion.

## Creation de compte

1. Ouvrir `/inscription`.
2. Renseigner un pseudo, un email et un mot de passe.
3. Valider le formulaire.

Regles principales :

- le mot de passe doit contenir au moins 8 caracteres ;
- le mot de passe doit contenir une majuscule, une minuscule et un chiffre ;
- un nouveau compte recoit 20 credits ;
- le profil par defaut est passager.

## Connexion

1. Ouvrir `/connexion`.
2. Saisir l'email et le mot de passe.
3. Valider le formulaire.

Comptes de demonstration :

| Profil | Email | Mot de passe |
| --- | --- | --- |
| Chauffeur | clara@example.com | Password123! |
| Passager | leo@example.com | Password123! |
| Employe | employe@ecoride.local | Password123! |
| Admin | admin@ecoride.local | Password123! |

## Passager

Un passager peut reserver un trajet avec ses credits.

### Rechercher un trajet

1. Ouvrir `/covoiturages`.
2. Saisir une ville de depart, une ville d'arrivee et une date.
3. Utiliser les filtres si besoin.
4. Cliquer sur `Detail`.

### Participer a un trajet

1. Se connecter en passager.
2. Ouvrir le detail d'un covoiturage.
3. Cliquer sur `Participer`.
4. Cocher les deux confirmations.
5. Valider.

Effets attendus :

- les credits du passager sont debites ;
- une reservation est creee ;
- le nombre de places restantes diminue ;
- la reservation apparait dans `Mon espace`.

### Annuler une reservation

1. Ouvrir `Mon espace`.
2. Dans `Mes reservations`, cliquer sur `Annuler`.

Effets attendus :

- la reservation passe en statut annulee ;
- les credits sont rembourses ;
- une place est rendue disponible.

### Valider ou signaler un trajet

Apres un trajet termine :

- le passager peut confirmer que tout s'est bien passe ;
- le passager peut laisser une note et un commentaire ;
- l'avis passe en attente de moderation ;
- en cas de probleme, le passager peut signaler un incident.

## Chauffeur

Un chauffeur peut gerer ses vehicules et proposer des covoiturages.

### Changer de profil

1. Ouvrir `Mon espace`.
2. Choisir `Chauffeur` ou `Passager et chauffeur`.
3. Cliquer sur `Mettre a jour`.

Le role est conserve en base de donnees.

### Ajouter un vehicule

1. Ouvrir `Mon espace`.
2. Aller dans `Vehicules et preferences`.
3. Remplir les informations du vehicule.
4. Renseigner les preferences chauffeur.
5. Cliquer sur `Enregistrer le vehicule`.

### Modifier un vehicule

1. Ouvrir `Mon espace`.
2. Dans `Mes vehicules`, ouvrir `Modifier ce vehicule`.
3. Corriger les informations.
4. Cliquer sur `Enregistrer les modifications`.

### Creer un covoiturage

1. Ouvrir `Mon espace`.
2. Aller dans `Saisir un voyage`.
3. Renseigner les villes, lieux, dates, prix et places.
4. Choisir un vehicule existant ou en proposer un nouveau.
5. Cliquer sur `Enregistrer le voyage`.

Le trajet apparait ensuite dans la recherche s'il est ouvert et dispose de places.

### Gerer le deroulement du trajet

Depuis `Mes voyages chauffeur`, le chauffeur peut :

- demarrer un trajet ouvert ;
- declarer l'arrivee a destination ;
- annuler un trajet ouvert.

Si le chauffeur annule un trajet, les participants sont rembourses.

## Employe

L'employe gere la moderation.

1. Se connecter avec `employe@ecoride.local`.
2. Ouvrir `/employe`.

L'employe peut :

- voir les avis en attente ;
- valider un avis ;
- refuser un avis ;
- consulter les incidents ouverts ;
- marquer un incident comme resolu.

## Administrateur

L'administrateur gere la plateforme.

1. Se connecter avec `admin@ecoride.local`.
2. Ouvrir `/admin`.

L'administrateur peut :

- consulter le total des credits gagnes par la plateforme ;
- consulter les graphiques de covoiturages et credits par jour ;
- creer un compte employe ;
- suspendre un compte utilisateur ou employe.

## Contact

1. Ouvrir `/contact`.
2. Remplir le formulaire.
3. Envoyer le message.

Le message est conserve en session dans le prototype et prepare pour une future collection NoSQL `contact_messages`.

## Deconnexion

Cliquer sur `Deconnexion` dans le menu pour fermer la session utilisateur.

## Notes de demonstration

- La base MariaDB est la source principale des donnees.
- Les donnees de session servent de secours si la base locale n'est pas disponible.
- Les mots de passe sont haches.
- Les donnees affichees dans les vues sont echappees avec la fonction `e()`.
