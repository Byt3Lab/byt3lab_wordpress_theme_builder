<?php

namespace Byt3lab\Builder\Admin;

use Byt3lab\Builder\Core\PageGenerator;

class PageController
{
    public function render()
    {
        $message = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_page'])) {
            check_admin_referer('generate_page_nonce');

            $theme = sanitize_title($_POST['theme_slug']);
            $slug = sanitize_title($_POST['page_slug'] ?? $_POST['page_title']);
            $isEdit = !empty($_POST['is_edit']);

            $generator = new PageGenerator();
            $result = $generator->generate($theme, [
                'title' => sanitize_text_field($_POST['page_title']),
                'slug' => $slug,
                'description' => sanitize_textarea_field($_POST['page_description'] ?? ''),
                'css_files' => isset($_POST['page_css']) ? array_map('sanitize_text_field', $_POST['page_css']) : [],
                'js_files' => isset($_POST['page_js']) ? array_map('sanitize_text_field', $_POST['page_js']) : [],
                'components' => isset($_POST['page_components']) ? array_map('sanitize_title', $_POST['page_components']) : []
            ]);

            if (is_wp_error($result)) {
                $message = '<div class="notice notice-error is-dismissible"><p>' . esc_html($result->get_error_message()) . '</p></div>';
            } elseif ($result) {
                $message = '<div class="notice notice-success is-dismissible"><p>Page ' . ($isEdit ? 'mise à jour' : 'générée') . ' avec succès !</p></div>';
            } else {
                $message = '<div class="notice notice-error is-dismissible"><p>Erreur lors de la création de la page.</p></div>';
            }
        }

        // Handle page deletion
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_page'])) {
            check_admin_referer('delete_page_nonce');
            if (! current_user_can('manage_options')) {
                $message = '<div class="notice notice-error is-dismissible"><p>Permission refusée.</p></div>';
            } else {
                $theme = sanitize_title($_POST['theme_slug'] ?? '');
                $slug = sanitize_title($_POST['page_slug'] ?? '');
                if (empty($theme) || empty($slug)) {
                    $message = '<div class="notice notice-error is-dismissible"><p>Paramètres invalides.</p></div>';
                } else {
                    $generator = new PageGenerator();
                    $res = $generator->delete($theme, $slug);
                    if (is_wp_error($res)) {
                        $message = '<div class="notice notice-error is-dismissible"><p>' . esc_html($res->get_error_message()) . '</p></div>';
                    } else {
                        $message = '<div class="notice notice-success is-dismissible"><p>Page supprimée.</p></div>';
                    }
                }
            }
        }

        // Fetch our builder themes to populate dropdown
        $themes = wp_get_themes();
        $builderThemes = [];
        foreach ($themes as $slug => $theme) {
            if (file_exists(WP_CONTENT_DIR . '/themes/' . $slug . '/config.json')) {
                $builderThemes[$slug] = $theme;
            }
        }

        $selectedTheme = $_GET['theme'] ?? get_option('byt3lab_builder_working_theme', '');

        // If theme is passed via GET but differs from working theme, just use GET (already prioritized by ??)
        // If they switch via the dropdown form in views/pages.php, the form uses GET method. That's perfect.
        $availableCss = [];
        $availableJs = [];
        $availableComponents = [];
        $existingPages = [];

        if ($selectedTheme && isset($builderThemes[$selectedTheme])) {
            $themeDir = WP_CONTENT_DIR . '/themes/' . $selectedTheme;

            // Scan CSS
            if (file_exists($themeDir . '/assets/css')) {
                $cssFiles = glob($themeDir . '/assets/css/*.css');
                if ($cssFiles) {
                    foreach ($cssFiles as $f) $availableCss[] = basename($f);
                }
            }
            // Scan JS
            if (file_exists($themeDir . '/assets/js')) {
                $jsFiles = glob($themeDir . '/assets/js/*.js');
                if ($jsFiles) {
                    foreach ($jsFiles as $f) $availableJs[] = basename($f);
                }
            }
            // Scan Components
            if (file_exists($themeDir . '/components')) {
                $dirs = array_filter(glob($themeDir . '/components/*'), 'is_dir');
                foreach ($dirs as $d) {
                    $availableComponents[] = basename($d);
                }
            }
            // Scan Existing Pages JSON
            $jsonFiles = glob($themeDir . '/pages/page-*.json');
            if ($jsonFiles) {
                foreach ($jsonFiles as $f) {
                    $data = json_decode(file_get_contents($f), true);
                    if ($data) $existingPages[] = $data;
                }
            }

            // Load editing page config if requested
            $editSlug = sanitize_title($_GET['edit'] ?? '');
            if ($editSlug) {
                $editJsonPath = $themeDir . '/pages/page-' . $editSlug . '.json';
                if (file_exists($editJsonPath)) {
                    $editPageData = json_decode(file_get_contents($editJsonPath), true);
                }
            }
        }

        require BYT3LAB_BUILDER_PATH . 'views/pages.php';
    }
}
