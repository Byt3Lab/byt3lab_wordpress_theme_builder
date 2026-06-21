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

        // HTML/PHP
        $htmlContent = "<div class=\"component-$compSlug\">\n    <!-- content here -->\n</div>\n";
        $this->fileManager->putContents($compDir . '/' . $compSlug . '.php', $htmlContent);

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

        // Update config
        $config = $this->configManager->readConfig($themePath);
        if ($config) {
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
}
