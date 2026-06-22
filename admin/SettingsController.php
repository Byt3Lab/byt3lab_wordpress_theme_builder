<?php

namespace Byt3lab\Builder\Admin;

use Byt3lab\Builder\Core\FileManager;
use Byt3lab\Builder\Core\ConfigManager;
use Byt3lab\Builder\Core\TemplateManager;

class SettingsController
{
    public function render()
    {
        $message = '';

        // Detect oversized POSTs (common cause of "Le lien suivi est expiré")
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $contentLen = isset($_SERVER['CONTENT_LENGTH']) ? (int) $_SERVER['CONTENT_LENGTH'] : 0;
            $postMax = $this->parseBytes(ini_get('post_max_size'));
            if ($contentLen > 0 && $postMax > 0 && $contentLen > $postMax && empty($_POST)) {
                $message = '<div class="notice notice-error is-dismissible"><p>La requête POST est trop volumineuse. Réduisez la taille du fichier ou augmentez <code>post_max_size</code> / <code>upload_max_filesize</code> dans php.ini.</p></div>';
                // Do not attempt to process POST data further when it's truncated by PHP
                require BYT3LAB_BUILDER_PATH . 'views/settings.php';
                return;
            }
        }

        // Save global plugin settings
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
            check_admin_referer('save_settings_nonce');

            update_option('byt3lab_css_framework', sanitize_text_field($_POST['css_framework']));
            update_option('byt3lab_auto_generate', isset($_POST['auto_generate']) ? '1' : '0');
            update_option('byt3lab_auto_backup', isset($_POST['auto_backup']) ? '1' : '0');

            $message = '<div class="notice notice-success is-dismissible"><p>Paramètres enregistrés !</p></div>';
        }

        // Save per-theme special page mappings
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_theme_mappings'])) {
            check_admin_referer('save_theme_mappings_nonce');

            if (! current_user_can('manage_options')) {
                $message = '<div class="notice notice-error is-dismissible"><p>Permission refusée.</p></div>';
            } else {
                $theme = sanitize_title($_POST['theme_slug'] ?? ($_POST['theme_select'] ?? ''));
                $front = sanitize_text_field($_POST['front_page'] ?? '');
                $notFound = sanitize_text_field($_POST['not_found_page'] ?? '');
                $posts = sanitize_text_field($_POST['posts_page'] ?? '');

                if (empty($theme)) {
                    $message = '<div class="notice notice-error is-dismissible"><p>Veuillez sélectionner un thème.</p></div>';
                } else {
                    $fileManager = new FileManager();
                    $configManager = new ConfigManager($fileManager);
                    $themePath = WP_CONTENT_DIR . '/themes/' . $theme;
                    $config = $configManager->readConfig($themePath) ?: [];
                    $config['special_pages'] = [
                        'front_page' => $front,
                        'not_found'  => $notFound,
                        'posts_page' => $posts,
                    ];
                    $fileManager->putContents($themePath . '/config.json', json_encode($config, JSON_PRETTY_PRINT));

                    // Apply template changes
                    $templateManager = new TemplateManager();
                    $templateManager->ensureDynamicTemplates($themePath);

                    $message = '<div class="notice notice-success is-dismissible"><p>Mappage des pages sauvegardé et templates mis à jour.</p></div>';
                }
            }
        }

        // Rebuild theme core files
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rebuild_theme'])) {
            check_admin_referer('rebuild_theme_nonce');

            if (! current_user_can('manage_options')) {
                $message = '<div class="notice notice-error is-dismissible"><p>Permission refusée.</p></div>';
            } else {
                $theme = sanitize_title($_POST['theme_slug'] ?? '');
                if (empty($theme)) {
                    $message = '<div class="notice notice-error is-dismissible"><p>Veuillez sélectionner un thème à reconstruire.</p></div>';
                } else {
                    $rebuilder = new \Byt3lab\Builder\Core\ThemeRebuilder();
                    $result = $rebuilder->rebuild($theme);

                    if (is_wp_error($result)) {
                        $message = '<div class="notice notice-error is-dismissible"><p>Erreur : ' . esc_html($result->get_error_message()) . '</p></div>';
                    } else {
                        $message = '<div class="notice notice-success is-dismissible"><p>Les fichiers cœurs du thème <strong>' . esc_html($theme) . '</strong> ont été mis à jour avec succès !</p></div>';
                    }
                }
            }
        }

        $css_framework = get_option('byt3lab_css_framework', 'none');
        $auto_generate = get_option('byt3lab_auto_generate', '0');
        $auto_backup = get_option('byt3lab_auto_backup', '0');

        // Fetch themes and pages for UI
        $themes = wp_get_themes();
        $builderThemes = [];
        foreach ($themes as $slug => $theme) {
            if (file_exists(WP_CONTENT_DIR . '/themes/' . $slug . '/config.json')) {
                $builderThemes[$slug] = $theme;
            }
        }

        $fileManager = new FileManager();
        $configManager = new ConfigManager($fileManager);


        $selectedTheme = $_GET['theme'] ?? get_option('byt3lab_builder_working_theme', '');
        $availablePages = [];
        $currentMapping = ['front_page' => '', 'not_found' => '', 'posts_page' => ''];
        if ($selectedTheme && isset($builderThemes[$selectedTheme])) {
            $themePath = WP_CONTENT_DIR . '/themes/' . $selectedTheme;
            $pagesDir = $themePath . '/pages';
            if (file_exists($pagesDir)) {
                $files = glob($pagesDir . '/page-*.json');
                foreach ($files as $f) {
                    $json = json_decode(file_get_contents($f), true);
                    if ($json && isset($json['slug'])) {
                        $availablePages[$json['slug']] = $json['title'] ?? $json['slug'];
                    } else {
                        // fallback to filename based slug
                        $base = basename($f, '.json');
                        $slug = preg_replace('/^page\-/', '', $base);
                        $availablePages[$slug] = $slug;
                    }
                }
            }

            $config = $configManager->readConfig($themePath) ?: [];
            if (!empty($config['special_pages'])) {
                $currentMapping = array_merge($currentMapping, $config['special_pages']);
            }
        }

        require BYT3LAB_BUILDER_PATH . 'views/settings.php';
    }

    /**
     * Parse php.ini size shorthand (e.g. 8M, 128K) into bytes
     *
     * @param string $val
     * @return int
     */
    protected function parseBytes($val)
    {
        $val = trim($val);
        if ($val === '') return 0;
        $unit = strtolower(substr($val, -1));
        $number = (int) $val;
        switch ($unit) {
            case 'g':
                return $number * 1024 * 1024 * 1024;
            case 'm':
                return $number * 1024 * 1024;
            case 'k':
                return $number * 1024;
            default:
                return (int) $val;
        }
    }
}
