<section class="page-hero page-hero-compact">
    <div class="container">
        <p class="hero-kicker">Pilotage EcoRide</p>
        <h1>Espace administrateur</h1>
        <p>Suivez les credits plateforme, les comptes utilisateurs et les statistiques de covoiturage.</p>
    </div>
</section>

<section class="section section-raised">
    <div class="container">
        <?php if (!empty($success)): ?>
            <p class="alert-success"><?= e($success) ?></p>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <p class="alert-error"><?= e($error) ?></p>
        <?php endif; ?>

        <div class="dashboard-grid">
            <article class="detail-card">
                <h2>Credits plateforme</h2>
                <p class="stat-number"><?= e((string) $stats['total_credits']) ?></p>
                <p class="empty-state">Total des credits gagnes par la plateforme.</p>
            </article>

            <article class="detail-card">
                <h2>Creer un employe</h2>
                <form class="form-grid" action="/admin/employes" method="post">
                    <label>
                        Pseudo
                        <input type="text" name="pseudo">
                    </label>
                    <label>
                        Email
                        <input type="email" name="email">
                    </label>
                    <label>
                        Mot de passe
                        <input type="password" name="password">
                    </label>
                    <button class="button" type="submit">Creer le compte</button>
                </form>
            </article>
        </div>

        <section class="reviews-section">
            <h2>Statistiques</h2>
            <div class="dashboard-grid">
                <article class="detail-card">
                    <h3>Covoiturages par jour</h3>
                    <canvas class="chart" data-chart='<?= e(json_encode($stats['rides_by_day'], JSON_THROW_ON_ERROR)) ?>'></canvas>
                </article>
                <article class="detail-card">
                    <h3>Credits gagnes par jour</h3>
                    <canvas class="chart" data-chart='<?= e(json_encode($stats['credits_by_day'], JSON_THROW_ON_ERROR)) ?>'></canvas>
                </article>
            </div>
        </section>

        <section class="reviews-section">
            <h2>Comptes</h2>
            <div class="mini-list">
                <?php foreach ($accounts as $account): ?>
                    <article>
                        <strong><?= e($account['pseudo']) ?></strong>
                        <p>
                            <?= e($account['email']) ?> -
                            <?= e(implode(', ', $account['roles'])) ?> -
                            Statut : <?= e($account['statut']) ?>
                        </p>
                        <?php if ($account['statut'] !== 'suspendu' && !in_array('ROLE_ADMIN', $account['roles'], true)): ?>
                            <form action="/admin/comptes/suspendre" method="post">
                                <input type="hidden" name="email" value="<?= e($account['email']) ?>">
                                <button class="button button-secondary" type="submit">Suspendre</button>
                            </form>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</section>
