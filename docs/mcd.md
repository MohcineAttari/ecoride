# MCD corrige - EcoRide

## Objectif du modele

Le schema fourni dans l'annexe sert de base, mais il ne couvre pas toutes les regles metier de l'application EcoRide.

Le modele corrige doit permettre de gerer :

- les utilisateurs et leurs roles ;
- les vehicules des chauffeurs ;
- les covoiturages ;
- les reservations ;
- les credits ;
- les avis ;
- les preferences chauffeur ;
- les incidents de trajet ;
- les comptes employes et administrateur ;
- les statistiques de credits gagnes par la plateforme.

## Entites principales

### Utilisateur

Represente tous les comptes de l'application : utilisateur classique, employe ou administrateur.

Un utilisateur peut avoir plusieurs roles.

Exemples de roles :

- `ROLE_USER`
- `ROLE_PASSAGER`
- `ROLE_CHAUFFEUR`
- `ROLE_EMPLOYE`
- `ROLE_ADMIN`

### Role

Represente les droits ou profils associes a un utilisateur.

Un role peut appartenir a plusieurs utilisateurs.

Un utilisateur peut posseder plusieurs roles.

### Vehicule

Represente une voiture appartenant a un chauffeur.

Un utilisateur chauffeur peut avoir plusieurs vehicules.

Un vehicule appartient a un seul utilisateur.

Un vehicule possede une marque et une energie.

### Marque

Represente la marque d'un vehicule.

Exemples :

- Renault
- Peugeot
- Tesla
- Toyota

### Energie

Represente le type d'energie du vehicule.

Exemples :

- electrique
- essence
- diesel
- hybride

Un covoiturage est considere ecologique si le vehicule utilise possede une energie electrique.

### Preference

Represente une preference possible du chauffeur.

Exemples :

- fumeur accepte
- animaux acceptes
- musique acceptee
- discussion acceptee

Un chauffeur peut associer plusieurs preferences a son profil.

### Covoiturage

Represente un trajet propose par un chauffeur.

Un covoiturage est cree par un utilisateur chauffeur.

Un covoiturage utilise un vehicule.

Un covoiturage peut avoir plusieurs reservations.

Un covoiturage possede un statut.

Exemples de statuts :

- `ouvert`
- `complet`
- `annule`
- `demarre`
- `termine`
- `litige`

### Reservation

Represente la participation d'un passager a un covoiturage.

Une reservation appartient a un utilisateur passager.

Une reservation concerne un seul covoiturage.

Une reservation possede un statut.

Exemples de statuts :

- `confirmee`
- `annulee`
- `terminee`
- `validee`
- `probleme`

### Avis

Represente un avis depose par un passager apres un trajet.

Un avis est lie a une reservation.

Un avis concerne un chauffeur.

Un avis est visible uniquement s'il est valide par un employe.

Exemples de statuts :

- `en_attente`
- `valide`
- `refuse`

### Incident

Represente un signalement lorsqu'un trajet s'est mal passe.

Un incident est lie a une reservation et a un covoiturage.

Un employe peut traiter l'incident.

Exemples de statuts :

- `ouvert`
- `en_cours`
- `resolu`
- `rejete`

### TransactionCredit

Represente un mouvement de credits.

Elle permet de tracer :

- le paiement d'une reservation ;
- le remboursement d'une annulation ;
- le versement au chauffeur ;
- le remboursement apres annulation d'un covoiturage.

Les gains de la plateforme correspondent a 2 credits par reservation validee.

## Associations principales

### Utilisateur - Role

Relation plusieurs a plusieurs.

- Un utilisateur possede un ou plusieurs roles.
- Un role peut etre attribue a plusieurs utilisateurs.

Table d'association : `utilisateur_role`.

### Utilisateur - Vehicule

Relation un a plusieurs.

- Un chauffeur peut posseder plusieurs vehicules.
- Un vehicule appartient a un seul chauffeur.

### Vehicule - Marque

Relation plusieurs a un.

- Un vehicule possede une marque.
- Une marque peut concerner plusieurs vehicules.

### Vehicule - Energie

Relation plusieurs a un.

- Un vehicule possede une energie.
- Une energie peut concerner plusieurs vehicules.

### Utilisateur - Preference

Relation plusieurs a plusieurs.

- Un chauffeur peut avoir plusieurs preferences.
- Une preference peut etre associee a plusieurs chauffeurs.

Table d'association : `utilisateur_preference`.

### Utilisateur - Covoiturage

Relation un a plusieurs.

- Un chauffeur peut creer plusieurs covoiturages.
- Un covoiturage est cree par un seul chauffeur.

### Vehicule - Covoiturage

Relation un a plusieurs.

- Un vehicule peut etre utilise pour plusieurs covoiturages.
- Un covoiturage utilise un seul vehicule.

### Covoiturage - Reservation

Relation un a plusieurs.

- Un covoiturage peut recevoir plusieurs reservations.
- Une reservation concerne un seul covoiturage.

### Utilisateur - Reservation

Relation un a plusieurs.

- Un passager peut effectuer plusieurs reservations.
- Une reservation appartient a un seul passager.

### Reservation - Avis

Relation zero ou un a un.

- Une reservation terminee peut donner lieu a un avis.
- Un avis concerne une seule reservation.

### Reservation - Incident

Relation zero ou un a un.

- Une reservation peut donner lieu a un incident.
- Un incident concerne une seule reservation.

### Utilisateur - TransactionCredit

Relation un a plusieurs.

- Un utilisateur peut avoir plusieurs mouvements de credits.
- Une transaction peut concerner un utilisateur source et/ou un utilisateur cible.

## Regles de gestion liees au modele

- Le compte administrateur est cree en amont.
- Un compte peut etre suspendu.
- Un utilisateur recoit 20 credits a la creation de son compte.
- Un passager doit posseder assez de credits pour reserver.
- Une reservation debite le passager.
- La plateforme garde 2 credits par covoiturage.
- Le chauffeur recoit ses credits apres validation du trajet.
- Le chauffeur recoit le prix paye par le passager moins 2 credits de commission plateforme.
- Une reservation diminue le nombre de places restantes.
- Une annulation doit mettre a jour les credits et les places.
- Un avis n'est visible qu'apres validation par un employe.
- Un trajet ecologique est determine par l'energie du vehicule.

## Tables relationnelles prevues

- `utilisateur`
- `role`
- `utilisateur_role`
- `marque`
- `energie`
- `vehicule`
- `preference`
- `utilisateur_preference`
- `covoiturage`
- `reservation`
- `avis`
- `incident`
- `transaction_credit`

## Donnees NoSQL prevues

La base NoSQL peut etre utilisee pour stocker des donnees moins structurees, par exemple :

- logs applicatifs ;
- historique d'actions utilisateur ;
- details de traitement des incidents ;
- messages de contact ;
- traces de moderation.

Collection MongoDB proposee :

- `activity_logs`
- `contact_messages`
- `incident_notes`
