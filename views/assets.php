<div class="wrap">
    <h1>BYT3LAB Builder - Assets</h1>
    <?= $message ?? '' ?>

    <div style="display: flex; gap: 20px;">
        <div style="flex: 1; max-width: 400px;">
            <div class="card" style="max-width: 100%; margin-top: 0;">
                <h2>Ajouter un fichier Asset (CSS/JS)</h2>
                <form method="POST" action="">
                    <?php wp_nonce_field('generate_asset_nonce'); ?>
                    <input type="hidden" name="generate_asset" value="1">

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
                        <label>Type d'asset :</label><br>
                        <select name="asset_type" required class="regular-text">
                            <option value="css">CSS</option>
                            <option value="js">JavaScript</option>
                        </select>
                    </p>
                    <p>
                        <label>Nom du fichier (ex: main.css) :</label><br>
                        <input type="text" name="asset_filename" required class="regular-text">
                    </p>
                    <p>
                        <button type="submit" class="button button-primary">Créer le fichier</button>
                    </p>
                </form>
            </div>
        </div>

        <div style="flex: 2;">
            <div class="card" style="max-width: 100%; margin-top: 0;">
                <h2>Uploader n'importe quel type de fichier</h2>
                <form method="POST" action="" enctype="multipart/form-data">
                    <?php wp_nonce_field('upload_asset_nonce'); ?>
                    <input type="hidden" name="upload_asset" value="1">

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
                        <label>Dossier cible (CSS, JS, Images, Fonts...) :</label><br>
                        <select name="asset_type" required class="regular-text">
                            <option value="css">CSS</option>
                            <option value="js">JavaScript</option>
                            <option value="images">Images</option>
                            <option value="fonts">Fonts</option>
                            <option value="media">Media divers</option>
                        </select>
                    </p>
                    <p>
                        <label>Fichier à uploader :</label><br>
                        <input type="file" name="asset_file" required>
                    </p>
                    <p>
                        <button type="submit" class="button button-primary">Uploader le fichier</button>
                    </p>
                </form>
            </div>

            <div class="card" style="max-width: 100%; margin-top: 20px;">
                <h2>Info</h2>
                <p>Les assets générés ou uploadés seront placés dans <code>assets/{type}/</code> de votre thème.</p>
                <p>La fonctionnalité permet maintenant d'uploader images, polices personnalisées (.ttf, .woff) qui pourront être référencées dans vos CSS et composants.</p>
            </div>
        </div>
    </div>
</div>