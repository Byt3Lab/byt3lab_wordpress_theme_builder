<?php
namespace Byt3lab\Builder\Core;

class ComponentGenerator {
    private $fileManager;
    private $configManager;

    public function __construct() {
        $this->fileManager = new FileManager();
        $this->configManager = new ConfigManager($this->fileManager);
    }

    public function generate($theme, $data) {
        $themePath = WP_CONTENT_DIR . '/themes/' . $theme;
        
        if (!file_exists($themePath)) {
            return new \WP_Error('theme_not_found', 'Theme introuvable.');
        }

        $compName = sanitize_text_field($data['name']);
        $compSlug = sanitize_title($compName);
        $compDir = $themePath . '/components/' . $compSlug;
        
        $this->fileManager->createDirectory($compDir);

        // HTML/PHP with automatic asset inclusion (deduplicated)
            // Build component PHP using a template to avoid escaping issues
            $phpTemplate = <<<'PHP'
    <?php
    // BYT3LAB Builder - auto-include component assets for __SLUG__
    if (!isset($GLOBALS['byt3lab_assets_printed'])) {
        $GLOBALS['byt3lab_assets_printed'] = ['css' => [], 'js' => []];
    }
    $__byt3lab_comp_slug = '__SLUG__';
    $__byt3lab_css_path = get_stylesheet_directory() . '/components/' . $__byt3lab_comp_slug . '/' . $__byt3lab_comp_slug . '.css';
    $__byt3lab_js_path = get_stylesheet_directory() . '/components/' . $__byt3lab_comp_slug . '/' . $__byt3lab_comp_slug . '.js';
    $__byt3lab_css_uri = get_stylesheet_directory_uri() . '/components/' . $__byt3lab_comp_slug . '/' . $__byt3lab_comp_slug . '.css';
    $__byt3lab_js_uri = get_stylesheet_directory_uri() . '/components/' . $__byt3lab_comp_slug . '/' . $__byt3lab_comp_slug . '.js';
    if (file_exists($__byt3lab_css_path) && !in_array($__byt3lab_css_uri, $GLOBALS['byt3lab_assets_printed']['css'])) {
        echo '<link rel="stylesheet" href="' . esc_url($__byt3lab_css_uri) . '" />' . "\n";
        $GLOBALS['byt3lab_assets_printed']['css'][] = $__byt3lab_css_uri;
    }
    if (file_exists($__byt3lab_js_path) && !in_array($__byt3lab_js_uri, $GLOBALS['byt3lab_assets_printed']['js'])) {
        echo '<script src="' . esc_url($__byt3lab_js_uri) . '"></script>' . "\n";
        $GLOBALS['byt3lab_assets_printed']['js'][] = $__byt3lab_js_uri;
    }
    ?>

    <div class="component-__SLUG__">
        <!-- content here -->
    </div>
    PHP;

            $phpContent = str_replace('__SLUG__', $compSlug, $phpTemplate);
        $this->fileManager->putContents($compDir . '/' . $compSlug . '.php', $phpContent);

        // CSS
        $cssContent = ".component-$compSlug {\n    /* styles here */\n}\n";
        $this->fileManager->putContents($compDir . '/' . $compSlug . '.css', $cssContent);

        // JS
        $jsContent = "console.log('Component $compName loaded');\n";
        $this->fileManager->putContents($compDir . '/' . $compSlug . '.js', $jsContent);

        // Component Config
        $compConfig = [
            'name' => $compName,
            'type' => sanitize_text_field($data['type']),
        ];
        $this->fileManager->putContents($compDir . '/component.json', json_encode($compConfig, JSON_PRETTY_PRINT));

        // Update config (components only)
        $config = $this->configManager->readConfig($themePath);
        if ($config) {
            if (!isset($config['components']) || !is_array($config['components'])) {
                $config['components'] = [];
            }

            $config['components'][] = [
                'name' => $compName,
                'slug' => $compSlug,
                'type' => $compConfig['type']
            ];

            $configJson = json_encode($config, JSON_PRETTY_PRINT);
            $this->fileManager->putContents($themePath . '/config.json', $configJson);
        }

        return true;
    }

    /**
     * Delete a component from a theme (files + config update)
     */
    public function delete($theme, $compSlug)
    {
        $themePath = WP_CONTENT_DIR . '/themes/' . $theme;
        $compDir = $themePath . '/components/' . $compSlug;

        if (!file_exists($compDir)) {
            return new \WP_Error('component_not_found', 'Composant introuvable.');
        }

        // Remove files
        $this->fileManager->deleteDirectory($compDir);

        // Update config
        $config = $this->configManager->readConfig($themePath);
        if ($config) {
            if (!empty($config['components']) && is_array($config['components'])) {
                $config['components'] = array_values(array_filter($config['components'], function ($c) use ($compSlug) {
                    return ($c['slug'] ?? '') !== $compSlug;
                }));
            }

            // Remove any leftover asset references for this component
            if (!empty($config['assets']) && is_array($config['assets'])) {
                foreach (['css','js'] as $t) {
                    if (!empty($config['assets'][$t]) && is_array($config['assets'][$t])) {
                        $config['assets'][$t] = array_values(array_filter($config['assets'][$t], function ($a) use ($compSlug) {
                            return strpos($a, 'components/' . $compSlug . '/') !== 0 ? true : false;
                        }));
                    }
                }
            }

            $this->fileManager->putContents($themePath . '/config.json', json_encode($config, JSON_PRETTY_PRINT));
        }

        return true;
    }
}
