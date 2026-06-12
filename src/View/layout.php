<?php
$app = config('app');
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$navClass = static fn (string $path): string => $currentPath === $path ? 'nav-link is-active' : 'nav-link';
?>
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
                <a class="<?= e($navClass('/')) ?>" href="/">Accueil</a>
                <a class="<?= e($navClass('/covoiturages')) ?>" href="/covoiturages">Covoiturages</a>
                <?php if (is_authenticated()): ?>
                    <?php if (in_array('ROLE_EMPLOYE', current_user()['roles'], true)): ?>
                        <a class="<?= e($navClass('/employe')) ?>" href="/employe">Espace employe</a>
                    <?php endif; ?>
                    <?php if (in_array('ROLE_ADMIN', current_user()['roles'], true)): ?>
                        <a class="<?= e($navClass('/admin')) ?>" href="/admin">Espace admin</a>
                    <?php endif; ?>
                    <a class="<?= e($navClass('/mon-espace')) ?> nav-link-account" href="/mon-espace"><?= e(current_user()['pseudo']) ?> - <?= e((string) current_user()['credits']) ?> credits</a>
                    <a class="nav-link nav-link-muted" href="/deconnexion">Deconnexion</a>
                <?php else: ?>
                    <a class="nav-link nav-link-primary <?= $currentPath === '/connexion' ? 'is-active' : '' ?>" href="/connexion">Connexion</a>
                <?php endif; ?>
                <a class="<?= e($navClass('/contact')) ?> nav-link-contact" href="/contact">Contact</a>
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
