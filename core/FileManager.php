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

    /**
     * Recursively copy a directory or a single file.
     *
     * @param string $src
     * @param string $dest
     * @return bool
     */
    public function copyDirectory($src, $dest) {
        if (!file_exists($src)) {
            return false;
        }

        if (is_file($src)) {
            $this->createDirectory(dirname($dest));
            return copy($src, $dest);
        }

        $this->createDirectory($dest);
        $items = scandir($src);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $s = $src . DIRECTORY_SEPARATOR . $item;
            $d = $dest . DIRECTORY_SEPARATOR . $item;
            if (is_dir($s)) {
                $this->copyDirectory($s, $d);
            } else {
                copy($s, $d);
            }
        }

        return true;
    }

    /**
     * Recursively delete a directory.
     *
     * @param string $dir
     * @return bool
     */
    public function deleteDirectory($dir) {
        if (!file_exists($dir)) {
            return false;
        }
        if (is_file($dir)) {
            return unlink($dir);
        }

        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }

        return rmdir($dir);
    }
}
