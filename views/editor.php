<div class="wrap">
    <h1>BYT3LAB Builder - Éditeur</h1>
    <?= $message ?? '' ?>

    <div style="display: flex; gap: 20px; align-items: flex-start;">
        <!-- Sidebar: Themes & Files -->
        <div style="flex: 1; max-width: 300px;">
            <div class="card" style="max-width: 100%; margin-top: 0;">
                <h2>Explorateur</h2>
                <form method="GET" action="">
                    <input type="hidden" name="page" value="byt3lab-builder-editor">
                    <p>
                        <select name="theme" onchange="this.form.submit()" style="width: 100%;">
                            <option value="">-- Choisir un thème --</option>
                            <?php foreach ($builderThemes as $slug => $theme): ?>
                                <option value="<?= esc_attr($slug) ?>" <?= $slug === $selectedTheme ? 'selected' : '' ?>>
                                    <?= esc_html($theme->get('Name')) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                </form>

                <?php if ($selectedTheme && !empty($files)): ?>
                    <ul style="margin-top: 15px; border-top: 1px solid #ddd; padding-top: 10px;">
                        <?php foreach ($files as $f): ?>
                            <li>
                                <a href="?page=byt3lab-builder-editor&theme=<?= urlencode($selectedTheme) ?>&file=<?= urlencode($f) ?>"
                                    style="<?= $f === $selectedFile ? 'font-weight:bold; color:#0073aa;' : 'text-decoration:none;' ?>">
                                    <?= esc_html($f) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php elseif ($selectedTheme): ?>
                    <p>Aucun fichier modifiable trouvé.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Editor Area -->
        <div style="flex: 3;">
            <?php if ($selectedFile): ?>
                <div class="card" style="max-width: 100%; margin-top: 0; padding: 0;">
                    <form method="POST" action="">
                        <?php wp_nonce_field('save_file_nonce'); ?>
                        <input type="hidden" name="save_file" value="1">
                        <input type="hidden" name="theme" value="<?= esc_attr($selectedTheme) ?>">
                        <input type="hidden" name="file" value="<?= esc_attr($selectedFile) ?>">

                        <div style="padding: 10px 15px; border-bottom: 1px solid #ddd; background: #f9f9f9; display: flex; justify-content: space-between; align-items: center;">
                            <strong><?= esc_html($selectedFile) ?></strong>
                            <div>
                                <?php if (basename($selectedFile) === 'index.php'): ?>
                                    <label style="margin-right:10px;"><input type="checkbox" name="confirm_index_edit" value="1"> Confirmer édition d'index.php</label>
                                <?php endif; ?>
                                <button type="submit" class="button button-primary">Sauvegarder</button>
                            </div>
                        </div>

                        <textarea id="byt3lab-code-editor" name="file_content" rows="30" style="width: 100%; font-family: monospace; border: none; padding: 15px;"><?= esc_textarea($fileContent) ?></textarea>
                    </form>
                </div>

                <?php if ($settings) : ?>
                    <script>
                        jQuery(document).ready(function($) {
                            wp.codeEditor.initialize('byt3lab-code-editor', <?= wp_json_encode($settings) ?>);
                        });
                    </script>
                <?php endif; ?>
            <?php else: ?>
                <div class="card" style="max-width: 100%; margin-top: 0;">
                    <p>Sélectionnez un fichier dans l'explorateur pour l'éditer.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>