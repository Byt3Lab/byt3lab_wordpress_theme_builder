<?php
namespace Byt3lab\Builder\Admin;

class SettingsController {
    public function render() {
        $message = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
            check_admin_referer('save_settings_nonce');
            
            update_option('byt3lab_css_framework', sanitize_text_field($_POST['css_framework']));
            update_option('byt3lab_auto_generate', isset($_POST['auto_generate']) ? '1' : '0');
            update_option('byt3lab_auto_backup', isset($_POST['auto_backup']) ? '1' : '0');

            $message = '<div class="notice notice-success is-dismissible"><p>Paramètres enregistrés !</p></div>';
        }

        $css_framework = get_option('byt3lab_css_framework', 'none');
        $auto_generate = get_option('byt3lab_auto_generate', '0');
        $auto_backup = get_option('byt3lab_auto_backup', '0');

        require BYT3LAB_BUILDER_PATH . 'views/settings.php';
    }
}
