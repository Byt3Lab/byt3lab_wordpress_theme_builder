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
                    <h2><?= isset($editPageData) ? '3. Modifier la Page : ' . esc_html($editPageData['title']) : '2. Configurer et Créer une Page' ?></h2>
                    <form method="POST" action="">
                        <?php wp_nonce_field('generate_page_nonce'); ?>
                        <input type="hidden" name="generate_page" value="1">
                        <input type="hidden" name="theme_slug" value="<?= esc_attr($selectedTheme) ?>">
                        <?php if (isset($editPageData)): ?>
                            <input type="hidden" name="is_edit" value="1">
                        <?php endif; ?>

                        <p>
                            <label>Titre de la Page :</label><br>
                            <input type="text" name="page_title" required class="regular-text" placeholder="Ex: A Propos" value="<?= esc_attr($editPageData['title'] ?? '') ?>">
                        </p>
                        <p>
                            <label>Slug de la Page (Optionnel) :</label><br>
                            <input type="text" name="page_slug" class="regular-text" placeholder="Ex: about" value="<?= esc_attr($editPageData['slug'] ?? '') ?>" <?= isset($editPageData) ? 'readonly' : '' ?>>
                        </p>
                        <p>
                            <label>Description SEO courte :</label><br>
                            <textarea name="page_description" rows="2" class="regular-text"><?= esc_html($editPageData['description'] ?? '') ?></textarea>
                        </p>

                        <div style="display: flex; gap: 20px; align-items: flex-start;">
                            <div style="flex: 1;">
                                <label>Assets CSS à inclure :</label><br>
                                <div style="display:flex; gap:6px;">
                                    <select id="page_css" name="page_css[]" multiple class="regular-text" style="height: 100px; width:100%;">
                                        <?php
                                        $savedCss = $editPageData['css_files'] ?? ($editPageData['css'] ?? []);
                                        // Show saved ones first in order
                                        foreach ($savedCss as $cssPath):
                                            if (in_array(basename($cssPath), $availableCss)):
                                        ?>
                                                <option value="<?= esc_attr($cssPath) ?>" selected><?= esc_html(basename($cssPath)) ?></option>
                                            <?php
                                            endif;
                                        endforeach;

                                        // Show remaining unused CSS
                                        foreach ($availableCss as $css):
                                            $cssPath = 'assets/css/' . $css;
                                            if (!in_array($cssPath, $savedCss)):
                                            ?>
                                                <option value="<?= esc_attr($cssPath) ?>"><?= esc_html($css) ?></option>
                                        <?php
                                            endif;
                                        endforeach;
                                        ?>
                                    </select>
                                </div>
                                <div style="margin-top:6px;">
                                    <button type="button" class="button" onclick="moveOption('page_css', -1)">Monter</button>
                                    <button type="button" class="button" onclick="moveOption('page_css', 1)">Descendre</button>
                                </div>
                            </div>
                            <div style="flex: 1;">
                                <label>Assets JS à inclure :</label><br>
                                <div style="display:flex; gap:6px;">
                                    <select id="page_js" name="page_js[]" multiple class="regular-text" style="height: 100px; width:100%;">
                                        <?php
                                        $savedJs = $editPageData['js_files'] ?? ($editPageData['js'] ?? []);
                                        // Show saved ones first in order
                                        foreach ($savedJs as $jsPath):
                                            if (in_array(basename($jsPath), $availableJs)):
                                        ?>
                                                <option value="<?= esc_attr($jsPath) ?>" selected><?= esc_html(basename($jsPath)) ?></option>
                                            <?php
                                            endif;
                                        endforeach;

                                        // Show remaining unused JS
                                        foreach ($availableJs as $js):
                                            $jsPath = 'assets/js/' . $js;
                                            if (!in_array($jsPath, $savedJs)):
                                            ?>
                                                <option value="<?= esc_attr($jsPath) ?>"><?= esc_html($js) ?></option>
                                        <?php
                                            endif;
                                        endforeach;
                                        ?>
                                    </select>
                                </div>
                                <div style="margin-top:6px;">
                                    <button type="button" class="button" onclick="moveOption('page_js', -1)">Monter</button>
                                    <button type="button" class="button" onclick="moveOption('page_js', 1)">Descendre</button>
                                </div>
                            </div>
                            <div style="flex: 1;">
                                <label>Composants à inclure :</label><br>
                                <div style="display:flex; gap:6px;">
                                    <select id="page_components" name="page_components[]" multiple class="regular-text" style="height: 100px; width:100%;">
                                        <?php
                                        $savedComps = $editPageData['components'] ?? [];
                                        // Show saved ones first in order
                                        foreach ($savedComps as $comp):
                                            if (in_array($comp, $availableComponents)):
                                        ?>
                                                <option value="<?= esc_attr($comp) ?>" selected><?= esc_html($comp) ?></option>
                                            <?php
                                            endif;
                                        endforeach;

                                        // Show remaining unused components
                                        foreach ($availableComponents as $comp):
                                            if (!in_array($comp, $savedComps)):
                                            ?>
                                                <option value="<?= esc_attr($comp) ?>"><?= esc_html($comp) ?></option>
                                        <?php
                                            endif;
                                        endforeach;
                                        ?>
                                    </select>
                                </div>
                                <div style="margin-top:6px;">
                                    <button type="button" class="button" onclick="moveOption('page_components', -1)">Monter</button>
                                    <button type="button" class="button" onclick="moveOption('page_components', 1)">Descendre</button>
                                </div>
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
                                    <th>CSS</th>
                                    <th>JS</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($existingPages as $pg): ?>
                                    <tr>
                                        <td><strong><?= esc_html($pg['title'] ?? '') ?></strong></td>
                                        <td><?= esc_html($pg['slug'] ?? '') ?></td>
                                        <td><?= esc_html(implode(', ', $pg['components'] ?? [])) ?></td>
                                        <td><small><?= esc_html(implode(', ', $pg['css_files'] ?? $pg['css'] ?? [])) ?></small></td>
                                        <td><small><?= esc_html(implode(', ', $pg['js_files'] ?? $pg['js'] ?? [])) ?></small></td>
                                        <td>
                                            <a href="<?= admin_url('admin.php?page=byt3lab-builder-pages&theme=' . urlencode($selectedTheme) . '&edit=' . urlencode($pg['slug'])) ?>">⚙️ Éditer config</a> |
                                            <a href="<?= admin_url('admin.php?page=byt3lab-builder-editor&theme=' . urlencode($selectedTheme) . '&file=' . urlencode('pages/page-' . $pg['slug'] . '.php')) ?>">Éditer PHP</a> |
                                            <a href="<?= admin_url('admin.php?page=byt3lab-builder-editor&theme=' . urlencode($selectedTheme) . '&file=' . urlencode('pages/page-' . $pg['slug'] . '.json')) ?>">Éditer JSON</a> |
                                            <form method="POST" action="" style="display:inline; margin:0;">
                                                <?php wp_nonce_field('delete_page_nonce'); ?>
                                                <input type="hidden" name="delete_page" value="1">
                                                <input type="hidden" name="theme_slug" value="<?= esc_attr($selectedTheme) ?>">
                                                <input type="hidden" name="page_slug" value="<?= esc_attr($pg['slug'] ?? '') ?>">
                                                <button class="button-link" style="color:#a00;" onclick="return confirm('Confirmer la suppression de cette page ?')">Supprimer</button>
                                            </form>
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
                <p>Les pages générées sont placées dans <code>pages/</code> du thème. Les assets CSS/JS sélectionnés sont enqueués via <code>functions.php</code> dans le <code>&lt;head&gt;</code>.</p>
                <p><strong>Ordre des assets :</strong> utilisez les boutons Monter/Descendre pour définir l'ordre de chargement, puis cliquez sur Générer.</p>
            </div>
        </div>
    </div>
</div>
<script>
    function moveOption(selectId, dir) {
        const sel = document.getElementById(selectId);
        if (!sel) return;
        for (let i = 0; i < sel.options.length; i++) {
            if (sel.options[i].selected) {
                const j = dir === -1 ? i - 1 : i + 1;
                if (j < 0 || j >= sel.options.length) return;
                const opt = sel.options[i];
                const ref = sel.options[j];
                if (dir === -1) {
                    sel.insertBefore(opt, ref);
                } else {
                    sel.insertBefore(ref, opt);
                }
                opt.selected = true;
                break;
            }
        }
    }
</script>