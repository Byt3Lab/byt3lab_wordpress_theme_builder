<div class="wrap">
    <h1>BYT3LAB Builder - Composants</h1>
    <?= $message ?? '' ?>

    <div style="display: flex; gap: 20px;">
        <div style="flex: 1; max-width: 400px;">
            <div class="card" style="max-width: 100%; margin-top: 0;">
                <h2>Créer un nouveau composant</h2>
                <form method="POST" action="">
                    <?php wp_nonce_field('generate_component_nonce'); ?>
                    <input type="hidden" name="generate_component" value="1">

                    <p>
                        <label>Sélectionner le thème :</label><br>
                        <select name="theme_slug" required class="regular-text">
                            <option value="">-- Choisir un thème --</option>
                            <?php foreach ($builderThemes as $slug => $theme): ?>
                                <option value="<?= esc_attr($slug) ?>" <?= (isset($selectedTheme) && $selectedTheme === $slug) ? 'selected' : '' ?>><?= esc_html($theme->get('Name')) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                    <p>
                        <label>Nom du composant (ex: Hero Section) :</label><br>
                        <input type="text" name="component_name" required class="regular-text">
                    </p>
                    <p>
                        <label>Type de composant :</label><br>
                        <select name="component_type" required class="regular-text">
                            <option value="section">Section</option>
                            <option value="element">Element</option>
                            <option value="layout">Layout</option>
                        </select>
                    </p>
                    <p>
                        <button type="submit" class="button button-primary">Générer le composant</button>
                    </p>
                </form>
            </div>
        </div>

        <div style="flex: 2;">
            <div class="card" style="max-width: 100%; margin-top: 0;">
                <h2>Gérer les Composants existants</h2>
                <form method="GET" action="">
                    <input type="hidden" name="page" value="byt3lab-builder-components">
                    <p>
                        <select name="theme" onchange="this.form.submit()" style="width: 100%; max-width: 300px;">
                            <option value="">-- Choisir un thème à inspecter --</option>
                            <?php foreach ($builderThemes as $slug => $theme): ?>
                                <option value="<?= esc_attr($slug) ?>" <?= (isset($selectedTheme) && $selectedTheme === $slug) ? 'selected' : '' ?>>
                                    <?= esc_html($theme->get('Name')) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                </form>

                <?php if (!empty($selectedTheme)): ?>
                    <?php if (empty($existingComponents)): ?>
                        <p>Aucun composant pour ce thème.</p>
                    <?php else: ?>
                        <table class="wp-list-table widefat fixed striped" style="margin-top: 15px;">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($existingComponents as $comp): ?>
                                    <tr>
                                        <td><strong><?= esc_html($comp['name']) ?></strong></td>
                                        <td><?= esc_html($comp['type']) ?></td>
                                        <td>
                                            <a href="<?= admin_url('admin.php?page=byt3lab-builder-editor&theme=' . urlencode($selectedTheme) . '&file=' . urlencode('components/' . $comp['slug'] . '/' . $comp['slug'] . '.php')) ?>">Éditer PHP</a> |
                                            <a href="<?= admin_url('admin.php?page=byt3lab-builder-editor&theme=' . urlencode($selectedTheme) . '&file=' . urlencode('components/' . $comp['slug'] . '/' . $comp['slug'] . '.css')) ?>">Éditer CSS</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                <?php else: ?>
                    <p>Sélectionnez un thème pour voir et éditer ses composants.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>