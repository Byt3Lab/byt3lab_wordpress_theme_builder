<?php

namespace Byt3lab\Builder\Core;

class ThemeGenerator
{
    private $fileManager;
    private $configManager;

    public function __construct()
    {
        $this->fileManager = new FileManager();
        $this->configManager = new ConfigManager($this->fileManager);
    }

    public function generate($data)
    {
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
        $this->applyStubs($themePath, $slug, $data);

        // Apply starter template features
        if (!empty($data['template']) && $data['template'] !== 'base') {
            $pageGen = new \Byt3lab\Builder\Core\PageGenerator();
            $compGen = new \Byt3lab\Builder\Core\ComponentGenerator();

            if ($data['template'] === 'corporate') {
                $pageGen->generate($slug, ['title' => 'Services']);
                $pageGen->generate($slug, ['title' => 'Contact']);
                $compGen->generate($slug, ['name' => 'Hero Banner', 'type' => 'section']);
                $compGen->generate($slug, ['name' => 'Feature List', 'type' => 'element']);
            } elseif ($data['template'] === 'blog') {
                $pageGen->generate($slug, ['title' => 'Blog']);
                $pageGen->generate($slug, ['title' => 'About Me']);
                $compGen->generate($slug, ['name' => 'Author Bio', 'type' => 'element']);
            }
        }

        return true;
    }

    /**
     * Apply theme stubs to a theme directory.
     *
     * @param string $themePath
     * @param string $slug
     * @param array $data
     * @return void
     */
    public function applyStubs($themePath, $slug, $data)
    {
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
    }

    /**
     * Delete a generated theme directory. Prevent deleting active theme.
     */
    public function delete($slug)
    {
        $themePath = WP_CONTENT_DIR . '/themes/' . $slug;
        if (!file_exists($themePath)) {
            return new \WP_Error('theme_not_found', 'Thème introuvable.');
        }

        // Prevent deleting active theme
        if (function_exists('get_stylesheet') && get_stylesheet() === $slug) {
            return new \WP_Error('theme_active', 'Impossible de supprimer le thème actif.');
        }

        // Use FileManager to remove directory
        $fm = $this->fileManager;
        $fm->deleteDirectory($themePath);
        return true;
    }
}
