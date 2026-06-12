<section class="page-hero page-hero-compact">
    <div class="container">
        <p class="hero-kicker">Espace membre</p>
        <h1>Connexion</h1>
        <p>Retrouvez vos credits, vos reservations et vos trajets chauffeur en quelques secondes.</p>
    </div>
</section>

<section class="section section-raised">
    <div class="container auth-layout">
        <div class="auth-panel">
            <h1>Connexion</h1>
            <?php if (!empty($error)): ?>
                <p class="alert-error"><?= e($error) ?></p>
            <?php endif; ?>
            <form class="form-grid" action="/connexion" method="post">
                <label>
                    Email
                    <input type="email" name="email" autocomplete="email">
                </label>
                <label>
                    Mot de passe
                    <input type="password" name="password" autocomplete="current-password">
                </label>
                <button class="button" type="submit">Se connecter</button>
            </form>
            <p class="empty-state">Compte demo : clara@example.com / Password123!</p>
            <p>Pas encore de compte ? <a href="/inscription">Creer un compte</a></p>
        </div>

        <aside class="auth-aside">
            <h2>Avec EcoRide</h2>
            <ul class="check-list">
                <li>20 credits offerts a l'inscription</li>
                <li>Reservations et annulations suivies</li>
                <li>Avis moderes pour plus de confiance</li>
            </ul>
        </aside>
    </div>
</section>
