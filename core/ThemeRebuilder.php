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
     * Rebuild a theme's core files, pages, and components.
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

        // 2. Re-generate all pages from their JSON files
        $pagesDir = $themePath . '/pages';
        if (file_exists($pagesDir)) {
            $pageGenerator = new PageGenerator();
            $pageJsonFiles = glob($pagesDir . '/page-*.json');
            if ($pageJsonFiles) {
                foreach ($pageJsonFiles as $jsonFile) {
                    $jsonData = json_decode($this->fileManager->getContents($jsonFile), true);
                    if ($jsonData) {
                        // Mark as edit so it won't throw 'page_exists' error
                        $_POST['is_edit'] = 1;
                        $pageGenerator->generate($themeSlug, $jsonData);
                    }
                }
            }
        }

        // 3. Re-generate all components while preserving user content
        $componentsDir = $themePath . '/components';
        if (file_exists($componentsDir)) {
            $componentGenerator = new ComponentGenerator();
            $compFolders = glob($componentsDir . '/*', GLOB_ONLYDIR);
            if ($compFolders) {
                foreach ($compFolders as $folder) {
                    $slug = basename($folder);
                    $jsonFile = $folder . '/component.json';
                    $phpFile = $folder . '/' . $slug . '.php';

                    if (file_exists($jsonFile)) {
                        $compData = json_decode($this->fileManager->getContents($jsonFile), true);
                        if ($compData) {
                            $userContent = null;
                            if (file_exists($phpFile)) {
                                $existingPhp = $this->fileManager->getContents($phpFile);
                                $userContent = $this->extractComponentContent($existingPhp, $slug);
                            }
                            // Re-generate component files (PHP template with auto-inclusion)
                            $componentGenerator->generate($themeSlug, $compData, $userContent);
                        }
                    }
                }
            }
        }

        // 4. Ensure dynamic templates (page.php, and specialized front-page/404/etc)
        $this->templateManager->ensureDynamicTemplates($themePath);

        return true;
    }

    /**
     * Extract user content from a component PHP file.
     *
     * @param string $content
     * @param string $slug
     * @return string
     */
    private function extractComponentContent($content, $slug)
    {
        // 1. Try markers
        if (preg_match('/<!-- BYT3LAB-CONTENT-START -->([\s\S]*?)<!-- BYT3LAB-CONTENT-END -->/', $content, $matches)) {
            return trim($matches[1], "\r\n");
        }

        // 2. Try tag matching for class "component-[slug]"
        $pattern = '/<div class="component-' . preg_quote($slug, '/') . '"[^>]*>([\s\S]*?)<\/div>/';
        if (preg_match($pattern, $content, $matches)) {
            $inner = trim($matches[1], "\r\n");
            // If it contains markers inside, clean them up just in case
            $inner = preg_replace('/<!-- BYT3LAB-CONTENT-(START|END) -->/', '', $inner);
            return trim($inner, "\r\n");
        }

        // 3. Fallback: take everything after the first php closing tag if it contains asset header
        if (strpos($content, 'byt3lab_assets_printed') !== false) {
            $parts = explode('?>', $content, 2);
            if (count($parts) === 2) {
                $html = trim($parts[1], "\r\n");
                $openDiv = '<div class="component-' . $slug . '">';
                if (strpos($html, $openDiv) === 0 && substr($html, -6) === '</div>') {
                    $html = substr($html, strlen($openDiv), -6);
                }
                return trim($html, "\r\n");
            }
        }

        // 4. Ultimate fallback
        return "\t\t<!-- content here -->";
    }
}
