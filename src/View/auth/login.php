<section class="section">
    <div class="container">
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
    </div>
</section>
