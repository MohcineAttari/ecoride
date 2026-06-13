<section class="page-hero page-hero-compact">
    <div class="container">
        <p class="hero-kicker">Rejoindre EcoRide</p>
        <h1>Inscription</h1>
        <p>Creer un compte pour reserver vos trajets, devenir chauffeur et suivre vos credits.</p>
    </div>
</section>

<section class="section section-raised">
    <div class="container auth-layout">
        <div class="auth-panel">
            <h1>Inscription</h1>
            <?php if (!empty($error)): ?>
                <p class="alert-error"><?= e($error) ?></p>
            <?php endif; ?>
            <form class="form-grid" action="/inscription" method="post">
                <label>
                    Pseudo
                    <input type="text" name="pseudo" autocomplete="nickname">
                </label>
                <label>
                    Email
                    <input type="email" name="email" autocomplete="email">
                </label>
                <label>
                    Mot de passe
                    <input type="password" name="password" autocomplete="new-password">
                </label>
                <button class="button" type="submit">Creer mon compte</button>
            </form>
        </div>

        <aside class="auth-aside">
            <h2>Votre compte EcoRide</h2>
            <ul class="check-list">
                <li>20 credits disponibles des la creation</li>
                <li>Profil passager, chauffeur ou les deux</li>
                <li>Vehicules, preferences et historique centralises</li>
            </ul>
        </aside>
    </div>
</section>
