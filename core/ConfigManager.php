<?php
namespace Byt3lab\Builder\Core;

class ConfigManager {
    private $fileManager;

    public function __construct(FileManager $fileManager) {
        $this->fileManager = $fileManager;
    }

    public function createThemeConfig($themePath, $data) {
        $defaultConfig = [
            "name" => $data['name'] ?? 'Undefined',
            "version" => $data['version'] ?? '1.0.0',
            "author" => $data['author'] ?? '',
            "pages" => [],
            "components" => [],
            "assets" => [
                "css" => [],
                "js" => []
            ]
            ,
            "special_pages" => [
                "front_page" => "",
                "not_found" => "",
                "posts_page" => ""
            ]
        ];
        
        $configJson = json_encode($defaultConfig, JSON_PRETTY_PRINT);
        $this->fileManager->putContents($themePath . '/config.json', $configJson);
    }

    public function readConfig($themePath) {
        $content = $this->fileManager->getContents($themePath . '/config.json');
        return $content ? json_decode($content, true) : null;
    }
}
