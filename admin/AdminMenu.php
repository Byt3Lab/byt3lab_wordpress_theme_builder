<?php

namespace Byt3lab\Builder\Admin;

class AdminMenu
{
    public function register()
    {
        add_action('admin_menu', [$this, 'addMenuItems']);
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
}
