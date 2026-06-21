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
                                <option value="<?= esc_attr($slug) ?>"><?= esc_html($theme->get('Name')) ?></option>
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
                <h2>Info</h2>
                <p>Les composants générés seront placés dans <code>components/nom-du-composant/</code>.</p>
                <p>Chaque composant inclut ses propres fichiers <code>.php</code>, <code>.css</code>, <code>.js</code> et <code>component.json</code>.</p>
            </div>
        </div>
    </div>
</div>
