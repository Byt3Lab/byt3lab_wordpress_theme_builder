<?php

namespace Byt3lab\Builder\Admin;

use Byt3lab\Builder\Core\AssetManager;

class AssetController
{
    public function render()
    {
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
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_asset'])) {
            check_admin_referer('upload_asset_nonce');

            $theme = sanitize_title($_POST['theme_slug']);
            $type = sanitize_text_field($_POST['asset_type']);

            if (isset($_FILES['asset_file']) && $_FILES['asset_file']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['asset_file'];

                $manager = new AssetManager();
                $result = $manager->uploadAssetFile($theme, $type, $file);

                if (is_wp_error($result)) {
                    $message = '<div class="notice notice-error is-dismissible"><p>' . esc_html($result->get_error_message()) . '</p></div>';
                } else {
                    $message = '<div class="notice notice-success is-dismissible"><p>Fichier uploadé avec succès !</p></div>';
                }
            } else {
                $message = '<div class="notice notice-error is-dismissible"><p>Erreur lors du transfert du fichier.</p></div>';
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_asset'])) {
            check_admin_referer('delete_asset_nonce');
            if (!current_user_can('manage_options')) {
                $message = '<div class="notice notice-error is-dismissible"><p>Permission refusée.</p></div>';
            } else {
                $theme = sanitize_title($_POST['theme_slug'] ?? '');
                $assetPathRel = isset($_POST['asset_path']) ? str_replace('\\', '/', trim($_POST['asset_path'])) : '';
                $assetPathRel = ltrim($assetPathRel, '/');
                
                if (empty($theme) || empty($assetPathRel) || strpos($assetPathRel, '..') !== false) {
                    $message = '<div class="notice notice-error is-dismissible"><p>Paramètres invalides.</p></div>';
                } else {
                    $themePath = WP_CONTENT_DIR . '/themes/' . $theme;
                    $absPath = $themePath . '/' . $assetPathRel;
                    if (file_exists($absPath) && strpos($absPath, $themePath . '/assets/') === 0) {
                        unlink($absPath);
                        $message = '<div class="notice notice-success is-dismissible"><p>Asset supprimé avec succès !</p></div>';
                    } else {
                        $message = '<div class="notice notice-error is-dismissible"><p>Fichier introuvable ou chemin non autorisé.</p></div>';
                    }
                }
            }
        }

        $themes = wp_get_themes();
        $builderThemes = [];
        foreach ($themes as $slug => $theme) {
            if (file_exists(WP_CONTENT_DIR . '/themes/' . $slug . '/config.json')) {
                $builderThemes[$slug] = $theme;
            }
        }

        $selectedTheme = $_GET['theme'] ?? get_option('byt3lab_builder_working_theme', '');

        // Scan existing assets
        $existingAssets = [];
        if ($selectedTheme && isset($builderThemes[$selectedTheme])) {
            $themePath = WP_CONTENT_DIR . '/themes/' . $selectedTheme;
            $assetsDir = $themePath . '/assets';
            if (file_exists($assetsDir)) {
                $subDirs = ['css', 'js', 'images', 'fonts', 'media', 'documents', 'others'];
                foreach ($subDirs as $dir) {
                    $dirAbs = $assetsDir . '/' . $dir;
                    if (file_exists($dirAbs)) {
                        $files = array_filter(glob($dirAbs . '/*'), 'is_file');
                        foreach ($files as $f) {
                            $filename = basename($f);
                            $existingAssets[$dir][] = [
                                'name' => $filename,
                                'path' => 'assets/' . $dir . '/' . $filename,
                                'url'  => content_url('/themes/' . $selectedTheme . '/assets/' . $dir . '/' . $filename),
                                'size' => size_format(filesize($f)),
                            ];
                        }
                    }
                }
            }
        }

        require BYT3LAB_BUILDER_PATH . 'views/assets.php';
    }
}
