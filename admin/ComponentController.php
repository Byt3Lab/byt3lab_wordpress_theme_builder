<?php

namespace Byt3lab\Builder\Admin;

use Byt3lab\Builder\Core\ComponentGenerator;
use Byt3lab\Builder\Core\FileManager;

class ComponentController
{
    /**
     * File manager instance.
     *
     * @var FileManager
     */
    private $fileManager;

    /**
     * ComponentController constructor.
     */
    public function __construct()
    {
        $this->fileManager = new FileManager();
    }
    public function render()
    {
        $message = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_component'])) {
            check_admin_referer('generate_component_nonce');

            $theme = sanitize_title($_POST['theme_slug']);
            $data = [
                'name' => sanitize_text_field($_POST['component_name']),
                'type' => sanitize_text_field($_POST['component_type']),
            ];

            $generator = new ComponentGenerator();
            $result = $generator->generate($theme, $data);

            if (is_wp_error($result)) {
                $message = '<div class="notice notice-error is-dismissible"><p>' . esc_html($result->get_error_message()) . '</p></div>';
            } else {
                $message = '<div class="notice notice-success is-dismissible"><p>Composant généré avec succès !</p></div>';
            }
        }

        // Handle ZIP upload of components
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_component_zip'])) {
            check_admin_referer('upload_component_zip');

