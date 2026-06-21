<div class="wrap">
    <h1>BYT3LAB Builder - Thèmes</h1>
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
                        <label>Description :</label><br>
                        <textarea name="theme_description" rows="4" class="regular-text"></textarea>
                    </p>
                    <p>
                        <button type="submit" class="button button-primary">Générer le thème</button>
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
                        <tr><td colspan="4">Aucun thème généré.</td></tr>
                    <?php else: ?>
                        <?php foreach ($builderThemes as $slug => $theme): ?>
                            <tr>
                                <td><strong><?= esc_html($theme->get('Name')) ?></strong></td>
                                <td><?= esc_html($theme->get('Version')) ?></td>
                                <td><?= $slug === get_template() ? 'Actif' : 'Inactif' ?></td>
                                <td>
                                    <?php if ($slug !== get_template()): ?>
                                        <a href="<?= admin_url('themes.php') ?>">Activer dans Apparence</a>
                                    <?php else: ?>
                                        <em>Thème courant</em>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
