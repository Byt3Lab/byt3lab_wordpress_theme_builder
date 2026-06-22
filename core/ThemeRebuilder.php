<?php

namespace Byt3lab\Builder\Core;

class ThemeRebuilder
{
    private $fileManager;
    private $configManager;
    private $themeGenerator;
    private $templateManager;

    public function __construct()
    {
        $this->fileManager = new FileManager();
        $this->configManager = new ConfigManager($this->fileManager);
        $this->themeGenerator = new ThemeGenerator();
        $this->templateManager = new TemplateManager();
    }

    /**
     * Rebuild a theme's core files.
     *
     * @param string $themeSlug
     * @return bool|\WP_Error
     */
    public function rebuild($themeSlug)
    {
        $themePath = WP_CONTENT_DIR . '/themes/' . $themeSlug;
        if (!file_exists($themePath)) {
            return new \WP_Error('theme_not_found', 'Thème introuvable.');
        }

        $config = $this->configManager->readConfig($themePath);
        if (!$config) {
            return new \WP_Error('config_not_found', 'Fichier config.json introuvable ou malformé.');
        }

        // 1. Refresh stubs (functions.php, header.php, footer.php, index.php, front-page.php, style.css)
        $data = [
            'name' => $config['name'] ?? $themeSlug,
            'version' => $config['version'] ?? '1.0.0',
            'author' => $config['author'] ?? '',
            'description' => $config['description'] ?? '',
        ];

        $this->themeGenerator->applyStubs($themePath, $themeSlug, $data);

        // 2. Ensure dynamic templates (page.php, and specialized front-page/404/etc)
        $this->templateManager->ensureDynamicTemplates($themePath);

        return true;
    }
}
