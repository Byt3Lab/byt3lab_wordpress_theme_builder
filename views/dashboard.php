<div class="wrap">
    <h1>BYT3LAB Builder Dashboard</h1>
    
    <div style="display: flex; gap: 20px; align-items: flex-start; max-width: 1200px; margin-top: 20px;">
        <div style="flex: 1;">
            <div class="card" style="max-width: 100%; margin-top: 0;">
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
                    <select name="working_theme" class="regular-text" style="width: 100%; max-width: 300px; margin-bottom: 10px;">
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

        <div style="flex: 1;">
            <div class="card" style="max-width: 100%; margin-top: 0;">
                <h2>Diagnostic Système (Health Check)</h2>
                <p>Vérifiez que votre environnement est correctement configuré pour faire fonctionner le Builder.</p>
                <table class="widefat striped" style="border: none; box-shadow: none; margin-top: 15px;">
                    <tbody>
                        <?php foreach ($healthChecks as $key => $check): ?>
                            <tr>
                                <td style="width: 30px; font-size: 18px; vertical-align: middle; text-align: center; padding: 12px 10px;">
                                    <?php if ($check['status']): ?>
                                        <span style="color: #46b450;">✔️</span>
                                    <?php else: ?>
                                        <span style="color: #dc3232;">❌</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 12px 10px;">
                                    <strong style="font-size: 13px;"><?= esc_html($check['label']) ?></strong><br>
                                    <small style="color: #666; font-size: 11px;"><?= esc_html($check['message']) ?></small>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>