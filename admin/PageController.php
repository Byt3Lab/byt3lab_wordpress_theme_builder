<?php
namespace Byt3lab\Builder\Admin;

use Byt3lab\Builder\Core\PageGenerator;

class PageController {
    public function render() {
        $message = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_page'])) {
            check_admin_referer('generate_page_nonce');
            
            $theme = sanitize_title($_POST['theme_slug']);
            $data = [
                'title' => sanitize_text_field($_POST['page_title'])
            ];

            $generator = new PageGenerator();
            $result = $generator->generate($theme, $data);

            if (is_wp_error($result)) {
                $message = '<div class="notice notice-error is-dismissible"><p>' . esc_html($result->get_error_message()) . '</p></div>';
            } else {
                $message = '<div class="notice notice-success is-dismissible"><p>Page générée avec succès !</p></div>';
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

        require BYT3LAB_BUILDER_PATH . 'views/pages.php';
    }
}
