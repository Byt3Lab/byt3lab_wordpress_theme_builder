<div class="wrap">
    <h1>BYT3LAB Builder - Export/Import</h1>
    <?= $message ?? '' ?>

    <?php if (!empty($downloadUrl)): ?>
        <div class="card" style="max-width: 600px; margin-top: 20px; background: #eefee6;">
            <h2>✔ Export prêt</h2>
            <p>Votre archive est générée.</p>
            <a href="<?= $downloadUrl ?>" class="button button-primary button-hero" download>Télécharger le ZIP</a>
        </div>
    <?php endif; ?>

    <div style="display: flex; gap: 20px; margin-top: 20px;">
        <div style="flex: 1; max-width: 400px;">
            <div class="card" style="max-width: 100%; margin-top: 0;">
                <h2>Exporter un thème</h2>
                <form method="POST" action="">
                    <?php wp_nonce_field('export_theme_nonce'); ?>
                    <input type="hidden" name="export_theme" value="1">

                    <p>
                        <label>Sélectionner le thème à exporter :</label><br>
                        <select name="theme_slug" required class="regular-text">
                            <option value="">-- Choisir un thème --</option>
                            <?php foreach ($builderThemes as $slug => $theme): ?>
                                <option value="<?= esc_attr($slug) ?>"><?= esc_html($theme->get('Name')) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                    <p>
                        <button type="submit" class="button button-primary">Générer l'archive ZIP</button>
                    </p>
                </form>
            </div>
        </div>

        <div style="flex: 2;">
            <div class="card" style="max-width: 100%; margin-top: 0;">
                <h2>Importer un thème</h2>
                <form method="POST" action="" enctype="multipart/form-data">
                    <?php wp_nonce_field('import_theme_nonce'); ?>
                    <input type="hidden" name="import_theme" value="1">

                    <p>
                        <label>Sélectionner une archive (.zip) exportée via BYT3LAB Builder :</label><br>
                        <input type="file" name="theme_zip" accept=".zip" required>
                    </p>
                    <p>
                        <button type="submit" class="button button-primary">Importer et extraire</button>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>