<?php

namespace Byt3lab\Builder\Admin;

use Byt3lab\Builder\Core\ExportManager;

class ExportController
{
    public function render()
    {
        $message = '';
        $downloadUrl = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['export_theme'])) {
            check_admin_referer('export_theme_nonce');

            $themeSlug = sanitize_title($_POST['theme_slug']);
            $exporter = new ExportManager();
            $result = $exporter->exportTheme($themeSlug);

            if (is_wp_error($result)) {
                $message = '<div class="notice notice-error is-dismissible"><p>' . esc_html($result->get_error_message()) . '</p></div>';
            } else {
                $downloadUrl = esc_url($result['url']);
                $message = '<div class="notice notice-success is-dismissible"><p>Thème exporté avec succès !</p></div>';
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['import_theme']) && isset($_FILES['theme_zip'])) {
            check_admin_referer('import_theme_nonce');

            $file = $_FILES['theme_zip'];
            if ($file['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                if (strtolower($ext) !== 'zip') {
                    $message = '<div class="notice notice-error is-dismissible"><p>Le fichier doit être une archive ZIP.</p></div>';
                } else {
                    $exporter = new ExportManager();
                    $result = $exporter->importTheme($file['tmp_name']);

                    if (is_wp_error($result)) {
                        $message = '<div class="notice notice-error is-dismissible"><p>' . esc_html($result->get_error_message()) . '</p></div>';
                    } else {
                        $message = '<div class="notice notice-success is-dismissible"><p>Thème importé et installé avec succès !</p></div>';
                    }
                }
            } else {
                $message = '<div class="notice notice-error is-dismissible"><p>Erreur lors du téléchargement du fichier.</p></div>';
            }
        } elseif (isset($_GET['action']) && $_GET['action'] === 'export' && !empty($_GET['theme'])) {
            check_admin_referer('export_theme_' . $_GET['theme']);

            $themeSlug = sanitize_title($_GET['theme']);
            $exporter = new ExportManager();
            $result = $exporter->exportTheme($themeSlug);

            if (is_wp_error($result)) {
                $message = '<div class="notice notice-error is-dismissible"><p>' . esc_html($result->get_error_message()) . '</p></div>';
            } else {
                $downloadUrl = esc_url($result['url']);
                $selectedTheme = $themeSlug;
                $message = '<div class="notice notice-success is-dismissible"><p>Thème exporté avec succès !</p></div>';
            }
        }

        // Fetch our builder themes
        $themes = wp_get_themes();
        $builderThemes = [];
        foreach ($themes as $slug => $theme) {
            if (file_exists(WP_CONTENT_DIR . '/themes/' . $slug . '/config.json')) {
                $builderThemes[$slug] = $theme;
            }
        }
        $selectedTheme = $_GET['theme'] ?? get_option('byt3lab_builder_working_theme', '');

        require BYT3LAB_BUILDER_PATH . 'views/export.php';
    }
}
