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
                                <input type="search" placeholder="Filtrer les CSS..." style="width: 100%; margin: 5px 0; padding: 4px 8px; border-radius: 4px; border: 1px solid #ccc;">
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
                            </div>
                            <div style="flex: 1;">
                                <label>Assets JS à inclure :</label><br>
                                <input type="search" placeholder="Filtrer les JS..." style="width: 100%; margin: 5px 0; padding: 4px 8px; border-radius: 4px; border: 1px solid #ccc;">
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
                            </div>
                            <div style="flex: 1;">
                                <label>Composants à inclure :</label><br>
                                <input type="search" placeholder="Filtrer les composants..." style="width: 100%; margin: 5px 0; padding: 4px 8px; border-radius: 4px; border: 1px solid #ccc;">
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
                                            <a href="<?= admin_url('admin.php?page=byt3lab-builder-editor&theme=' . urlencode($selectedTheme) . '&page_slug=' . urlencode($pg['slug'])) ?>" style="font-weight:bold; color:#0284c7;">🚀 Workspace (Édition + Rendu)</a> |
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
                <p><strong>Ordre des assets :</strong> ordonnez vos assets et composants en utilisant le glisser-déposer ci-contre, puis cliquez sur le bouton de génération pour sauvegarder.</p>
            </div>

            <?php if (isset($editPageData)): ?>
                <?php
                $pageObj = get_page_by_path($editPageData['slug']);
                $previewUrl = $pageObj ? get_permalink($pageObj->ID) : home_url('/' . $editPageData['slug']);
                $previewUrl = add_query_arg('byt3lab_preview_time', time(), $previewUrl);
                ?>
                <div class="card" style="max-width: 100%; margin-top: 20px; padding: 15px;">
                    <h2>👁️ Aperçu en Direct</h2>
                    <p style="font-size: 12px; color: #666; margin-bottom: 10px;">Le rendu ci-dessous se met à jour à chaque enregistrement ou modification.</p>
                    <div style="border: 1px solid #ccd0d4; border-radius: 6px; overflow: hidden; background: #f0f0f1; position: relative; height: 550px; box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);">
                        <iframe id="byt3lab-preview-iframe" src="<?= esc_url($previewUrl) ?>" style="width: 100%; height: 100%; border: none;"></iframe>
                    </div>
                    <div style="margin-top: 12px; display: flex; justify-content: space-between; align-items: center;">
                        <button type="button" class="button" onclick="document.getElementById('byt3lab-preview-iframe').src = document.getElementById('byt3lab-preview-iframe').src;">🔄 Rafraîchir</button>
                        <a href="<?= esc_url($previewUrl) ?>" target="_blank" class="button button-primary">Ouvrir dans un onglet ↗️</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        setupDragDropSelect('page_css', 'Assets CSS disponibles (cliquer pour ajouter) :');
        setupDragDropSelect('page_js', 'Assets JS disponibles (cliquer pour ajouter) :');
        setupDragDropSelect('page_components', 'Composants disponibles (cliquer pour ajouter) :');
    });

    function setupDragDropSelect(selectId, labelText) {
        const sel = document.getElementById(selectId);
        if (!sel) return;

        sel.style.display = 'none';
        const parent = sel.parentNode;
        
        const container = document.createElement('div');
        container.className = 'byt3lab-dragdrop-container';
        container.style.marginTop = '8px';
        
        const availHeader = document.createElement('div');
        availHeader.style.fontWeight = '600';
        availHeader.style.fontSize = '11px';
        availHeader.style.color = '#4b5563';
        availHeader.style.marginBottom = '5px';
        availHeader.innerText = labelText;
        container.appendChild(availHeader);
        
        const availArea = document.createElement('div');
        availArea.className = 'byt3lab-avail-area';
        availArea.style.display = 'flex';
        availArea.style.flexWrap = 'wrap';
        availArea.style.gap = '6px';
        availArea.style.padding = '8px';
        availArea.style.border = '1px dashed #cbd5e1';
        availArea.style.borderRadius = '6px';
        availArea.style.background = '#f8fafc';
        availArea.style.minHeight = '36px';
        availArea.style.maxHeight = '120px';
        availArea.style.overflowY = 'auto';
        availArea.style.marginBottom = '10px';
        container.appendChild(availArea);
        
        const selHeader = document.createElement('div');
        selHeader.style.fontWeight = '600';
        selHeader.style.fontSize = '11px';
        selHeader.style.color = '#4b5563';
        selHeader.style.marginBottom = '5px';
        selHeader.innerText = 'Ordre de chargement (glisser-déposer pour réordonner) :';
        container.appendChild(selHeader);
        
        const selectedList = document.createElement('div');
        selectedList.className = 'byt3lab-selected-list';
        selectedList.style.display = 'flex';
        selectedList.style.flexDirection = 'column';
        selectedList.style.gap = '6px';
        selectedList.style.padding = '8px';
        selectedList.style.border = '1px solid #cbd5e1';
        selectedList.style.borderRadius = '6px';
        selectedList.style.background = '#ffffff';
        selectedList.style.minHeight = '60px';
        selectedList.style.maxHeight = '200px';
        selectedList.style.overflowY = 'auto';
        container.appendChild(selectedList);
        
        parent.appendChild(container);
        
        const optionsData = [];
        for (let i = 0; i < sel.options.length; i++) {
            const opt = sel.options[i];
            optionsData.push({
                value: opt.value,
                text: opt.text,
                selected: opt.selected
            });
        }
        
        renderWidget();
        
        const searchInput = parent.querySelector('input[type="search"]');
        if (searchInput) {
            searchInput.oninput = function() {
                const query = this.value.toLowerCase();
                const chips = availArea.querySelectorAll('.byt3lab-chip');
                chips.forEach(chip => {
                    const txt = chip.dataset.text.toLowerCase();
                    if (txt.includes(query)) {
                        chip.style.display = 'inline-flex';
                    } else {
                        chip.style.display = 'none';
                    }
                });
            };
        }
        
        function renderWidget() {
            availArea.innerHTML = '';
            selectedList.innerHTML = '';
            
            const selectedItems = optionsData.filter(o => o.selected);
            const availableItems = optionsData.filter(o => !o.selected);
            
            if (availableItems.length === 0) {
                availArea.innerHTML = '<span style="color:#94a3b8; font-size:11px;">Aucun élément disponible</span>';
            } else {
                availableItems.forEach(item => {
                    const chip = document.createElement('div');
                    chip.className = 'byt3lab-chip';
                    chip.dataset.text = item.text;
                    chip.style.display = 'inline-flex';
                    chip.style.alignItems = 'center';
                    chip.style.padding = '4px 10px';
                    chip.style.background = '#e2e8f0';
                    chip.style.color = '#334155';
                    chip.style.borderRadius = '12px';
                    chip.style.fontSize = '11px';
                    chip.style.cursor = 'pointer';
                    chip.style.userSelect = 'none';
                    chip.style.border = '1px solid #cbd5e1';
                    chip.innerText = item.text;
                    
                    chip.onclick = () => {
                        item.selected = true;
                        updateOriginalSelect();
                        renderWidget();
                    };
                    
                    availArea.appendChild(chip);
                });
            }
            
            if (selectedItems.length === 0) {
                selectedList.innerHTML = '<span style="color:#94a3b8; font-size:11px; padding: 10px; text-align: center;">Cliquez sur un élément ci-dessus pour l\'ajouter</span>';
            } else {
                selectedItems.forEach((item, index) => {
                    const row = document.createElement('div');
                    row.className = 'byt3lab-sortable-row';
                    row.draggable = true;
                    row.dataset.index = index;
                    
                    row.style.display = 'flex';
                    row.style.alignItems = 'center';
                    row.style.padding = '6px 10px';
                    row.style.background = '#f8fafc';
                    row.style.border = '1px solid #e2e8f0';
                    row.style.borderRadius = '4px';
                    row.style.cursor = 'grab';
                    row.style.fontSize = '12px';
                    row.style.userSelect = 'none';
                    
                    row.innerHTML = `
                        <span style="margin-right: 8px; color: #94a3b8; font-size: 14px; cursor: grab;">☰</span>
                        <span style="font-weight: 500; color: #1e293b; flex-grow: 1;">${item.text}</span>
                        <span class="remove-btn" style="color: #ef4444; cursor: pointer; font-weight: bold; margin-left: 10px; padding: 0 4px; font-size: 14px;">×</span>
                    `;
                    
                    row.querySelector('.remove-btn').onclick = (e) => {
                        e.stopPropagation();
                        item.selected = false;
                        updateOriginalSelect();
                        renderWidget();
                    };
                    
                    row.addEventListener('dragstart', (e) => {
                        e.dataTransfer.setData('text/plain', index);
                        row.style.opacity = '0.5';
                    });
                    
                    row.addEventListener('dragend', () => {
                        row.style.opacity = '1';
                    });
                    
                    row.addEventListener('dragover', (e) => {
                        e.preventDefault();
                        row.style.borderTop = '2px solid #3b82f6';
                    });
                    
                    row.addEventListener('dragleave', () => {
                        row.style.borderTop = '1px solid #e2e8f0';
                    });
                    
                    row.addEventListener('drop', (e) => {
                        e.preventDefault();
                        row.style.borderTop = '1px solid #e2e8f0';
                        const fromIndex = parseInt(e.dataTransfer.getData('text/plain'), 10);
                        const toIndex = index;
                        
                        if (fromIndex !== toIndex) {
                            const selectedElements = optionsData.filter(o => o.selected);
                            const targetItem = selectedElements[fromIndex];
                            
                            selectedElements.splice(fromIndex, 1);
                            selectedElements.splice(toIndex, 0, targetItem);
                            
                            const unselectedElements = optionsData.filter(o => !o.selected);
                            optionsData.length = 0;
                            optionsData.push(...selectedElements, ...unselectedElements);
                            
                            updateOriginalSelect();
                            renderWidget();
                        }
                    });
                    
                    selectedList.appendChild(row);
                });
            }
        }
        
        function updateOriginalSelect() {
            sel.innerHTML = '';
            optionsData.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item.value;
                opt.text = item.text;
                opt.selected = item.selected;
                sel.appendChild(opt);
            });
        }
    }
</script>