<?php $app = config('app'); ?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(($title ?? 'Accueil') . ' - ' . $app['app_name']) ?></title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
    <header class="site-header">
        <div class="container nav">
            <a class="brand" href="/">
                <span class="brand-mark">E</span>
                <span>EcoRide</span>
            </a>
            <nav class="nav-links" aria-label="Navigation principale">
                <a href="/">Accueil</a>
                <a href="/covoiturages">Covoiturages</a>
                <?php if (is_authenticated()): ?>
                    <?php if (in_array('ROLE_EMPLOYE', current_user()['roles'], true)): ?>
                        <a href="/employe">Espace employe</a>
                    <?php endif; ?>
                    <?php if (in_array('ROLE_ADMIN', current_user()['roles'], true)): ?>
                        <a href="/admin">Espace admin</a>
                    <?php endif; ?>
                    <a href="/mon-espace"><?= e(current_user()['pseudo']) ?> - <?= e((string) current_user()['credits']) ?> credits</a>
                    <a href="/deconnexion">Deconnexion</a>
                <?php else: ?>
                    <a href="/connexion">Connexion</a>
                <?php endif; ?>
                <a href="#contact">Contact</a>
            </nav>
        </div>
    </header>

    <main>
        <?php require $viewPath; ?>
    </main>

    <footer class="site-footer" id="contact">
        <div class="container footer-content">
            <span><?= e($app['app_name']) ?> - covoiturage responsable</span>
            <a href="mailto:<?= e($app['app_email']) ?>"><?= e($app['app_email']) ?></a>
            <a href="/mentions-legales">Mentions legales</a>
        </div>
    </footer>

    <script src="/assets/js/app.js"></script>
</body>
</html>
