<?php
namespace Byt3lab\Builder\Core;

class ThemeGenerator {
    private $fileManager;
    private $configManager;

    public function __construct() {
        $this->fileManager = new FileManager();
        $this->configManager = new ConfigManager($this->fileManager);
    }

    public function generate($data) {
        // Fallback slug to sanitized name if omitted
        if (empty($data['slug'])) {
            $data['slug'] = sanitize_title($data['name']);
        } else {
            $data['slug'] = sanitize_title($data['slug']);
        }
        
        $slug = $data['slug'];
        $themePath = WP_CONTENT_DIR . '/themes/' . $slug;

        if (file_exists($themePath)) {
            return new \WP_Error('theme_exists', 'Un thème avec ce slug existe déjà.');
        }

        // Create main directories
        $dirs = [
            '',
            '/components',
            '/pages',
            '/assets',
            '/assets/css',
            '/assets/js',
            '/assets/images'
        ];

        foreach ($dirs as $dir) {
            $this->fileManager->createDirectory($themePath . $dir);
        }

        // Generate Config
        $this->configManager->createThemeConfig($themePath, $data);

        // Copy and Replace stubs
        $stubs = [
            'style.css',
            'functions.php',
            'index.php',
            'header.php',
            'footer.php',
            'front-page.php'
        ];

        $stubPath = BYT3LAB_BUILDER_PATH . 'stubs/theme/';

        foreach ($stubs as $stub) {
            $content = $this->fileManager->getContents($stubPath . $stub . '.stub');
            
            // Replace placeholders
            $content = str_replace(
                ['{{NAME}}', '{{SLUG}}', '{{VERSION}}', '{{AUTHOR}}', '{{DESCRIPTION}}'],
                [$data['name'], $slug, $data['version'], $data['author'], $data['description']],
                $content
            );

            $this->fileManager->putContents($themePath . '/' . $stub, $content);
        }

        return true;
    }
}
