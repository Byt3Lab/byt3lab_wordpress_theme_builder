<?php

namespace Byt3lab\Builder\Admin;

class DashboardController
{
    public function render()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_working_theme'])) {
            check_admin_referer('set_working_theme_nonce');
            $selected = sanitize_text_field($_POST['working_theme']);
            update_option('byt3lab_builder_working_theme', $selected);
            echo '<div class="notice notice-success is-dismissible"><p>Thème de travail par défaut mis à jour.</p></div>';
        }

        $themes = wp_get_themes();
        $builderThemesCount = 0;
        $totalPages = 0;
        $totalComponents = 0;
        $activeThemeName = "Aucun";
        $builderThemesArr = [];

        foreach ($themes as $slug => $theme) {
            $themeDir = WP_CONTENT_DIR . '/themes/' . $slug;
            if (file_exists($themeDir . '/config.json')) {
                $builderThemesCount++;
                $builderThemesArr[$slug] = $theme;

                if (get_template() === $slug || get_stylesheet() === $slug) {
                    $activeThemeName = $theme->get('Name');
                }

                $pages = glob($themeDir . '/pages/page-*.json');
                if ($pages) $totalPages += count($pages);

                $compDirs = glob($themeDir . '/components/*', GLOB_ONLYDIR);
                if ($compDirs) $totalComponents += count($compDirs);
            }
        }

        $workingTheme = get_option('byt3lab_builder_working_theme', '');

        require BYT3LAB_BUILDER_PATH . "views/dashboard.php";
    }
}
