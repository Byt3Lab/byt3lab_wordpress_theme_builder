<?php

namespace Byt3lab\Builder\Core;

use Byt3lab\Builder\Admin\AdminMenu;

class Application {
    public function init() {
        if (is_admin()) {
            $adminMenu = new AdminMenu();
            $adminMenu->register();
        }
        else {
            add_action('wp_enqueue_scripts', [$this, 'enqueue_theme_assets']);
        }
    }

    public function enqueue_theme_assets() {
        $fileManager = new FileManager();
        $themePath = get_stylesheet_directory();
        $configContent = $fileManager->getContents($themePath . '/config.json');
        if (!$configContent) {
            return;
        }

        $config = json_decode($configContent, true);
        $version = wp_get_theme()->get('Version') ?: BYT3LAB_BUILDER_VERSION;

        // Enqueue CSS
        if (!empty($config['assets']['css']) && is_array($config['assets']['css'])) {
            foreach ($config['assets']['css'] as $asset) {
                $asset = ltrim($asset, '/');
                // Only enqueue external or theme-level assets (assets/ folder). Skip component/page-local assets.
                $isExternal = (preg_match('#^https?://#i', $asset) || strpos($asset, '//') === 0);
                $isThemeAsset = (strpos($asset, 'assets/') === 0);
                if (!$isExternal && !$isThemeAsset) {
                    continue;
                }
                $src = $isExternal ? $asset : get_stylesheet_directory_uri() . '/' . $asset;
                $handle = sanitize_key('byt3lab-' . str_replace(['/', '\\', '.', ' '], '-', $asset));
                if (!wp_style_is($handle, 'enqueued')) {
                    wp_enqueue_style($handle, $src, [], $version);
                }
            }
        }

        // Enqueue JS
        if (!empty($config['assets']['js']) && is_array($config['assets']['js'])) {
            foreach ($config['assets']['js'] as $asset) {
                $asset = ltrim($asset, '/');
                // Only enqueue external or theme-level assets (assets/ folder). Skip component/page-local assets.
                $isExternal = (preg_match('#^https?://#i', $asset) || strpos($asset, '//') === 0);
                $isThemeAsset = (strpos($asset, 'assets/') === 0);
                if (!$isExternal && !$isThemeAsset) {
                    continue;
                }
                $src = $isExternal ? $asset : get_stylesheet_directory_uri() . '/' . $asset;
                $handle = sanitize_key('byt3lab-' . str_replace(['/', '\\', '.', ' '], '-', $asset));
                if (!wp_script_is($handle, 'enqueued')) {
                    wp_enqueue_script($handle, $src, [], $version, true);
                }
            }
        }
    }
}
