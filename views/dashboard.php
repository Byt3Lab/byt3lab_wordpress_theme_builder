<div class="wrap">
    <h1>BYT3LAB Builder Dashboard</h1>
    <div class="card" style="max-width: 600px; margin-top: 20px;">
        <h2>Overview</h2>
        <p><strong>Version:</strong> <?= BYT3LAB_BUILDER_VERSION ?></p>
        <p><strong>Thèmes générés :</strong> <?= $builderThemesCount ?></p>
        <p><strong>Thème actif :</strong> <?= esc_html($activeThemeName) ?></p>
        <p><strong>Pages générées :</strong> <?= $totalPages ?></p>
        <p><strong>Composants disponibles :</strong> <?= $totalComponents ?></p>

        <hr>
        <h2>Thème de travail par défaut</h2>
        <form method="POST" action="">
            <?php wp_nonce_field('set_working_theme_nonce'); ?>
            <input type="hidden" name="set_working_theme" value="1">
            <select name="working_theme" class="regular-text">
                <option value="">-- Aucun thème de travail par défaut --</option>
                <?php foreach ($builderThemesArr as $slug => $theme): ?>
                    <option value="<?= esc_attr($slug) ?>" <?= ($workingTheme === $slug) ? 'selected' : '' ?>>
                        <?= esc_html($theme->get('Name')) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="button button-secondary">Enregistrer</button>
        </form>

        <div style="margin-top: 20px;">
            <a href="?page=byt3lab-builder-themes" class="button button-primary">Créer un thème</a>
            <a href="?page=byt3lab-builder-pages" class="button">Gérer les pages</a>
            <a href="?page=byt3lab-builder-components" class="button">Gérer les composants</a>
        </div>
    </div>
</div>