<section class="section">
    <div class="container">
        <h1 class="section-title">Contact</h1>

        <?php if (!empty($success)): ?>
            <p class="alert-success"><?= e($success) ?></p>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <p class="alert-error"><?= e($error) ?></p>
        <?php endif; ?>

        <div class="dashboard-grid">
            <article class="detail-card">
                <h2>Ecrire a EcoRide</h2>
                <form class="form-grid" action="/contact" method="post">
                    <label>
                        Nom
                        <input type="text" name="nom" placeholder="Votre nom">
                    </label>
                    <label>
                        Email
                        <input type="email" name="email" placeholder="vous@example.com">
                    </label>
                    <label>
                        Sujet
                        <input type="text" name="sujet" placeholder="Objet de votre message">
                    </label>
                    <label>
                        Message
                        <textarea name="message" rows="6" placeholder="Votre demande"></textarea>
                    </label>
                    <button class="button" type="submit">Envoyer le message</button>
                </form>
            </article>

            <article class="detail-card">
                <h2>Coordonnees</h2>
                <dl class="info-list">
                    <div>
                        <dt>Email</dt>
                        <dd><a href="mailto:contact@ecoride.local">contact@ecoride.local</a></dd>
                    </div>
                    <div>
                        <dt>Objet</dt>
                        <dd>Aide, compte, trajet ou incident</dd>
                    </div>
                    <div>
                        <dt>Reponse</dt>
                        <dd>Traitement par l equipe EcoRide</dd>
                    </div>
                </dl>
                <p class="empty-state">
                    Les messages de contact sont prepares pour une future collection NoSQL `contact_messages`.
                </p>
            </article>
        </div>
    </div>
</section>
