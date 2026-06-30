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
                            <option value="media">Médias</option>
                            <option value="documents">Documents (PDF, Docx...)</option>
                            <option value="others">Autres fichiers</option>
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
                <h2>Explorateur d'Assets existants</h2>
                <form method="GET" action="">
                    <input type="hidden" name="page" value="byt3lab-builder-assets">
                    <p>
                        <select name="theme" onchange="this.form.submit()" style="width: 100%; max-width: 300px;">
                            <option value="">-- Choisir un thème à inspecter --</option>
                            <?php foreach ($builderThemes as $slug => $theme): ?>
                                <option value="<?= esc_attr($slug) ?>" <?= ($selectedTheme === $slug) ? 'selected' : '' ?>>
                                    <?= esc_html($theme->get('Name')) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                </form>

                <?php if (!empty($selectedTheme)): ?>
                    <?php if (empty($existingAssets)): ?>
                        <p style="color: #666; font-style: italic;">Aucun fichier asset trouvé pour ce thème.</p>
                    <?php else: ?>
                        <?php foreach ($existingAssets as $category => $items): ?>
                            <h3 style="text-transform: uppercase; font-size: 13px; color: #4b5563; border-bottom: 2px solid #cbd5e1; padding-bottom: 5px; margin-top: 20px;">
                                📁 <?= esc_html($category) ?> (<?= count($items) ?>)
                            </h3>
                            <table class="wp-list-table widefat fixed striped" style="margin-top: 8px;">
                                <thead>
                                    <tr>
                                        <th style="width: 40%;">Nom / Aperçu</th>
                                        <th style="width: 35%;">Chemin Relatif</th>
                                        <th style="width: 12%;">Taille</th>
                                        <th style="width: 13%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td style="vertical-align: middle;">
                                                <div style="display: flex; align-items: center; gap: 8px;">
                                                    <?php if ($category === 'images'): ?>
                                                        <img src="<?= esc_url($item['url']) ?>" style="max-width: 40px; max-height: 40px; border-radius: 4px; border: 1px solid #ccc; object-fit: cover;">
                                                    <?php endif; ?>
                                                    <strong><?= esc_html($item['name']) ?></strong>
                                                </div>
                                            </td>
                                            <td style="vertical-align: middle;"><code style="font-size: 11px;"><?= esc_html($item['path']) ?></code></td>
                                            <td style="vertical-align: middle;"><small><?= esc_html($item['size']) ?></small></td>
                                            <td style="vertical-align: middle;">
                                                <a href="<?= esc_url($item['url']) ?>" target="_blank" class="button button-small" style="margin-right: 4px;">👁️ Voir</a>
                                                <form method="POST" action="" style="display:inline; margin:0;">
                                                    <?php wp_nonce_field('delete_asset_nonce'); ?>
                                                    <input type="hidden" name="delete_asset" value="1">
                                                    <input type="hidden" name="theme_slug" value="<?= esc_attr($selectedTheme) ?>">
                                                    <input type="hidden" name="asset_path" value="<?= esc_attr($item['path']) ?>">
                                                    <button type="submit" class="button button-small button-link" style="color: #ef4444;" onclick="return confirm('Confirmer la suppression ?')">Supprimer</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php else: ?>
                    <p style="color: #666;">Sélectionnez un thème pour explorer ses assets.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>