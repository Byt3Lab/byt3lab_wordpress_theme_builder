<?php

namespace Byt3lab\Builder\Admin;

class DashboardController
{
    public function render()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('Vous n\'avez pas les permissions nécessaires pour accéder à cette page.', 'byt3lab-builder'));
        }

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

        // Health Check Statuses
        $healthChecks = [
            'php_version' => [
                'label' => 'Version de PHP (>= 7.4)',
                'status' => version_compare(PHP_VERSION, '7.4.0', '>='),
                'message' => 'Version actuelle : ' . PHP_VERSION,
            ],
            'themes_writable' => [
                'label' => 'Dossier des thèmes accessible en écriture',
                'status' => wp_is_writable(WP_CONTENT_DIR . '/themes'),
                'message' => wp_is_writable(WP_CONTENT_DIR . '/themes') 
                    ? 'Le répertoire wp-content/themes est inscriptible.' 
                    : 'Le répertoire wp-content/themes n\'est pas inscriptible. Veuillez ajuster les permissions pour que le constructeur puisse générer des fichiers.',
            ],
            'permalinks' => [
                'label' => 'Structure des permaliens personnalisée',
                'status' => !empty(get_option('permalink_structure')),
                'message' => !empty(get_option('permalink_structure'))
                    ? 'Les permaliens sont configurés correctement.'
                    : 'Les permaliens sont configurés sur "Simple". Il est fortement recommandé d\'utiliser une structure personnalisée pour éviter tout conflit d\'affichage.',
            ],
            'zip_archive' => [
                'label' => 'Extension PHP ZipArchive',
                'status' => class_exists('ZipArchive'),
                'message' => class_exists('ZipArchive')
                    ? 'L\'extension ZipArchive est disponible.'
                    : 'L\'extension ZipArchive est manquante. Les fonctionnalités d\'importation et d\'exportation au format ZIP ne fonctionneront pas.',
            ],
            'json_extension' => [
                'label' => 'Extension PHP JSON',
                'status' => extension_loaded('json'),
                'message' => extension_loaded('json')
                    ? 'L\'extension JSON est disponible.'
                    : 'L\'extension JSON est manquante. Le builder ne pourra pas lire/écrire ses configurations.',
            ],
        ];

        require BYT3LAB_BUILDER_PATH . "views/dashboard.php";
    }
}
