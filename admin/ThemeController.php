<?php
namespace Byt3lab\Builder\Admin;

use Byt3lab\Builder\Core\ThemeGenerator;

class ThemeController {
    public function render() {
        // Handle form submission
        $message = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_theme'])) {
            check_admin_referer('generate_theme_nonce');
            
            $data = [
                'name' => sanitize_text_field($_POST['theme_name']),
                'slug' => sanitize_title($_POST['theme_slug']),
                'author' => sanitize_text_field($_POST['theme_author']),
                'description' => sanitize_textarea_field($_POST['theme_description']),
                'version' => sanitize_text_field($_POST['theme_version'])
            ];

            $generator = new ThemeGenerator();
            $result = $generator->generate($data);

            if (is_wp_error($result)) {
                $message = '<div class="notice notice-error is-dismissible"><p>' . esc_html($result->get_error_message()) . '</p></div>';
            } else {
                $message = '<div class="notice notice-success is-dismissible"><p>Thème généré avec succès !</p></div>';
            }
        }

        // Fetch existing themes
        $themes = wp_get_themes();
        $builderThemes = [];
        
        foreach ($themes as $slug => $theme) {
            if (file_exists(WP_CONTENT_DIR . '/themes/' . $slug . '/config.json')) {
                // Read from config to verify it's our builder theme
                $builderThemes[$slug] = $theme;
            }
        }

        require BYT3LAB_BUILDER_PATH . 'views/themes.php';
    }
}
