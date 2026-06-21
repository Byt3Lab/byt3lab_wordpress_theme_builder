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
</div>
