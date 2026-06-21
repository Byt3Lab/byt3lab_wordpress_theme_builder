<?php

namespace Byt3lab\Builder\Core;

class PageGenerator
{
    private $fileManager;
    private $configManager;

    public function __construct()
    {
        $this->fileManager = new FileManager();
        $this->configManager = new ConfigManager($this->fileManager);
    }

    public function generate($theme, $data)
    {
        $themePath = WP_CONTENT_DIR . '/themes/' . $theme;

        if (!file_exists($themePath)) {
            return new \WP_Error('theme_not_found', 'Theme introuvable.');
        }

        if (empty($data['title'])) {
            return new \WP_Error('missing_title', 'Le nom de la page est requis.');
        }

        $slug = !empty($data['slug']) ? sanitize_title($data['slug']) : sanitize_title($data['title']);
        $data['slug'] = $slug;
        $isEdit = !empty($_POST['is_edit']);

        $pagesDir = $themePath . '/pages';
        if (!file_exists($pagesDir)) {
            $this->fileManager->createDirectory($pagesDir);
        }

        // Check if page already exists and this is not an edit
        if (!$isEdit) {
            $phpPath = $pagesDir . '/page-' . $slug . '.php';
            if (file_exists($phpPath)) {
                return new \WP_Error('page_exists', 'Une page avec ce slug existe déjà.');
            }
        }

        // Ensure components referenced by the page exist in the theme.
        if (!empty($data['components']) && is_array($data['components'])) {
            $componentsDir = $themePath . '/components';
            $this->fileManager->createDirectory($componentsDir);
            foreach ($data['components'] as $comp) {
                $compSlug = sanitize_file_name($comp);
                $dest = $componentsDir . '/' . $compSlug;
                if (!file_exists($dest)) {
                    // Try to copy from bundled defaults if available
                    $src = BYT3LAB_BUILDER_PATH . 'defaults/components/' . $compSlug;
                    if (file_exists($src)) {
                        $this->fileManager->copyDirectory($src, $dest);
                    }
                }
            }
        }

        // 1. Sauvegarder la configuration JSON
        // Normalize and create page-level assets when necessary
        $themeCssDir = $themePath . '/assets/css';
        $themeJsDir = $themePath . '/assets/js';
        $this->fileManager->createDirectory($themeCssDir);
        $this->fileManager->createDirectory($themeJsDir);

        // Helper to normalize and ensure files
        $normalizeAssets = function ($items, $type) use ($themeCssDir, $themeJsDir, $themePath) {
            $out = [];
            if (!is_array($items)) return $out;
            foreach ($items as $it) {
                $it = trim($it);
                if ($it === '') continue;
                // external URL
                if (preg_match('#^https?://#i', $it) || strpos($it, '//') === 0) {
                    $out[] = $it;
                    continue;
                }
                // already a path (assets/ or components/ or contains slash)
                if (strpos($it, 'assets/') === 0 || strpos($it, 'components/') === 0 || strpos($it, '/') !== false) {
                    $out[] = $it;
                    continue;
                }

                // simple filename -> place under assets/css or assets/js
                $filename = sanitize_file_name($it);
                if ($type === 'css') {
                    $destRel = 'assets/css/' . $filename;
                    $destAbs = $themeCssDir . '/' . $filename;
                    if (!file_exists($destAbs)) {
                        $content = "/* BYT3LAB page asset - $filename */\n";
                        file_put_contents($destAbs, $content);
                    }
                    $out[] = $destRel;
                } else {
                    $destRel = 'assets/js/' . $filename;
                    $destAbs = $themeJsDir . '/' . $filename;
                    if (!file_exists($destAbs)) {
                        $content = "// BYT3LAB page asset - $filename\n";
                        file_put_contents($destAbs, $content);
                    }
                    $out[] = $destRel;
                }
            }
            return $out;
        };

        // Normalize css_files/js_files into paths under assets/ if simple filenames provided
        if (!empty($data['css_files']) && is_array($data['css_files'])) {
            $data['css_files'] = $normalizeAssets($data['css_files'], 'css');
        }
        if (!empty($data['js_files']) && is_array($data['js_files'])) {
            $data['js_files'] = $normalizeAssets($data['js_files'], 'js');
        }

        $jsonPath = $pagesDir . '/page-' . $slug . '.json';
        $this->fileManager->putContents($jsonPath, json_encode($data, JSON_PRETTY_PRINT));

        // 2. Générer le fichier PHP final
        $pagePath = $pagesDir . '/page-' . $slug . '.php';

        // Generate a content-only page template (assets handled by functions.php enqueue hook)
        $phpTemplate = <<<'PHP'
<?php
/*
Template Name: __TITLE__
Description: __DESCRIPTION__
*/
// Assets (CSS/JS) for this page are enqueued by functions.php via wp_enqueue_scripts.
// Edit pages/__PAGE_SLUG__.json to add/remove/reorder assets and components.
?>

<main id="primary" class="site-main">

__COMPONENTS__

</main>
PHP;

        $componentsHtml = '';
        if (!empty($data['components'])) {
            foreach ($data['components'] as $comp) {
                $compEsc = esc_html($comp);
                $componentsHtml .= "<?php get_template_part('components/{$compEsc}/{$compEsc}'); ?>\n";
            }
        } else {
            $componentsHtml = "    <!-- Le contenu généré ou les composants iront ici -->\n";
        }

        $phpContent = str_replace([
            '__TITLE__',
            '__DESCRIPTION__',
            '__PAGE_SLUG__',
            '__COMPONENTS__'
        ], [
            esc_html($data['title']),
            esc_html($data['description'] ?? ''),
            $slug,
            $componentsHtml
        ], $phpTemplate);

        $this->fileManager->putContents($pagePath, $phpContent);

        // Ensure dynamic templates updated (page.php / front-page / 404 / home)
        $templateManager = new TemplateManager();
        $templateManager->ensureDynamicTemplates($themePath);

        // Update config (pages only)
        $config = $this->configManager->readConfig($themePath);
        if ($config) {
            // Ensure pages array exists
            if (!isset($config['pages'])) {
                $config['pages'] = [];
            }

            // Check if page already exists
            $exists = false;
            foreach ($config['pages'] as &$p) {
                if ($p['filename'] === 'pages/page-' . $slug . '.php') {
                    $p['title'] = sanitize_text_field($data['title']);
                    $exists = true;
                }
            }
            if (!$exists) {
                $config['pages'][] = [
                    'title' => sanitize_text_field($data['title']),
                    'filename' => 'pages/page-' . $slug . '.php'
                ];
            }

            $configJson = json_encode($config, JSON_PRETTY_PRINT);
            $this->fileManager->putContents($themePath . '/config.json', $configJson);
        }


        return true;
    }

    /**
     * Delete a page from a theme (files + config update)
     */
    public function delete($theme, $slug)
    {
        $themePath = WP_CONTENT_DIR . '/themes/' . $theme;
        $pagesDir = $themePath . '/pages';
        $phpPath = $pagesDir . '/page-' . $slug . '.php';
        $jsonPath = $pagesDir . '/page-' . $slug . '.json';

        if (!file_exists($phpPath) && !file_exists($jsonPath)) {
            return new \WP_Error('page_not_found', 'Page introuvable.');
        }

        if (file_exists($phpPath)) {
            unlink($phpPath);
        }
        if (file_exists($jsonPath)) {
            unlink($jsonPath);
        }

        // Update config
        $config = $this->configManager->readConfig($themePath);
        if ($config && !empty($config['pages']) && is_array($config['pages'])) {
            $config['pages'] = array_values(array_filter($config['pages'], function ($p) use ($slug) {
                return ($p['filename'] ?? '') !== 'pages/page-' . $slug . '.php';
            }));
            $this->fileManager->putContents($themePath . '/config.json', json_encode($config, JSON_PRETTY_PRINT));
        }

        return true;
    }
}
