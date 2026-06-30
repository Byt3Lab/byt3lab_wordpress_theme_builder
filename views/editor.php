<div class="wrap">
    <h1>BYT3LAB Builder - Éditeur</h1>
    <?= $message ?? '' ?>

    <div style="display: flex; gap: 20px; align-items: flex-start;">
        <!-- Sidebar: Themes & Files -->
        <div style="flex: 1; max-width: 300px;">
            <div class="card" style="max-width: 100%; margin-top: 0;">
                <h2><?= !empty($pageSlug) ? 'Workspace : ' . esc_html($pageSlug) : 'Explorateur' ?></h2>
                
                <?php if (empty($pageSlug)): ?>
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
                <?php else: ?>
                    <p style="margin-bottom:10px;">
                        <a href="?page=byt3lab-builder-editor&theme=<?= urlencode($selectedTheme) ?>" class="button button-small">⬅️ Voir tous les fichiers</a>
                    </p>
                <?php endif; ?>

                <?php if ($selectedTheme && !empty($files)): ?>
                    <ul style="margin-top: 15px; border-top: 1px solid #ddd; padding-top: 10px; list-style: none; padding-left: 0;">
                        <?php foreach ($files as $f): ?>
                            <?php
                            // Format path nicely for display
                            $displayName = basename($f);
                            if (strpos($f, 'components/') === 0) {
                                $parts = explode('/', $f);
                                $displayName = '🧩 ' . $parts[1] . ' > ' . basename($f);
                            } elseif (strpos($f, 'pages/') === 0) {
                                $displayName = '📄 Page > ' . basename($f);
                            } elseif (strpos($f, 'assets/css/') === 0) {
                                $displayName = '🎨 CSS > ' . basename($f);
                            } elseif (strpos($f, 'assets/js/') === 0) {
                                $displayName = '⚡ JS > ' . basename($f);
                            } elseif (strpos($f, 'assets/fonts/') === 0) {
                                $displayName = '🔤 Police > ' . basename($f);
                            } elseif (strpos($f, 'assets/images/') === 0) {
                                $displayName = '🖼️ Image > ' . basename($f);
                            } elseif (strpos($f, 'assets/media/') === 0) {
                                $displayName = '🎥 Média > ' . basename($f);
                            } elseif (strpos($f, 'assets/documents/') === 0) {
                                $displayName = '📁 Doc > ' . basename($f);
                            } else {
                                $displayName = '📁 Autre > ' . basename($f);
                            }
                            ?>
                            <li style="margin-bottom: 6px;">
                                <a href="?page=byt3lab-builder-editor&theme=<?= urlencode($selectedTheme) ?><?= !empty($pageSlug) ? '&page_slug=' . urlencode($pageSlug) : '' ?>&file=<?= urlencode($f) ?>"
                                    style="<?= $f === $selectedFile ? 'font-weight:bold; color:#0073aa;' : 'text-decoration:none;' ?> display: block; padding: 6px 8px; border-radius: 4px; background: <?= $f === $selectedFile ? '#f0f0f1' : 'transparent' ?>;">
                                    <?= esc_html($displayName) ?>
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
                        <?php if (!empty($pageSlug)): ?>
                            <input type="hidden" name="page_slug" value="<?= esc_attr($pageSlug) ?>">
                        <?php endif; ?>

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
                            if (typeof wp !== 'undefined' && typeof wp.codeEditor !== 'undefined') {
                                wp.codeEditor.initialize('byt3lab-code-editor', <?= wp_json_encode($settings) ?>);
                            }
                        });
                    </script>
                <?php endif; ?>
            <?php else: ?>
                <div class="card" style="max-width: 100%; margin-top: 0;">
                    <p>Sélectionnez un fichier dans l'explorateur pour l'éditer.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Live Preview Panel (Workspace Mode) -->
        <?php if (!empty($pageSlug)): ?>
            <?php
            $pageObj = get_page_by_path($pageSlug);
            $previewUrl = $pageObj ? get_permalink($pageObj->ID) : home_url('/' . $pageSlug);
            $previewUrl = add_query_arg('byt3lab_preview_time', time(), $previewUrl);
            ?>
            <div style="flex: 2; min-width: 320px;">
                <div class="card" style="max-width: 100%; margin-top: 0; padding: 15px;">
                    <h2>👁️ Rendu en Direct</h2>
                    <p style="font-size: 12px; color: #666; margin-bottom: 10px;">Le rendu ci-dessous s'actualise après chaque sauvegarde.</p>
                    <div style="border: 1px solid #ccd0d4; border-radius: 6px; overflow: hidden; background: #f0f0f1; position: relative; height: 550px; box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);">
                        <iframe id="byt3lab-preview-iframe" src="<?= esc_url($previewUrl) ?>" style="width: 100%; height: 100%; border: none;"></iframe>
                    </div>
                    <div style="margin-top: 12px; display: flex; justify-content: space-between; align-items: center;">
                        <button type="button" class="button" onclick="document.getElementById('byt3lab-preview-iframe').src = document.getElementById('byt3lab-preview-iframe').src;">🔄 Rafraîchir</button>
                        <a href="<?= esc_url($previewUrl) ?>" target="_blank" class="button button-primary">Ouvrir ↗️</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>