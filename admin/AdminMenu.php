<?php

namespace Byt3lab\Builder\Admin;

class AdminMenu
{
    public function register()
    {
        add_action('admin_menu', [$this, 'addMenuItems']);
        add_action('admin_notices', [$this, 'checkPermalinks']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueEditorAssets']);
    }

    public function enqueueEditorAssets($hook)
    {
        if (strpos($hook, 'byt3lab-builder-editor') !== false) {
            wp_enqueue_code_editor(array('type' => 'application/x-httpd-php'));
        }
    }

    public function addMenuItems()
    {
        // Main menu
        add_menu_page(
            'BYT3LAB Builder',
            'BYT3LAB Builder',
            'manage_options',
            'byt3lab-builder',
            [new DashboardController(), 'render'],
            'dashicons-layout',
            2
        );

        // Submenus
        add_submenu_page(
            'byt3lab-builder',
            'Dashboard',
            'Dashboard',
            'manage_options',
            'byt3lab-builder-home',
            [new DashboardController(), 'render']
        );

        add_submenu_page(
            'byt3lab-builder',
            'Themes',
            'Themes',
            'manage_options',
            'byt3lab-builder-themes',
            [new ThemeController(), 'render']
        );

        add_submenu_page(
            'byt3lab-builder',
            'Pages',
            'Pages',
            'manage_options',
            'byt3lab-builder-pages',
            [new PageController(), 'render']
        );

        add_submenu_page(
            'byt3lab-builder',
            'Components',
            'Components',
            'manage_options',
            'byt3lab-builder-components',
            [new ComponentController(), 'render']
        );

        add_submenu_page(
            'byt3lab-builder',
            'Assets',
            'Assets',
            'manage_options',
            'byt3lab-builder-assets',
            [new AssetController(), 'render']
        );

        add_submenu_page(
            'byt3lab-builder',
            'Éditeur',
            'Éditeur',
            'manage_options',
            'byt3lab-builder-editor',
            [new EditorController(), 'render']
        );

        add_submenu_page(
            'byt3lab-builder',
            'Export',
            'Export',
            'manage_options',
            'byt3lab-builder-export',
            [new ExportController(), 'render']
        );

        add_submenu_page(
            'byt3lab-builder',
            'Settings',
            'Settings',
            'manage_options',
            'byt3lab-builder-settings',
            [new SettingsController(), 'render']
        );
    }

    /**
     * Check if permalinks are configured correctly and display a notice if not.
     */
    public function checkPermalinks()
    {
        // Only show notice to users who can manage options
        if (!current_user_can('manage_options')) {
            return;
        }

        // Only show notice on BYT3LAB-related pages
        $page = $_GET['page'] ?? '';
        if (strpos($page, 'byt3lab-builder') === false) {
            return;
        }

        $permalinkStructure = get_option('permalink_structure');

        if (empty($permalinkStructure)) {
?>
            <div class="notice notice-warning is-dismissible">
                <p>
                    <strong><?php _e('BYT3LAB Builder :', 'byt3lab-builder'); ?></strong>
                    <?php _e('Les permaliens sont actuellement configurés sur "Simple". Pour un fonctionnement optimal du builder, il est fortement recommandé d\'utiliser une structure de "titre de publication".', 'byt3lab-builder'); ?>
                </p>
                <p>
                    <a href="<?php echo admin_url('options-permalink.php'); ?>" class="button button-primary">
                        <?php _e('Configurer les permaliens', 'byt3lab-builder'); ?>
                    </a>
                </p>
            </div>
<?php
        }
    }
}
