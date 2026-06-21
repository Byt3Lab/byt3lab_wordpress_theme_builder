<?php
if (!defined('ABSPATH')) { exit; }

$items = [ 'Développement web', 'Design', 'Maintenance' ];
?>
<section class="byt3lab-component byt3lab-services">
    <div class="container">
        <h2>Nos services</h2>
        <ul>
            <?php foreach ($items as $it): ?>
                <li><?php echo esc_html($it); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
