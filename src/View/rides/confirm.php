<section class="section">
    <div class="container">
        <a class="back-link" href="/covoiturages/detail?id=<?= e((string) $ride['id']) ?>">Retour au detail</a>

        <div class="auth-panel">
            <h1>Confirmer la participation</h1>

            <?php if (!empty($error)): ?>
                <p class="alert-error"><?= e($error) ?></p>
            <?php endif; ?>

            <p>
                Vous allez reserver une place pour le trajet
                <strong><?= e($ride['departure_city']) ?> vers <?= e($ride['arrival_city']) ?></strong>.
            </p>

            <dl class="info-list">
                <div>
                    <dt>Depart</dt>
                    <dd><?= e($ride['departure_at']) ?></dd>
                </div>
                <div>
                    <dt>Prix</dt>
                    <dd><?= e((string) $ride['price']) ?> credits</dd>
                </div>
                <div>
                    <dt>Votre solde</dt>
                    <dd><?= e((string) current_user()['credits']) ?> credits</dd>
                </div>
            </dl>

            <form class="form-grid" action="/covoiturages/participer" method="post">
                <input type="hidden" name="id" value="<?= e((string) $ride['id']) ?>">

                <label class="checkbox-line">
                    <input type="checkbox" name="confirmation_credits" value="1">
                    Je confirme utiliser <?= e((string) $ride['price']) ?> credits pour ce covoiturage.
                </label>

                <label class="checkbox-line">
                    <input type="checkbox" name="confirmation_conditions" value="1">
                    Je confirme vouloir participer a ce trajet.
                </label>

                <button class="button" type="submit">Confirmer ma participation</button>
            </form>
        </div>
    </div>
</section>
