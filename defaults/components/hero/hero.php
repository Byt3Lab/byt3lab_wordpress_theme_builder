<?php
/**
 * Default Hero component (plugin defaults)
 */
if (!defined('ABSPATH')) {
    exit;
}

$title = 'Welcome to BYT3LAB';
$subtitle = 'This is the default hero component.';
?>
<section class="byt3lab-component byt3lab-hero">
    <div class="container">
        <h1 class="byt3lab-hero__title"><?php echo esc_html($title); ?></h1>
        <p class="byt3lab-hero__subtitle"><?php echo esc_html($subtitle); ?></p>
    </div>
</section>
