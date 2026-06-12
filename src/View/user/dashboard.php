<section class="section">
    <div class="container">
        <h1 class="section-title">Mon espace</h1>

        <?php if (!empty($success)): ?>
            <p class="alert-success"><?= e($success) ?></p>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <p class="alert-error"><?= e($error) ?></p>
        <?php endif; ?>

        <div class="dashboard-grid">
            <article class="detail-card">
                <h2>Compte</h2>
                <dl class="info-list">
                    <div>
                        <dt>Pseudo</dt>
                        <dd><?= e($user['pseudo']) ?></dd>
                    </div>
                    <div>
                        <dt>Email</dt>
                        <dd><?= e($user['email']) ?></dd>
                    </div>
                    <div>
                        <dt>Credits</dt>
                        <dd><?= e((string) $user['credits']) ?></dd>
                    </div>
                </dl>
            </article>

            <article class="detail-card">
                <h2>Profil</h2>
                <ul class="tag-list">
                    <?php foreach ($user['roles'] as $role): ?>
                        <li><?= e($role) ?></li>
                    <?php endforeach; ?>
                </ul>
                <form class="form-grid inline-form" action="/mon-espace/profil" method="post">
                    <label>
                        Type de profil
                        <select name="profile">
                            <?php $profile = $user['profile'] ?? 'passager'; ?>
                            <option value="passager" <?= $profile === 'passager' ? 'selected' : '' ?>>Passager</option>
                            <option value="chauffeur" <?= $profile === 'chauffeur' ? 'selected' : '' ?>>Chauffeur</option>
                            <option value="passager_chauffeur" <?= $profile === 'passager_chauffeur' ? 'selected' : '' ?>>Passager et chauffeur</option>
                        </select>
                    </label>
                    <button class="button" type="submit">Mettre a jour</button>
                </form>
            </article>
        </div>

        <section class="reviews-section">
            <h2>Mes reservations</h2>

            <?php if ($reservations === []): ?>
                <p class="empty-state">Aucune reservation pour le moment.</p>
            <?php endif; ?>

            <div class="ride-list">
                <?php foreach ($reservations as $index => $reservation): ?>
                    <article class="ride-card">
                        <h2><?= e($reservation['ride_label']) ?></h2>
                        <p>
                            Reserve le <?= e($reservation['reserved_at']) ?>.
                            Credits utilises : <?= e((string) $reservation['credits_payes']) ?>.
                            Statut : <?= e($reservation['statut'] ?? 'confirmee') ?>.
                        </p>
                        <div class="action-row">
                            <a class="button" href="/covoiturages/detail?id=<?= e((string) $reservation['ride_id']) ?>">Voir le trajet</a>
                            <?php if (($reservation['statut'] ?? 'confirmee') !== 'annulee'): ?>
                                <form action="/mon-espace/reservation/annuler" method="post">
                                    <input type="hidden" name="index" value="<?= e((string) $index) ?>">
                                    <?php if (($reservation['source'] ?? '') === 'sql'): ?>
                                        <input type="hidden" name="reservation_id" value="<?= e((string) $reservation['id']) ?>">
                                    <?php endif; ?>
                                    <button class="button button-secondary" type="submit">Annuler</button>
                                </form>
                            <?php endif; ?>
                        </div>
                        <?php if (in_array(($reservation['statut'] ?? 'confirmee'), ['confirmee', 'terminee'], true)): ?>
                            <div class="validation-box">
                                <form action="/mon-espace/reservation/valider" method="post">
                                    <input type="hidden" name="index" value="<?= e((string) $index) ?>">
                                    <?php if (($reservation['source'] ?? '') === 'sql'): ?>
                                        <input type="hidden" name="reservation_id" value="<?= e((string) $reservation['id']) ?>">
                                    <?php endif; ?>
                                    <label>
                                        Note
                                        <input type="number" name="note" min="1" max="5" value="5">
                                    </label>
                                    <label>
                                        Avis
                                        <textarea name="commentaire" rows="3" placeholder="Votre avis sur le chauffeur"></textarea>
                                    </label>
                                    <button class="button" type="submit">Tout s'est bien passe</button>
                                </form>
                                <form class="form-grid" action="/mon-espace/reservation/signaler" method="post">
                                    <input type="hidden" name="index" value="<?= e((string) $index) ?>">
                                    <?php if (($reservation['source'] ?? '') === 'sql'): ?>
                                        <input type="hidden" name="reservation_id" value="<?= e((string) $reservation['id']) ?>">
                                    <?php endif; ?>
                                    <label>
                                        Signaler un probleme
                                        <textarea name="commentaire" rows="3" placeholder="Expliquez ce qui s'est passe"></textarea>
                                    </label>
                                    <button class="button button-secondary" type="submit">Signaler</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="reviews-section">
            <h2>Saisir un voyage</h2>
            <div class="dashboard-grid">
                <article class="detail-card">
                    <h3>Nouveau covoiturage</h3>
                    <form class="form-grid" action="/mon-espace/voyage" method="post">
                        <label>
                            Ville de depart
                            <input type="text" name="ville_depart" placeholder="Lyon">
                        </label>
                        <label>
                            Lieu de depart
                            <input type="text" name="lieu_depart" placeholder="Gare Part-Dieu">
                        </label>
                        <label>
                            Ville d'arrivee
                            <input type="text" name="ville_arrivee" placeholder="Marseille">
                        </label>
                        <label>
                            Lieu d'arrivee
                            <input type="text" name="lieu_arrivee" placeholder="Gare Saint-Charles">
                        </label>
                        <label>
                            Date et heure de depart
                            <input type="datetime-local" name="date_depart">
                        </label>
                        <label>
                            Date et heure d'arrivee
                            <input type="datetime-local" name="date_arrivee">
                        </label>
                        <label>
                            Prix par passager en credits
                            <input type="number" name="prix_personne" min="1" value="5">
                        </label>
                        <label>
                            Nombre de places proposees
                            <input type="number" name="nb_places" min="1" max="8" value="1">
                        </label>

                        <fieldset class="fieldset">
                            <legend>Vehicule du voyage</legend>
                            <label class="checkbox-line">
                                <input type="radio" name="vehicle_choice" value="existing" checked>
                                Choisir un vehicule existant
                            </label>
                            <label>
                                Vehicule existant
                                <select name="vehicle_index">
                                    <option value="">Selectionner</option>
                                    <?php foreach ($vehicles as $index => $vehicle): ?>
                                        <option value="<?= e((string) $index) ?>">
                                            <?= e($vehicle['marque']) ?> <?= e($vehicle['modele']) ?> - <?= e($vehicle['immatriculation']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </label>

                            <label class="checkbox-line">
                                <input type="radio" name="vehicle_choice" value="new">
                                Proposer un nouveau vehicule
                            </label>
                            <div class="nested-grid">
                                <input type="text" name="new_immatriculation" placeholder="Plaque">
                                <input type="date" name="new_date_premiere_immatriculation">
                                <input type="text" name="new_marque" placeholder="Marque">
                                <input type="text" name="new_modele" placeholder="Modele">
                                <input type="text" name="new_couleur" placeholder="Couleur">
                                <select name="new_energie">
                                    <option value="electrique">Electrique</option>
                                    <option value="hybride">Hybride</option>
                                    <option value="essence">Essence</option>
                                    <option value="diesel">Diesel</option>
                                </select>
                                <input type="number" name="new_nb_places" min="1" max="8" placeholder="Places">
                            </div>
                        </fieldset>

                        <p class="empty-state">EcoRide preleve 2 credits par covoiturage pour garantir le fonctionnement de la plateforme.</p>
                        <button class="button" type="submit">Enregistrer le voyage</button>
                    </form>
                </article>

                <article class="detail-card">
                    <h3>Mes voyages chauffeur</h3>
                    <?php if ($driverRides === []): ?>
                        <p class="empty-state">Aucun voyage cree pour le moment.</p>
                    <?php endif; ?>

                    <div class="mini-list">
                        <?php foreach ($driverRides as $index => $ride): ?>
                            <article>
                                <strong><?= e($ride['ville_depart']) ?> vers <?= e($ride['ville_arrivee']) ?></strong>
                                <p>
                                    Depart : <?= e($ride['date_depart']) ?> -
                                    Prix : <?= e((string) $ride['prix_personne']) ?> credits
                                    dont 2 credits plateforme.
                                </p>
                                <p>
                                    Vehicule : <?= e($ride['vehicule']['marque']) ?> <?= e($ride['vehicule']['modele']) ?> -
                                    <?= e((string) $ride['nb_places']) ?> place(s) -
                                    Statut : <?= e($ride['statut']) ?>
                                </p>
                                <?php if (!empty($ride['notification'])): ?>
                                    <p><?= e($ride['notification']) ?></p>
                                <?php endif; ?>
                                <?php if ($ride['statut'] !== 'annule'): ?>
                                    <div class="action-row">
                                        <?php if ($ride['statut'] === 'ouvert'): ?>
                                            <form action="/mon-espace/voyage/demarrer" method="post">
                                                <input type="hidden" name="index" value="<?= e((string) $index) ?>">
                                                <?php if (($ride['source'] ?? '') === 'sql'): ?>
                                                    <input type="hidden" name="ride_id" value="<?= e((string) $ride['id']) ?>">
                                                <?php endif; ?>
                                                <button class="button" type="submit">Demarrer</button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if ($ride['statut'] === 'demarre'): ?>
                                            <form action="/mon-espace/voyage/arrivee" method="post">
                                                <input type="hidden" name="index" value="<?= e((string) $index) ?>">
                                                <?php if (($ride['source'] ?? '') === 'sql'): ?>
                                                    <input type="hidden" name="ride_id" value="<?= e((string) $ride['id']) ?>">
                                                <?php endif; ?>
                                                <button class="button" type="submit">Arrivee a destination</button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if ($ride['statut'] === 'ouvert'): ?>
                                            <form action="/mon-espace/voyage/annuler" method="post">
                                                <input type="hidden" name="index" value="<?= e((string) $index) ?>">
                                                <?php if (($ride['source'] ?? '') === 'sql'): ?>
                                                    <input type="hidden" name="ride_id" value="<?= e((string) $ride['id']) ?>">
                                                <?php endif; ?>
                                                <button class="button button-secondary" type="submit">Annuler ce voyage</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </article>
            </div>
        </section>

        <section class="reviews-section">
            <h2>Vehicules et preferences</h2>
            <div class="dashboard-grid">
                <article class="detail-card">
                    <h3>Ajouter un vehicule</h3>
                    <form class="form-grid" action="/mon-espace/vehicule" method="post">
                        <label>
                            Plaque d'immatriculation
                            <input type="text" name="immatriculation" placeholder="AA-123-AA">
                        </label>
                        <label>
                            Date de premiere immatriculation
                            <input type="date" name="date_premiere_immatriculation">
                        </label>
                        <label>
                            Marque
                            <input type="text" name="marque" placeholder="Renault">
                        </label>
                        <label>
                            Modele
                            <input type="text" name="modele" placeholder="Zoe">
                        </label>
                        <label>
                            Couleur
                            <input type="text" name="couleur" placeholder="Blanc">
                        </label>
                        <label>
                            Energie
                            <select name="energie">
                                <option value="electrique">Electrique</option>
                                <option value="hybride">Hybride</option>
                                <option value="essence">Essence</option>
                                <option value="diesel">Diesel</option>
                            </select>
                        </label>
                        <label>
                            Nombre de places disponibles
                            <input type="number" name="nb_places" min="1" max="8" value="1">
                        </label>
                        <label class="checkbox-line">
                            <input type="checkbox" name="fumeur" value="1">
                            Fumeur accepte
                        </label>
                        <label class="checkbox-line">
                            <input type="checkbox" name="animaux" value="1">
                            Animaux acceptes
                        </label>
                        <label>
                            Preference personnalisee
                            <input type="text" name="preference_custom" placeholder="Ex. discussion acceptee">
                        </label>
                        <button class="button" type="submit">Enregistrer le vehicule</button>
                    </form>
                </article>

                <article class="detail-card">
                    <h3>Mes vehicules</h3>
                    <?php if ($vehicles === []): ?>
                        <p class="empty-state">Aucun vehicule enregistre.</p>
                    <?php endif; ?>

                    <div class="mini-list">
                        <?php foreach ($vehicles as $vehicleIndex => $vehicle): ?>
                            <article>
                                <strong><?= e($vehicle['marque']) ?> <?= e($vehicle['modele']) ?></strong>
                                <p>
                                    <?= e($vehicle['immatriculation']) ?> -
                                    <?= e($vehicle['couleur']) ?> -
                                    <?= e($vehicle['energie']) ?> -
                                    <?= e((string) $vehicle['nb_places']) ?> place(s)
                                </p>
                                <details class="inline-form">
                                    <summary>Modifier ce vehicule</summary>
                                    <form class="form-grid inline-form" action="/mon-espace/vehicule/modifier" method="post">
                                        <input type="hidden" name="vehicle_index" value="<?= e((string) $vehicleIndex) ?>">
                                        <?php if (($vehicle['source'] ?? '') === 'sql'): ?>
                                            <input type="hidden" name="vehicle_id" value="<?= e((string) $vehicle['id']) ?>">
                                        <?php endif; ?>
                                        <label>
                                            Plaque d'immatriculation
                                            <input type="text" name="immatriculation" value="<?= e($vehicle['immatriculation']) ?>">
                                        </label>
                                        <label>
                                            Date de premiere immatriculation
                                            <input type="date" name="date_premiere_immatriculation" value="<?= e($vehicle['date_premiere_immatriculation']) ?>">
                                        </label>
                                        <label>
                                            Marque
                                            <input type="text" name="marque" value="<?= e($vehicle['marque']) ?>">
                                        </label>
                                        <label>
                                            Modele
                                            <input type="text" name="modele" value="<?= e($vehicle['modele']) ?>">
                                        </label>
                                        <label>
                                            Couleur
                                            <input type="text" name="couleur" value="<?= e($vehicle['couleur']) ?>">
                                        </label>
                                        <label>
                                            Energie
                                            <select name="energie">
                                                <?php foreach (['electrique', 'hybride', 'essence', 'diesel'] as $energy): ?>
                                                    <option value="<?= e($energy) ?>" <?= $vehicle['energie'] === $energy ? 'selected' : '' ?>><?= e(ucfirst($energy)) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </label>
                                        <label>
                                            Nombre de places disponibles
                                            <input type="number" name="nb_places" min="1" max="8" value="<?= e((string) $vehicle['nb_places']) ?>">
                                        </label>
                                        <button class="button" type="submit">Enregistrer les modifications</button>
                                    </form>
                                </details>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <h3>Mes preferences</h3>
                    <?php if ($preferences === []): ?>
                        <p class="empty-state">Aucune preference enregistree.</p>
                    <?php else: ?>
                        <ul class="tag-list">
                            <?php foreach ($preferences as $preference): ?>
                                <li><?= e($preference) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </article>
            </div>
        </section>

        <?php if ($incidents !== []): ?>
            <section class="reviews-section">
                <h2>Signalements en attente</h2>
                <div class="mini-list">
                    <?php foreach ($incidents as $incident): ?>
                        <article>
                            <strong><?= e($incident['ride_label']) ?></strong>
                            <p><?= e($incident['commentaire']) ?></p>
                            <p>Statut : <?= e($incident['statut']) ?> - cree le <?= e($incident['created_at']) ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </div>
</section>
