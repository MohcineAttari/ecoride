<section class="section">
    <div class="container">
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
    </div>
</section>
