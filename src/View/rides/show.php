<section class="page-hero page-hero-compact">
    <div class="container">
        <p class="hero-kicker"><?= $ride['is_ecological'] ? 'Voyage ecologique' : 'Voyage classique' ?></p>
        <h1><?= e($ride['departure_city']) ?> vers <?= e($ride['arrival_city']) ?></h1>
        <p><?= e($ride['departure_place']) ?> vers <?= e($ride['arrival_place']) ?></p>
    </div>
</section>

<section class="section section-raised">
    <div class="container">
        <a class="back-link" href="/covoiturages">Retour aux covoiturages</a>

        <?php if (!empty($_SESSION['flash_success'])): ?>
            <p class="alert-success"><?= e($_SESSION['flash_success']) ?></p>
            <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['flash_error'])): ?>
            <p class="alert-error"><?= e($_SESSION['flash_error']) ?></p>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>

        <div class="detail-header">
            <div>
                <span class="badge <?= $ride['is_ecological'] ? '' : 'badge-muted' ?>">
                    <?= $ride['is_ecological'] ? 'Voyage ecologique' : 'Voyage classique' ?>
                </span>
                <h2 class="section-title"><?= e((string) $ride['price']) ?> credits par passager</h2>
                <p class="empty-state"><?= e((string) $ride['available_seats']) ?> place(s) encore disponible(s).</p>
            </div>
            <?php if (is_authenticated()): ?>
                <a class="button" href="/covoiturages/participer?id=<?= e((string) $ride['id']) ?>">Participer</a>
            <?php else: ?>
                <a class="button" href="/connexion">Se connecter pour participer</a>
            <?php endif; ?>
        </div>

        <div class="detail-layout">
            <article class="detail-card">
                <h2>Informations du trajet</h2>
                <dl class="info-list">
                    <div>
                        <dt>Depart</dt>
                        <dd><?= e($ride['departure_at']) ?></dd>
                    </div>
                    <div>
                        <dt>Arrivee</dt>
                        <dd><?= e($ride['arrival_at']) ?></dd>
                    </div>
                    <div>
                        <dt>Places restantes</dt>
                        <dd><?= e((string) $ride['available_seats']) ?></dd>
                    </div>
                    <div>
                        <dt>Prix</dt>
                        <dd><?= e((string) $ride['price']) ?> credits</dd>
                    </div>
                </dl>
            </article>

            <article class="detail-card">
                <h2>Conducteur</h2>
                <div class="ride-card-header">
                    <img class="driver-photo" src="<?= e($ride['driver_photo']) ?>" alt="">
                    <div>
                        <strong><?= e($ride['driver_pseudo']) ?></strong>
                        <p class="empty-state">Note moyenne : <?= e((string) $ride['driver_rating']) ?>/5</p>
                    </div>
                </div>
            </article>

            <article class="detail-card">
                <h2>Vehicule</h2>
                <dl class="info-list">
                    <div>
                        <dt>Marque</dt>
                        <dd><?= e($ride['vehicle_brand']) ?></dd>
                    </div>
                    <div>
                        <dt>Modele</dt>
                        <dd><?= e($ride['vehicle_model']) ?></dd>
                    </div>
                    <div>
                        <dt>Energie</dt>
                        <dd><?= e($ride['energy']) ?></dd>
                    </div>
                </dl>
            </article>

            <article class="detail-card">
                <h2>Preferences</h2>
                <ul class="tag-list">
                    <?php foreach ($ride['preferences'] as $preference): ?>
                        <li><?= e($preference) ?></li>
                    <?php endforeach; ?>
                </ul>
            </article>
        </div>

        <section class="reviews-section">
            <h2>Avis du conducteur</h2>

            <?php if ($ride['reviews'] === []): ?>
                <p class="empty-state">Aucun avis valide pour ce conducteur pour le moment.</p>
            <?php endif; ?>

            <div class="review-list">
                <?php foreach ($ride['reviews'] as $review): ?>
                    <article class="review-card">
                        <strong><?= e($review['author']) ?> - <?= e((string) $review['rating']) ?>/5</strong>
                        <p><?= e($review['comment']) ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</section>
