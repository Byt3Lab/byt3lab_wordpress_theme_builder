<?php

namespace Byt3lab\Builder\Admin;

use Byt3lab\Builder\Core\ThemeGenerator;

class ThemeController
{
    public function render()
    {
        // Handle form submission
        $message = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_theme'])) {
            check_admin_referer('generate_theme_nonce');

            $data = [
                'name' => sanitize_text_field($_POST['theme_name']),
                'slug' => sanitize_title($_POST['theme_slug']),
                'author' => sanitize_text_field($_POST['theme_author']),
                'description' => sanitize_textarea_field($_POST['theme_description']),
                'version' => sanitize_text_field($_POST['theme_version']),
                'template' => sanitize_text_field($_POST['theme_template'] ?? 'base')
            ];

            $generator = new ThemeGenerator();
            $result = $generator->generate($data);

            if ($result) {
                $message = '<div class="notice notice-success is-dismissible"><p>Thème créé avec succès !</p></div>';
            } else {
                $message = '<div class="notice notice-error is-dismissible"><p>Erreur lors de la création du thème.</p></div>';
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_screenshot'])) {
            check_admin_referer('upload_screenshot_nonce');
            $themeSlug = sanitize_title($_POST['theme_slug']);

            // Check if theme exists and is ours
            $themeDir = WP_CONTENT_DIR . '/themes/' . $themeSlug;
            if (file_exists($themeDir . '/config.json')) {
                if (isset($_FILES['screenshot_file']) && $_FILES['screenshot_file']['error'] === UPLOAD_ERR_OK) {
                    $tmp = $_FILES['screenshot_file']['tmp_name'];
                    $ext = strtolower(pathinfo($_FILES['screenshot_file']['name'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['png', 'jpg', 'jpeg'])) {
                        $dest = $themeDir . '/screenshot.' . $ext;
                        if (move_uploaded_file($tmp, $dest)) {
                            $message = '<div class="notice notice-success is-dismissible"><p>Capture d\'écran mise à jour !</p></div>';
                        } else {
                            $message = '<div class="notice notice-error is-dismissible"><p>Erreur lors du déplacement du fichier image.</p></div>';
                        }
                    } else {
                        $message = '<div class="notice notice-error is-dismissible"><p>Seules les images PNG et JPG sont autorisées pour la capture d\'écran (screenshot).*</p></div>';
                    }
                }
            } else {
                $message = '<div class="notice notice-error is-dismissible"><p>Thème introuvable.</p></div>';
            }
        }

        // Handle theme deletion
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_theme'])) {
            check_admin_referer('delete_theme_nonce');
            if (! current_user_can('manage_options')) {
                $message = '<div class="notice notice-error is-dismissible"><p>Permission refusée.</p></div>';
            } else {
                $themeSlug = sanitize_title($_POST['theme_slug'] ?? '');
                if (empty($themeSlug)) {
                    $message = '<div class="notice notice-error is-dismissible"><p>Spécifiez un thème.</p></div>';
                } else {
                    $generator = new ThemeGenerator();
                    $res = $generator->delete($themeSlug);
                    if (is_wp_error($res)) {
                        $message = '<div class="notice notice-error is-dismissible"><p>' . esc_html($res->get_error_message()) . '</p></div>';
                    } else {
                        $message = '<div class="notice notice-success is-dismissible"><p>Thème supprimé.</p></div>';
                    }
                }
            }
        }

        // Fetch our builder themes
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
