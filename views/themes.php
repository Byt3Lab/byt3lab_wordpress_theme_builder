<div class="wrap">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1>BYT3LAB Builder - Thèmes</h1>
        <a href="?page=byt3lab-builder-export" class="button button-secondary">Importer / Exporter une archive ZIP</a>
    </div>
    <?= $message ?? '' ?>

    <div style="display: flex; gap: 20px;">
        <!-- Left Column: Form -->
        <div style="flex: 1; max-width: 400px;">
            <div class="card" style="max-width: 100%; margin-top: 0;">
                <h2>Créer un nouveau thème</h2>
                <form method="POST" action="">
                    <?php wp_nonce_field('generate_theme_nonce'); ?>
                    <input type="hidden" name="generate_theme" value="1">

                    <p>
                        <label>Nom du thème :</label><br>
                        <input type="text" name="theme_name" required class="regular-text">
                    </p>
                    <p>
                        <label>Slug (optionnel) :</label><br>
                        <input type="text" name="theme_slug" class="regular-text">
                    </p>
                    <p>
                        <label>Auteur :</label><br>
                        <input type="text" name="theme_author" class="regular-text">
                    </p>
                    <p>
                        <label>Version :</label><br>
                        <input type="text" name="theme_version" value="1.0.0" class="regular-text">
                    </p>
                    <p>
                        <label>Template de démarrage :</label><br>
                        <select name="theme_template" class="regular-text">
                            <option value="base">Base (Vide)</option>
                            <option value="corporate">Corporate (Avec pages et composants)</option>
                            <option value="blog">Blog</option>
                        </select>
                    </p>
                    <p>
                        <label>Description :</label><br>
                        <textarea name="theme_description" rows="4" class="regular-text"></textarea>
                    </p>
                    <p>
                        <button type="submit" class="button button-primary">Générer le thème</button>
                    </p>
                </form>
            </div>

            <div class="card" style="max-width: 100%; margin-top: 20px;">
                <h2>Uploader l'Image de Présentation (Screenshot)</h2>
                <form method="POST" action="" enctype="multipart/form-data">
                    <?php wp_nonce_field('upload_screenshot_nonce'); ?>
                    <input type="hidden" name="upload_screenshot" value="1">

                    <p>
                        <select name="theme_slug" required class="regular-text">
                            <option value="">-- Choisir un thème --</option>
                            <?php foreach ($builderThemes as $slug => $theme): ?>
                                <option value="<?= esc_attr($slug) ?>"><?= esc_html($theme->get('Name')) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                    <p>
                        <input type="file" name="screenshot_file" accept=".png,.jpg,.jpeg" required>
                    </p>
                    <p>
                        <button type="submit" class="button button-secondary">Mettre à jour l'image</button>
                    </p>
                </form>
            </div>
        </div>

        <!-- Right Column: List -->
        <div style="flex: 2;">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Version</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($builderThemes)): ?>
                        <tr>
                            <td colspan="4">Aucun thème généré.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($builderThemes as $slug => $theme): ?>
                            <tr>
                                <td><strong><?= esc_html($theme->get('Name')) ?></strong></td>
                                <td><?= esc_html($theme->get('Version')) ?></td>
                                <td><?= $slug === get_template() ? 'Actif' : 'Inactif' ?></td>
                                <td>
                                    <?php if ($slug !== get_template()): ?>
                                        <a href="<?= admin_url('themes.php') ?>">Activer</a> |
                                    <?php else: ?>
                                        <em>Actif</em> |
                                    <?php endif; ?>
                                    <a href="<?= admin_url('customize.php?theme=' . urlencode($slug)) ?>">Prévisualiser</a> |
                                    <a href="<?= wp_nonce_url(admin_url('admin.php?page=byt3lab-builder-export&action=export&theme=' . urlencode($slug)), 'export_theme_' . $slug) ?>">Exporter</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>