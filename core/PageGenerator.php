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

        // 1. Sauvegarder la configuration JSON
        $jsonPath = $pagesDir . '/page-' . $slug . '.json';
        $this->fileManager->putContents($jsonPath, json_encode($data, JSON_PRETTY_PRINT));

        // 2. Générer le fichier PHP final
        $pagePath = $pagesDir . '/page-' . $slug . '.php';

        $phpContent = "<?php\n";
        $phpContent .= "/*\n";
        $phpContent .= "Template Name: " . esc_html($data['title']) . "\n";
        if (!empty($data['description'])) {
            $phpContent .= "Description: " . esc_html($data['description']) . "\n";
        }
        $phpContent .= "*/\n\n";

        $phpContent .= "get_header();\n?>\n\n";

        if (!empty($data['css_files'])) {
            foreach ($data['css_files'] as $css) {
                $phpContent .= '<link rel="stylesheet" href="<?= get_template_directory_uri() ?>/assets/css/' . esc_html($css) . '">' . "\n";
            }
            $phpContent .= "\n";
        }

        $phpContent .= "<main id=\"primary\" class=\"site-main\">\n\n";

        if (!empty($data['components'])) {
            foreach ($data['components'] as $comp) {
                $phpContent .= "<?php get_template_part('components/" . esc_html($comp) . "/" . esc_html($comp) . "'); ?>\n";
            }
        } else {
            $phpContent .= "    <!-- Le contenu généré ou les composants iront ici -->\n";
        }

        $phpContent .= "\n</main>\n\n";

        if (!empty($data['js_files'])) {
            foreach ($data['js_files'] as $js) {
                $phpContent .= '<script src="<?= get_template_directory_uri() ?>/assets/js/' . esc_html($js) . '"></script>' . "\n";
            }
            $phpContent .= "\n";
        }

        $phpContent .= "<?php get_footer(); ?>\n";

        $this->fileManager->putContents($pagePath, $phpContent);

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