            if (! current_user_can('manage_options')) {
                $message = '<div class="notice notice-error is-dismissible"><p>Permission refusée.</p></div>';
            } else {
                $theme = sanitize_title($_POST['theme_slug'] ?? '');
                if (empty($theme)) {
                    $message = '<div class="notice notice-error is-dismissible"><p>Veuillez sélectionner un thème.</p></div>';
                } elseif (! isset($_FILES['component_zip']) || empty($_FILES['component_zip']['tmp_name'])) {
                    $message = '<div class="notice notice-error is-dismissible"><p>Aucun fichier ZIP reçu.</p></div>';
                } else {
                    $zipFile = $_FILES['component_zip'];
                    $ext = pathinfo($zipFile['name'], PATHINFO_EXTENSION);
                    if (strtolower($ext) !== 'zip') {
                        $message = '<div class="notice notice-error is-dismissible"><p>Format non pris en charge, veuillez fournir un fichier ZIP.</p></div>';
                    } else {
                        $zip = new \ZipArchive();
                        if ($zip->open($zipFile['tmp_name']) === true) {
                            $tmpDir = WP_CONTENT_DIR . '/uploads/byt3lab_components_' . uniqid();
                            wp_mkdir_p($tmpDir);
                            $zip->extractTo($tmpDir);
                            $zip->close();

                            // Find component directories inside extracted content
                            $found = [];
                            $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($tmpDir, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);
                            foreach ($it as $fileinfo) {
                                if ($fileinfo->isDir()) {
                                    $dir = $fileinfo->getPathname();
                                    $basename = basename($dir);
                                    if (file_exists($dir . '/component.json') || file_exists($dir . '/' . $basename . '.php') || file_exists($dir . '/config.json')) {
                                        $found[] = $dir;
                                    }
                                }
                            }

                            $fileManager = $this->fileManager;
                            $themePath = WP_CONTENT_DIR . '/themes/' . $theme;
                            $componentsPath = $themePath . '/components';
                            $fileManager->createDirectory($componentsPath);

                            $installed = [];
                            $skipped = [];

                            foreach ($found as $srcDir) {
                                $slug = basename($srcDir);
                                $dest = $componentsPath . '/' . $slug;
                                if (file_exists($dest)) {
                                    $skipped[] = $slug;
                                    continue;
                                }
                                $fileManager->copyDirectory($srcDir, $dest);
                                $installed[] = $slug;
                            }

                            // Cleanup
                            $fileManager->deleteDirectory($tmpDir);

                            $msgParts = [];
                            if (!empty($installed)) {
                                $msgParts[] = 'Installés: ' . implode(', ', array_map('esc_html', $installed));
                            }
                            if (!empty($skipped)) {
                                $msgParts[] = 'Ignorés (existent déjà): ' . implode(', ', array_map('esc_html', $skipped));
                            }
                            $message = '<div class="notice notice-success is-dismissible"><p>' . implode(' — ', $msgParts) . '</p></div>';
                        } else {
                            $message = '<div class="notice notice-error is-dismissible"><p>Impossible d\'ouvrir l\'archive ZIP.</p></div>';
                        }
                    }
                }
            }
        }

        // Install default components bundled with the plugin
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['install_defaults'])) {
            check_admin_referer('install_defaults_nonce');

            if (! current_user_can('manage_options')) {
                $message = '<div class="notice notice-error is-dismissible"><p>Permission refusée.</p></div>';
            } else {
                $theme = sanitize_title($_POST['theme_slug'] ?? '');
                $selected = $_POST['defaults'] ?? [];
                if (empty($theme) || empty($selected) || !is_array($selected)) {
                    $message = '<div class="notice notice-error is-dismissible"><p>Aucun composant sélectionné ou thème invalide.</p></div>';
                } else {
                    $fileManager = $this->fileManager;
                    $themePath = WP_CONTENT_DIR . '/themes/' . $theme;
                    $componentsPath = $themePath . '/components';
                    $fileManager->createDirectory($componentsPath);

                    $installed = [];
                    $skipped = [];
                    foreach ($selected as $slug) {
                        $slug = sanitize_file_name($slug);
                        $src = BYT3LAB_BUILDER_PATH . 'defaults/components/' . $slug;
                        if (!file_exists($src)) {
                            $skipped[] = $slug;
                            continue;
                        }
                        $dest = $componentsPath . '/' . $slug;
                        if (file_exists($dest)) {
                            $skipped[] = $slug;
                            continue;
                        }
                        $fileManager->copyDirectory($src, $dest);
                        $installed[] = $slug;
                    }

                    $msgParts = [];
                    if (!empty($installed)) {
                        $msgParts[] = 'Installés: ' . implode(', ', array_map('esc_html', $installed));
                    }
                    if (!empty($skipped)) {
                        $msgParts[] = 'Ignorés: ' . implode(', ', array_map('esc_html', $skipped));
                    }
                    $message = '<div class="notice notice-success is-dismissible"><p>' . implode(' — ', $msgParts) . '</p></div>';
                }
            }
        }

        // Fetch our builder themes to populate dropdown
        $themes = wp_get_themes();
        $builderThemes = [];
        foreach ($themes as $slug => $theme) {
            if (file_exists(WP_CONTENT_DIR . '/themes/' . $slug . '/config.json')) {
                $builderThemes[$slug] = $theme;
            }
        }

        $selectedTheme = $_GET['theme'] ?? get_option('byt3lab_builder_working_theme', '');
        $existingComponents = [];
        if ($selectedTheme && isset($builderThemes[$selectedTheme])) {
            $compDir = WP_CONTENT_DIR . '/themes/' . $selectedTheme . '/components';
            if (file_exists($compDir)) {
                $dirs = array_filter(glob($compDir . '/*'), 'is_dir');
                foreach ($dirs as $dir) {
                    $json = $dir . '/component.json';
                    if (file_exists($json)) {
                        $compData = json_decode(file_get_contents($json), true);
                        if ($compData) {
                            // Extract directory name as slug
                            $compData['slug'] = basename($dir);
                            $existingComponents[] = $compData;
                        }
                    }
                }
            }
        }

        // Load default components bundled with the plugin
        $defaultComponents = [];
        $defaultsDir = BYT3LAB_BUILDER_PATH . 'defaults/components';
        if (file_exists($defaultsDir)) {
            $dirs = array_filter(glob($defaultsDir . '/*'), 'is_dir');
            foreach ($dirs as $dir) {
                $json = $dir . '/component.json';
                $compData = [];
                if (file_exists($json)) {
                    $compData = json_decode(file_get_contents($json), true) ?: [];
                }
                $compData['slug'] = basename($dir);
                if (empty($compData['name'])) {
                    $compData['name'] = $compData['slug'];
                }
                $defaultComponents[] = $compData;
            }
        }

        require BYT3LAB_BUILDER_PATH . 'views/components.php';
    }
}
