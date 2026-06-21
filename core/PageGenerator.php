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

        $pagesDir = $themePath . '/pages';
        if (!file_exists($pagesDir)) {
            $this->fileManager->createDirectory($pagesDir);
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
        $jsonPath = $pagesDir . '/page-' . $slug . '.json';
        $this->fileManager->putContents($jsonPath, json_encode($data, JSON_PRETTY_PRINT));

        // 2. Générer le fichier PHP final
        $pagePath = $pagesDir . '/page-' . $slug . '.php';

        // Generate a content-only page template (header/footer managed by page.php loader)
        $phpContent = "<?php\n";
        $phpContent .= "/*\n";
        $phpContent .= "Template Name: " . esc_html($data['title']) . "\n";
        if (!empty($data['description'])) {
            $phpContent .= "Description: " . esc_html($data['description']) . "\n";
        }
        $phpContent .= "*/\n\n";

        $phpContent .= "?>\n\n";

        $phpContent .= "<main id=\"primary\" class=\"site-main\">\n\n";

        if (!empty($data['components'])) {
            foreach ($data['components'] as $comp) {
                $compEsc = esc_html($comp);
                $phpContent .= "<?php get_template_part('components/{$compEsc}/{$compEsc}'); ?>\n";
            }
        } else {
            $phpContent .= "    <!-- Le contenu généré ou les composants iront ici -->\n";
        }

        $phpContent .= "\n</main>\n";

        $this->fileManager->putContents($pagePath, $phpContent);

        // Ensure dynamic templates updated (page.php / front-page / 404 / home)
        $templateManager = new TemplateManager();
        $templateManager->ensureDynamicTemplates($themePath);

        // Update config
        $config = $this->configManager->readConfig($themePath);
        if ($config) {
            // Check if page already exists
            $exists = false;
            if (!isset($config['pages'])) {
                $config['pages'] = [];
            }
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
}
