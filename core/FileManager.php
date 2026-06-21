<?php
namespace Byt3lab\Builder\Core;

class FileManager {
    public function createDirectory($path) {
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
    }

    public function putContents($path, $contents) {
        file_put_contents($path, $contents);
    }

    public function getContents($path) {
        if (file_exists($path)) {
            return file_get_contents($path);
        }
        return '';
    }
}
