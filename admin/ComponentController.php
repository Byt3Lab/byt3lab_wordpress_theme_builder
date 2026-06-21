<?php

namespace Byt3lab\Builder\Admin;

use Byt3lab\Builder\Core\ComponentGenerator;

class ComponentController
{
    public function render()
    {
        $message = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_component'])) {
            check_admin_referer('generate_component_nonce');

            $theme = sanitize_title($_POST['theme_slug']);
            $data = [
                'name' => sanitize_text_field($_POST['component_name']),
                'type' => sanitize_text_field($_POST['component_type']),
            ];

            $generator = new ComponentGenerator();
            $result = $generator->generate($theme, $data);

            if (is_wp_error($result)) {
                $message = '<div class="notice notice-error is-dismissible"><p>' . esc_html($result->get_error_message()) . '</p></div>';
            } else {
                $message = '<div class="notice notice-success is-dismissible"><p>Composant généré avec succès !</p></div>';
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
        $existingComponents = [];
        if ($selectedTheme && isset($builderThemes[$selectedTheme])) {
            $compDir = WP_CONTENT_DIR . '/themes/' . $selectedTheme . '/components';
            if (file_exists($compDir)) {
                $dirs = array_filter(glob($compDir . '/*'), 'is_dir');
                foreach ($dirs as $dir) {
                    $json = $dir . '/component.json';
                    if (file_exists($json)) {
                        $compData = json_decode(file_get_contents($json), true);
                        if ($compData) {
                            // Extract directory name as slug
                            $compData['slug'] = basename($dir);
                            $existingComponents[] = $compData;
                        }
                    }
                }
            }
        }

        require BYT3LAB_BUILDER_PATH . 'views/components.php';
    }
}
