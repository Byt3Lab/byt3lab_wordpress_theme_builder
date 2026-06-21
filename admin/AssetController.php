<?php
namespace Byt3lab\Builder\Admin;

use Byt3lab\Builder\Core\AssetManager;

class AssetController {
    public function render() {
        $message = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_asset'])) {
            check_admin_referer('generate_asset_nonce');
            
            $theme = sanitize_title($_POST['theme_slug']);
            $type = sanitize_text_field($_POST['asset_type']);
            $filename = sanitize_file_name($_POST['asset_filename']);

            // Ensure extension is there
            if (!str_ends_with($filename, '.' . $type)) {
                $filename .= '.' . $type;
            }

            $generator = new AssetManager();
            $result = $generator->createAssetFile($theme, $type, $filename);

            if (is_wp_error($result)) {
                $message = '<div class="notice notice-error is-dismissible"><p>' . esc_html($result->get_error_message()) . '</p></div>';
            } else {
                $message = '<div class="notice notice-success is-dismissible"><p>Asset créé avec succès !</p></div>';
            }
        }

        $themes = wp_get_themes();
        $builderThemes = [];
        foreach ($themes as $slug => $theme) {
            if (file_exists(WP_CONTENT_DIR . '/themes/' . $slug . '/config.json')) {
                $builderThemes[$slug] = $theme;
            }
        }

        require BYT3LAB_BUILDER_PATH . 'views/assets.php';
    }
}
