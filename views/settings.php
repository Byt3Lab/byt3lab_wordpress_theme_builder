<div class="wrap">
    <h1>BYT3LAB Builder - Paramètres</h1>
    <?= $message ?? '' ?>

    <form method="POST" action="">
        <?php wp_nonce_field('save_settings_nonce'); ?>
        <input type="hidden" name="save_settings" value="1">

        <table class="form-table">
            <tr>
                <th scope="row">Framework CSS par défaut</th>
                <td>
                    <select name="css_framework">
                        <option value="none" <?= $css_framework === 'none' ? 'selected' : '' ?>>Aucun</option>
                        <option value="bootstrap" <?= $css_framework === 'bootstrap' ? 'selected' : '' ?>>Bootstrap</option>
                        <option value="tailwind" <?= $css_framework === 'tailwind' ? 'selected' : '' ?>>Tailwind CSS</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">Génération automatique des fichiers manquants</th>
                <td>
                    <label>
                        <input type="checkbox" name="auto_generate" value="1" <?= $auto_generate === '1' ? 'checked' : '' ?>>
                        Activer la génération automatique
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row">Sauvegarde avant modification</th>
                <td>
                    <label>
                        <input type="checkbox" name="auto_backup" value="1" <?= $auto_backup === '1' ? 'checked' : '' ?>>
                        Créer un backup automatique des fichiers modifiés
                    </label>
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <button type="submit" class="button button-primary">Enregistrer les modifications</button>
        </p>
    </form>

    <hr />

    <h2>Pages spéciales du thème</h2>
    <?= $message ?? '' ?>
    <form method="POST" action="">
        <?php wp_nonce_field('save_theme_mappings_nonce'); ?>
        <input type="hidden" name="save_theme_mappings" value="1">

        <p>
            <label>Sélectionner le thème :</label><br>
            <select name="theme_select" onchange="window.location.href='?page=byt3lab-builder-settings&theme='+encodeURIComponent(this.value)">
                <option value="">-- Choisir un thème --</option>
                <?php foreach ($builderThemes as $slug => $theme): ?>
                    <option value="<?= esc_attr($slug) ?>" <?= ($selectedTheme === $slug) ? 'selected' : '' ?>><?= esc_html($theme->get('Name')) ?></option>
                <?php endforeach; ?>
            </select>
        </p>

        <?php if (empty($selectedTheme)): ?>
            <p>Choisissez un thème pour configurer ses pages spéciales.</p>
        <?php else: ?>
            <?php
                // Detect existing screenshot for preview
                $screenshotUrl = '';
                $possible = ['screenshot.png','screenshot.jpg','screenshot.jpeg','screenshot.gif'];
                foreach ($possible as $p) {
                    if (file_exists(WP_CONTENT_DIR . '/themes/' . $selectedTheme . '/' . $p)) {
                        $screenshotUrl = WP_CONTENT_URL . '/themes/' . $selectedTheme . '/' . $p;
                        break;
                    }
                }
            ?>
            <h3>Capture d'écran du thème</h3>
            <?php if ($screenshotUrl): ?>
                <p><img src="<?= esc_url($screenshotUrl) ?>" alt="Screenshot" style="max-width:300px;border:1px solid #ddd;padding:4px;background:#fff"></p>
            <?php else: ?>
                <p>Aucune capture trouvée pour ce thème.</p>
            <?php endif; ?>

            <form method="POST" action="" enctype="multipart/form-data" style="margin-bottom:10px;">
                <?php wp_nonce_field('upload_theme_screenshot_nonce'); ?>
                <input type="hidden" name="upload_theme_screenshot" value="1">
                <input type="hidden" name="theme_select" value="<?= esc_attr($selectedTheme) ?>">
                <p>
                    <label>Importer une nouvelle capture (PNG/JPEG/GIF) :</label><br>
                    <input type="file" name="theme_screenshot" accept="image/png,image/jpeg,image/gif" required>
                </p>
                <p>
                    <button type="submit" class="button button-primary">Téléverser la capture</button>
                </p>
            </form>

            <form method="POST" action="">
                <?php wp_nonce_field('remove_theme_screenshot_nonce'); ?>
                <input type="hidden" name="remove_theme_screenshot" value="1">
                <input type="hidden" name="theme_select" value="<?= esc_attr($selectedTheme) ?>">
                <button type="submit" class="button">Supprimer la capture</button>
            </form>
            <table class="form-table">
                <tr>
                    <th scope="row">Page d'accueil (front-page)</th>
                    <td>
                        <select name="front_page">
                            <option value="">-- Aucune --</option>
                            <?php foreach ($availablePages as $slug => $title): ?>
                                <option value="<?= esc_attr($slug) ?>" <?= ($currentMapping['front_page'] === $slug) ? 'selected' : '' ?>><?= esc_html($title) ?> (<?= esc_html($slug) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Page d'erreur 404</th>
                    <td>
                        <select name="not_found_page">
                            <option value="">-- Aucune --</option>
                            <?php foreach ($availablePages as $slug => $title): ?>
                                <option value="<?= esc_attr($slug) ?>" <?= ($currentMapping['not_found'] === $slug) ? 'selected' : '' ?>><?= esc_html($title) ?> (<?= esc_html($slug) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Page des articles (blog)</th>
                    <td>
                        <select name="posts_page">
                            <option value="">-- Aucune --</option>
                            <?php foreach ($availablePages as $slug => $title): ?>
                                <option value="<?= esc_attr($slug) ?>" <?= ($currentMapping['posts_page'] === $slug) ? 'selected' : '' ?>><?= esc_html($title) ?> (<?= esc_html($slug) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <button type="submit" class="button button-primary">Sauvegarder le mappage</button>
            </p>
        <?php endif; ?>
    </form>
</div>
