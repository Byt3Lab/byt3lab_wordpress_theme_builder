<?php
namespace Byt3lab\Builder\Core;

class PageGenerator {
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

        $pageSlug = sanitize_title($data['title']);
        $filename = $pageSlug . '.php';
        
        $templateContent = "<?php\n/*\nTemplate Name: " . sanitize_text_field($data['title']) . "\n*/\n?>\n";
        $templateContent .= "<?php get_header(); ?>\n";
        $templateContent .= "<main class=\"page-$pageSlug\">\n";
        $templateContent .= "    <h1>" . sanitize_text_field($data['title']) . "</h1>\n";
        $templateContent .= "</main>\n";
        $templateContent .= "<?php get_footer(); ?>\n";

        $this->fileManager->putContents($themePath . '/pages/' . $filename, $templateContent);

        // Update config
        $config = $this->configManager->readConfig($themePath);
        if ($config) {
            $config['pages'][] = [
                'title' => sanitize_text_field($data['title']),
                'filename' => $filename
            ];
            $configJson = json_encode($config, JSON_PRETTY_PRINT);
            $this->fileManager->putContents($themePath . '/config.json', $configJson);
        }

        return true;
    }
}
