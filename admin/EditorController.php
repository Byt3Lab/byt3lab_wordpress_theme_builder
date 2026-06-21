<?php

namespace Byt3lab\Builder\Admin;

class EditorController
{
    public function render()
    {
        $message = '';
        $selectedTheme = $_GET['theme'] ?? get_option('byt3lab_builder_working_theme', '');
        $selectedFile = $_GET['file'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_file'])) {
            check_admin_referer('save_file_nonce');
            $content = stripslashes($_POST['file_content']);

            $themeSlug = sanitize_title($_POST['theme'] ?? '');
            $fileRel = isset($_POST['file']) ? str_replace('\\', '/', trim($_POST['file'])) : '';
            $fileRel = ltrim($fileRel, "/");

            // Basic checks to prevent path traversal
            if ($fileRel === '' || strpos($fileRel, '..') !== false) {
                $message = '<div class="notice notice-error is-dismissible"><p>Chemin de fichier non autorisé.</p></div>';
            } else {
                $themeRoot = realpath(WP_CONTENT_DIR . '/themes/' . $themeSlug);
                if ($themeRoot === false) {
                    $message = '<div class="notice notice-error is-dismissible"><p>Thème introuvable.</p></div>';
                } else {
                    $fileToSave = $themeRoot . '/' . $fileRel;
                    $targetDir = dirname($fileToSave);
                    $targetDirReal = realpath($targetDir) ?: $targetDir;

                    if (strpos($targetDirReal, $themeRoot) === 0) {
                        // Prevent accidental edits to index.php unless explicitly confirmed
                        if (basename($fileToSave) === 'index.php' && empty($_POST['confirm_index_edit'])) {
                            $message = '<div class="notice notice-warning is-dismissible"><p>Édition d\'index.php bloquée pour éviter les écrasements accidentels. Cochez la case de confirmation pour modifier.</p></div>';
                        } else {
                            if (!is_dir(dirname($fileToSave))) {
                                @mkdir(dirname($fileToSave), 0755, true);
                            }
                            $written = @file_put_contents($fileToSave, $content);
                            if ($written === false) {
                                $message = '<div class="notice notice-error is-dismissible"><p>Impossible d\'écrire le fichier. Vérifiez les permissions serveur.</p></div>';
                            } else {
                                $message = '<div class="notice notice-success is-dismissible"><p>Fichier sauvegardé avec succès.</p></div>';
                            }
                        }
                    } else {
                        $message = '<div class="notice notice-error is-dismissible"><p>Opération non autorisée.</p></div>';
                    }
                }
            }
        }

        $themes = wp_get_themes();
        $builderThemes = [];
        foreach ($themes as $slug => $theme) {
            if (file_exists(WP_CONTENT_DIR . '/themes/' . $slug . '/config.json')) {
                $builderThemes[$slug] = $theme;
            }
        }

        $files = [];
        $fileContent = '';
        $ext = '';
        if ($selectedTheme && isset($builderThemes[$selectedTheme])) {
            $themePath = WP_CONTENT_DIR . '/themes/' . $selectedTheme;
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($themePath));
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $path = substr($file->getRealPath(), strlen($themePath) + 1);
                    // Filter allowed extensions
                    if (preg_match('/\.(php|css|js|json)$/', $path)) {
                        $files[] = $path;
                    }
                }
            }
            sort($files);

            if ($selectedFile && in_array($selectedFile, $files)) {
                $fileContent = file_get_contents($themePath . '/' . $selectedFile);
                $ext = pathinfo($selectedFile, PATHINFO_EXTENSION);
            }
        }

        // Initialize WordPress native CodeMirror
        $mime = 'application/x-httpd-php';
        if ($ext === 'css') $mime = 'text/css';
        if ($ext === 'js') $mime = 'text/javascript';
        if ($ext === 'json') $mime = 'application/json';

        $settings = wp_enqueue_code_editor(array('type' => $mime));

        require BYT3LAB_BUILDER_PATH . 'views/editor.php';
    }
}
