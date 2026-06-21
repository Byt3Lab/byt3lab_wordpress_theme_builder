<div class="wrap">
    <h1>BYT3LAB Builder - Pages</h1>
    <?= $message ?? '' ?>

    <div style="display: flex; gap: 20px;">
        <div style="flex: 1; max-width: 400px;">
            <div class="card" style="max-width: 100%; margin-top: 0;">
                <h2>Créer une nouvelle page</h2>
                <form method="POST" action="">
                    <?php wp_nonce_field('generate_page_nonce'); ?>
                    <input type="hidden" name="generate_page" value="1">
                    
                    <p>
                        <label>Sélectionner le thème :</label><br>
                        <select name="theme_slug" required class="regular-text">
                            <option value="">-- Choisir un thème --</option>
                            <?php foreach ($builderThemes as $slug => $theme): ?>
                                <option value="<?= esc_attr($slug) ?>"><?= esc_html($theme->get('Name')) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                    <p>
                        <label>Titre de la page (ex: About) :</label><br>
                        <input type="text" name="page_title" required class="regular-text">
                    </p>
                    <p>
                        <button type="submit" class="button button-primary">Générer la page</button>
                    </p>
                </form>
            </div>
        </div>

        <div style="flex: 2;">
            <div class="card" style="max-width: 100%; margin-top: 0;">
                <h2>Info</h2>
                <p>Les pages générées seront placées dans le dossier <code>pages/</code> du thème. WordPress les détectera automatiquement tant qu'elles contiennent le commentaire d'en-tête de template.</p>
            </div>
        </div>
    </div>
</div>
