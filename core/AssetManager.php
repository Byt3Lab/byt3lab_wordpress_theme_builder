<?php
namespace Byt3lab\Builder\Core;

class AssetManager {
    private $fileManager;

    public function __construct() {
        $this->fileManager = new FileManager();
    }

    public function createAssetFile($theme, $type, $filename) {
        $themePath = WP_CONTENT_DIR . '/themes/' . $theme;
        
        if (!file_exists($themePath)) {
            return new \WP_Error('theme_not_found', 'Theme introuvable.');
        }

        $filename = sanitize_file_name($filename);
        $assetPath = $themePath . '/assets/' . $type . '/' . $filename;
        
        if (file_exists($assetPath)) {
             return new \WP_Error('asset_exists', 'Asset existe déjà.');
        }

        $content = "/* BYT3LAB Builder - $filename */\n";
        $this->fileManager->putContents($assetPath, $content);
        return true;
    }
}
