<section class="section">
    <div class="container">
        <h1 class="section-title">Covoiturages</h1>

        <div class="rides-layout">
            <aside class="filters">
                <h2>Filtres</h2>
                <form class="form-grid" action="/covoiturages" method="get">
                    <label>
                        Depart
                        <input type="text" name="depart" value="<?= e($filters['depart'] ?? '') ?>">
                    </label>
                    <label>
                        Arrivee
                        <input type="text" name="arrivee" value="<?= e($filters['arrivee'] ?? '') ?>">
                    </label>
                    <label>
                        Date
                        <input type="date" name="date" value="<?= e($filters['date'] ?? '') ?>">
                    </label>
                    <label>
                        Prix maximum
                        <input type="number" name="prix_max" min="0" placeholder="Credits" value="<?= e((string) ($filters['prix_max'] ?? '')) ?>">
                    </label>
                    <label>
                        Duree maximum
                        <input type="number" name="duree_max" min="0" placeholder="Minutes" value="<?= e((string) ($filters['duree_max'] ?? '')) ?>">
                    </label>
                    <label>
                        Note minimale
                        <input type="number" name="note_min" min="1" max="5" step="0.1" value="<?= e((string) ($filters['note_min'] ?? '')) ?>">
                    </label>
                    <label class="checkbox-line">
                        <input type="checkbox" name="ecologique" value="1" <?= ($filters['ecologique'] ?? false) ? 'checked' : '' ?>>
                        Trajet ecologique
                    </label>
                    <button class="button" type="submit">Filtrer</button>
                </form>
            </aside>

            <div class="ride-list">
                <?php if ($rides === []): ?>
                    <p class="empty-state">
                        Aucun covoiturage ne correspond a votre recherche.
                        <?php if ($closestDate !== null): ?>
                            Le premier trajet proche est disponible le <?= e($closestDate) ?>.
                        <?php endif; ?>
                    </p>
                <?php endif; ?>

                <?php foreach ($rides as $ride): ?>
                    <article class="ride-card">
                        <div class="ride-card-header">
                            <img class="driver-photo" src="<?= e($ride['driver_photo']) ?>" alt="">
                            <div>
                                <h2><?= e($ride['departure_city']) ?> vers <?= e($ride['arrival_city']) ?></h2>
                                <p>Chauffeur : <?= e($ride['driver_pseudo']) ?>, note <?= e((string) $ride['driver_rating']) ?>/5</p>
                            </div>
                            <strong class="ride-price"><?= e((string) $ride['price']) ?> credits</strong>
                        </div>

                        <div class="ride-meta">
                            <span><?= e($ride['departure_at']) ?> - <?= e($ride['arrival_at']) ?></span>
                            <span><?= e((string) $ride['available_seats']) ?> place(s)</span>
                            <span><?= e($ride['vehicle_brand']) ?> <?= e($ride['vehicle_model']) ?></span>
                        </div>

                        <p>
                            Depart : <?= e($ride['departure_place']) ?>.
                            Arrivee : <?= e($ride['arrival_place']) ?>.
                        </p>

                        <?php if ($ride['is_ecological']): ?>
                            <span class="badge">Voyage ecologique</span>
                        <?php else: ?>
                            <span class="badge badge-muted">Voyage classique</span>
                        <?php endif; ?>

                        <a class="button" href="/covoiturages/detail?id=<?= e((string) $ride['id']) ?>">Detail</a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
