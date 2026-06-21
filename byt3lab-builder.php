<?php
/**
 * Plugin Name: BYT3LAB Builder
 * Description: Environnement de développement intégré pour générer des thèmes WordPress.
 * Version: 1.0.0
 * Author: BYT3LAB
 * Text Domain: byt3lab-builder
 */

if (!defined('ABSPATH')) {
    exit;
}

define('BYT3LAB_BUILDER_VERSION', '1.0.0');
define('BYT3LAB_BUILDER_PATH', plugin_dir_path(__FILE__));
define('BYT3LAB_BUILDER_URL', plugin_dir_url(__FILE__));

// Simple PSR-4 Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'Byt3lab\\Builder\\';
    $base_dir = BYT3LAB_BUILDER_PATH;
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    
    // Map namespaces to directories
    $directories = [
        'Core\\' => 'core/',
        'Admin\\' => 'admin/',
    ];
    
    foreach ($directories as $namespace => $dir) {
        if (strncmp($namespace, $relative_class, strlen($namespace)) === 0) {
            $file = $base_dir . $dir . str_replace('\\', '/', substr($relative_class, strlen($namespace))) . '.php';
            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }
});

// Initialize plugin
add_action('plugins_loaded', function() {
    $app = new \Byt3lab\Builder\Core\Application();
    $app->init();
});
