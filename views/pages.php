<div class="wrap">
    <h1>BYT3LAB Builder - Pages</h1>
    <?= $message ?? '' ?>

    <div style="display: flex; gap: 20px;">
        <div style="flex: 2;">
            <div class="card" style="max-width: 100%; margin-top: 0;">
                <form method="GET" action="" style="margin-bottom: 20px;">
                    <input type="hidden" name="page" value="byt3lab-builder-pages">
                    <label><strong>1. Choisir le thème de travail :</strong></label>
                    <select name="theme" onchange="this.form.submit()">
                        <option value="">-- Aucun --</option>
                        <?php foreach ($builderThemes as $slug => $theme): ?>
                            <option value="<?= esc_attr($slug) ?>" <?= (isset($selectedTheme) && $selectedTheme === $slug) ? 'selected' : '' ?>>
                                <?= esc_html($theme->get('Name')) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>

                <?php if (!empty($selectedTheme)): ?>
                    <hr>
                    <h2>2. Configurer et Créer / Mettre à jour une Page</h2>
                    <form method="POST" action="">
                        <?php wp_nonce_field('generate_page_nonce'); ?>
                        <input type="hidden" name="generate_page" value="1">
                        <input type="hidden" name="theme_slug" value="<?= esc_attr($selectedTheme) ?>">

                        <p>
                            <label>Titre de la Page :</label><br>
                            <input type="text" name="page_title" required class="regular-text" placeholder="Ex: A Propos">
                        </p>
                        <p>
                            <label>Slug de la Page (Optionnel) :</label><br>
                            <input type="text" name="page_slug" class="regular-text" placeholder="Ex: about">
                        </p>
                        <p>
                            <label>Description SEO courte :</label><br>
                            <textarea name="page_description" rows="2" class="regular-text"></textarea>
                        </p>

                        <div style="display: flex; gap: 20px; align-items: flex-start;">
                            <div style="flex: 1;">
                                <label>Assets CSS à inclure :</label><br>
                                <select name="page_css[]" multiple class="regular-text" style="height: 100px;">
                                    <?php foreach ($availableCss as $css): ?>
                                        <option value="<?= esc_attr($css) ?>"><?= esc_html($css) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div style="flex: 1;">
                                <label>Assets JS à inclure :</label><br>
                                <select name="page_js[]" multiple class="regular-text" style="height: 100px;">
                                    <?php foreach ($availableJs as $js): ?>
                                        <option value="<?= esc_attr($js) ?>"><?= esc_html($js) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div style="flex: 1;">
                                <label>Composants à inclure :</label><br>
                                <select name="page_components[]" multiple class="regular-text" style="height: 100px;">
                                    <?php foreach ($availableComponents as $comp): ?>
                                        <option value="<?= esc_attr($comp) ?>"><?= esc_html($comp) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <br><small>Appuyez sur CTRL (ou CMD) pour une sélection multiple.</small>
                            </div>
                        </div>

                        <p style="margin-top:20px;">
                            <button type="submit" class="button button-primary button-large">Générer / Mettre à jour la Page</button>
                        </p>
                    </form>

                    <hr>
                    <h2>Pages générées dans ce thème</h2>
                    <?php if (empty($existingPages)): ?>
                        <p>Aucune page existante.</p>
                    <?php else: ?>
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Slug</th>
                                    <th>Composants</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($existingPages as $pg): ?>
                                    <tr>
                                        <td><strong><?= esc_html($pg['title'] ?? '') ?></strong></td>
                                        <td><?= esc_html($pg['slug'] ?? '') ?></td>
                                        <td><?= esc_html(implode(', ', $pg['components'] ?? [])) ?></td>
                                        <td>
                                            <a href="<?= admin_url('admin.php?page=byt3lab-builder-editor&theme=' . urlencode($selectedTheme) . '&file=' . urlencode('pages/page-' . $pg['slug'] . '.php')) ?>">Éditer PHP source</a> |
                                            <a href="<?= admin_url('admin.php?page=byt3lab-builder-editor&theme=' . urlencode($selectedTheme) . '&file=' . urlencode('pages/page-' . $pg['slug'] . '.json')) ?>">Éditer JSON config</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                <?php else: ?>
                    <p>Veuillez sélectionner un thème pour configurer vos pages.</p>
                <?php endif; ?>
            </div>
        </div>

        <div style="flex: 1;">
            <div class="card" style="max-width: 100%; margin-top: 0;">
                <h2>Info</h2>
                <p>Les pages générées seront placées dans le dossier <code>pages/</code> du thème. WordPress les détectera automatiquement tant qu'elles contiennent le commentaire d'en-tête de template.</p>
            </div>
        </div>
    </div>
</div>