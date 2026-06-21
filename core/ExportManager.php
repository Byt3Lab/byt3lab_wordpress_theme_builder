<?php

namespace Byt3lab\Builder\Core;

class ExportManager
{
    public function exportTheme($slug)
    {
        $themePath = WP_CONTENT_DIR . '/themes/' . $slug;
        if (!file_exists($themePath)) {
            return new \WP_Error('theme_not_found', 'Thème introuvable.');
        }

        $uploadDir = wp_upload_dir();
        $zipPath = $uploadDir['basedir'] . '/byt3lab_exports/';
        $zipUrl = $uploadDir['baseurl'] . '/byt3lab_exports/';

        if (!file_exists($zipPath)) {
            mkdir($zipPath, 0755, true);
        }

        $zipFile = $zipPath . $slug . '.zip';

        if (class_exists('ZipArchive')) {
            $zip = new \ZipArchive();
            if ($zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($themePath),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                );

                foreach ($files as $name => $file) {
                    if (!$file->isDir()) {
                        $filePath = $file->getRealPath();
                        // Add file to ZIP, using relative path inside the theme
                        $relativePath = substr($filePath, strlen($themePath) + 1);
                        $zip->addFile($filePath, $slug . '/' . $relativePath);
                    }
                }
                $zip->close();

                return [
                    'file' => $zipFile,
                    'url' => $zipUrl . $slug . '.zip'
                ];
            }
            return new \WP_Error('zip_error', 'Erreur lors de la création de l\'archive ZIP.');
        } else {
            return new \WP_Error('no_zip', 'L\'extension PHP ZipArchive n\'est pas activée sur ce serveur.');
        }
    }

    public function importTheme($zipFilePath)
    {
        if (!class_exists('ZipArchive')) {
            return new \WP_Error('no_zip', 'L\'extension PHP ZipArchive n\'est pas activée.');
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipFilePath) === TRUE) {
            $themeDir = WP_CONTENT_DIR . '/themes/';

            $success = $zip->extractTo($themeDir);
            $zip->close();

            if ($success) {
                return true;
            } else {
                return new \WP_Error('extract_failed', 'Échec de l\'extraction de l\'archive.');
            }
        }

        return new \WP_Error('invalid_zip', 'Fichier ZIP invalide ou corrompu.');
    }
}
