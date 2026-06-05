<section class="section">
    <div class="container">
        <h1 class="section-title">Espace employe</h1>

        <?php if (!empty($success)): ?>
            <p class="alert-success"><?= e($success) ?></p>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <p class="alert-error"><?= e($error) ?></p>
        <?php endif; ?>

        <section class="reviews-section">
            <h2>Avis a moderer</h2>

            <?php if ($reviews === []): ?>
                <p class="empty-state">Aucun avis en attente.</p>
            <?php endif; ?>

            <div class="mini-list">
                <?php foreach ($reviews as $index => $review): ?>
                    <article>
                        <strong><?= e($review['ride_label']) ?> - <?= e((string) $review['note']) ?>/5</strong>
                        <p><?= e($review['commentaire']) ?></p>
                        <p>
                            Passager : <?= e($review['passager_pseudo']) ?> -
                            Chauffeur : <?= e($review['chauffeur_pseudo']) ?> -
                            Statut : <?= e($review['statut']) ?>
                        </p>
                        <?php if ($review['statut'] === 'en_attente'): ?>
                            <div class="action-row">
                                <form action="/employe/avis/valider" method="post">
                                    <input type="hidden" name="index" value="<?= e((string) $index) ?>">
                                    <?php if (($review['source'] ?? '') === 'sql'): ?>
                                        <input type="hidden" name="review_id" value="<?= e((string) $review['id']) ?>">
                                    <?php endif; ?>
                                    <button class="button" type="submit">Valider</button>
                                </form>
                                <form action="/employe/avis/refuser" method="post">
                                    <input type="hidden" name="index" value="<?= e((string) $index) ?>">
                                    <?php if (($review['source'] ?? '') === 'sql'): ?>
                                        <input type="hidden" name="review_id" value="<?= e((string) $review['id']) ?>">
                                    <?php endif; ?>
                                    <button class="button button-secondary" type="submit">Refuser</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="reviews-section">
            <h2>Covoiturages problematiques</h2>

            <?php if ($incidents === []): ?>
                <p class="empty-state">Aucun trajet signale.</p>
            <?php endif; ?>

            <div class="mini-list">
                <?php foreach ($incidents as $incident): ?>
                    <article>
                        <strong><?= e($incident['ride_label']) ?></strong>
                        <p><?= e($incident['commentaire']) ?></p>
                        <p>
                            Numero reservation : <?= e((string) $incident['reservation_index']) ?> -
                            Passager : <?= e($incident['passager_pseudo'] ?? 'Utilisateur') ?> -
                            Email : <?= e($incident['passager_email'] ?? 'non renseigne') ?> -
                            Statut : <?= e($incident['statut']) ?>
                        </p>
                        <p>Cree le <?= e($incident['created_at']) ?></p>
                        <?php if (($incident['source'] ?? '') === 'sql' && $incident['statut'] === 'ouvert'): ?>
                            <form action="/employe/incidents/resoudre" method="post">
                                <input type="hidden" name="incident_id" value="<?= e((string) $incident['id']) ?>">
                                <button class="button" type="submit">Marquer comme resolu</button>
                            </form>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</section>
