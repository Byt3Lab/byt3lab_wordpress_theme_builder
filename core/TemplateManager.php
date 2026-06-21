<?php
namespace Byt3lab\Builder\Core;

if (! defined('ABSPATH')) {
    exit;
}

/**
 * TemplateManager
 *
 * Writes dynamic theme templates (page.php, front-page.php, 404.php, home.php)
 * based on theme configuration (special pages) and ensures backups when
 * configured.
 */
class TemplateManager {
    /** @var FileManager */
    protected $fileManager;

    /** @var ConfigManager */
    protected $configManager;

    public function __construct() {
        $this->fileManager = new FileManager();
        $this->configManager = new ConfigManager($this->fileManager);
    }

    /**
     * Ensure dynamic templates are present for a given theme.
     *
     * @param string $themePath Absolute theme path.
     * @return bool|\WP_Error
     */
    public function ensureDynamicTemplates($themePath) {
        if (!file_exists($themePath)) {
            return new \WP_Error('theme_not_found', 'Theme introuvable.');
        }

        $config = $this->configManager->readConfig($themePath);
        $special = $config['special_pages'] ?? [];

        // Write page.php loader
        $this->writeTemplateWithBackup(
            $themePath . '/page.php',
            $this->buildPageLoaderContent()
        );

        // front-page
        $frontSlug = $special['front_page'] ?? '';
        if ($frontSlug) {
            $this->writeTemplateWithBackup(
                $themePath . '/front-page.php',
                $this->buildStaticPageIncludeContent($frontSlug)
            );
        }

        // 404
        $notFoundSlug = $special['not_found'] ?? '';
        if ($notFoundSlug) {
            $this->writeTemplateWithBackup(
                $themePath . '/404.php',
                $this->build404IncludeContent($notFoundSlug)
            );
        }

        // posts page (blog)
        $postsSlug = $special['posts_page'] ?? '';
        if ($postsSlug) {
            $this->writeTemplateWithBackup(
                $themePath . '/home.php',
                $this->buildStaticPageIncludeContent($postsSlug)
            );
        }

        return true;
    }

    /**
     * Create or overwrite a template file with optional backup.
     *
     * @param string $path
     * @param string $content
     * @return void
     */
    protected function writeTemplateWithBackup($path, $content) {
        $backup = get_option('byt3lab_auto_backup', '0');
        $dir = dirname($path);
        $this->fileManager->createDirectory($dir);

        if (file_exists($path) && $backup === '1') {
            $backupDir = dirname($path) . '/.byt3lab_backups';
            $this->fileManager->createDirectory($backupDir);
            $time = gmdate('Ymd-His');
            $basename = basename($path);
            $backupPath = $backupDir . '/' . $basename . '.' . $time . '.bak';
            copy($path, $backupPath);
        }

        $this->fileManager->putContents($path, $content);
    }

    /**
     * Build the dynamic page loader content for `page.php`.
     *
     * @return string
     */
    protected function buildPageLoaderContent() {
        $content = "<?php\n";
        $content .= "if (!defined('ABSPATH')) { exit; }\n";
        $content .= "get_header();\n";
        $content .= "global \$post;\n";
        $content .= "\$slug = isset(\$post->post_name) ? \$post->post_name : '';\n";
        $content .= "\$path = get_template_directory() . '/pages/page-' . \$slug . '.php';\n";
        $content .= "if (file_exists(\$path)) { include \$path; } else {\n";
        $content .= "    echo '<main id=\"primary\" class=\"site-main\">';\n";
        $content .= "    if (have_posts()) : while (have_posts()) : the_post(); the_content(); endwhile; endif;\n";
        $content .= "    echo '</main>';\n";
        $content .= "}\n";
        $content .= "get_footer();\n";
        return $content;
    }

    /**
     * Build a template that includes a static page by slug.
     *
     * @param string $slug
     * @return string
     */
    protected function buildStaticPageIncludeContent($slug) {
        $slugEsc = addslashes($slug);
        $content = "<?php\n";
        $content .= "if (!defined('ABSPATH')) { exit; }\n";
        $content .= "get_header();\n";
        $content .= "\$path = get_template_directory() . '/pages/page-{$slugEsc}.php';\n";
        $content .= "if (file_exists(\$path)) { include \$path; } else {\n";
        $content .= "    echo '<main id=\"primary\" class=\"site-main\">';\n";
        $content .= "    if (have_posts()) : while (have_posts()) : the_post(); the_content(); endwhile; endif;\n";
        $content .= "    echo '</main>';\n";
        $content .= "}\n";
        $content .= "get_footer();\n";
        return $content;
    }

    /**
     * Build 404 template content that includes a page by slug.
     *
     * @param string $slug
     * @return string
     */
    protected function build404IncludeContent($slug) {
        $slugEsc = addslashes($slug);
        $content = "<?php\n";
        $content .= "if (!defined('ABSPATH')) { exit; }\n";
        $content .= "status_header(404);\n";
        $content .= "get_header();\n";
        $content .= "\$path = get_template_directory() . '/pages/page-{$slugEsc}.php';\n";
        $content .= "if (file_exists(\$path)) { include \$path; } else {\n";
        $content .= "    echo '<main id=\"primary\" class=\"site-main\">';\n";
        $content .= "    echo '<h1>404 - Page non trouvée</h1>';\n";
        $content .= "    echo '</main>';\n";
        $content .= "}\n";
        $content .= "get_footer();\n";
        return $content;
    }
}
