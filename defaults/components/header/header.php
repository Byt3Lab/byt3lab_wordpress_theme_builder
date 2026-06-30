<?php
/**
 * Default Header component (plugin defaults)
 */
if (!defined('ABSPATH')) {
    exit;
}
?>
<header class="byt3lab-component byt3lab-header">
    <div class="container" style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 2rem;">
        <div class="logo">
            <a href="<?php echo esc_url(home_url('/')); ?>" style="font-weight: bold; font-size: 1.5rem; text-decoration: none; color: #111827;">
                <?php bloginfo('name'); ?>
            </a>
        </div>
        <nav class="navigation">
            <ul style="display: flex; gap: 1.5rem; list-style: none; margin: 0; padding: 0;">
                <li><a href="<?php echo esc_url(home_url('/')); ?>" style="text-decoration: none; color: #4b5563;">Accueil</a></li>
                <li><a href="#" style="text-decoration: none; color: #4b5563;">Services</a></li>
                <li><a href="#" style="text-decoration: none; color: #4b5563;">À propos</a></li>
                <li><a href="#" style="text-decoration: none; color: #4b5563;">Contact</a></li>
            </ul>
        </nav>
    </div>
</header>
