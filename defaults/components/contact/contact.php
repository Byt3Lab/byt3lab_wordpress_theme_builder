<?php
/**
 * Default Contact component (plugin defaults)
 */
if (!defined('ABSPATH')) {
    exit;
}
?>
<section class="byt3lab-component byt3lab-contact" style="padding: 4rem 2rem; background: #ffffff;">
    <div class="container" style="max-width: 600px; margin: 0 auto;">
        <h2 style="text-align: center; margin-bottom: 2rem; color: #111827;">Contactez-nous</h2>
        <form action="" method="post" style="display: flex; flex-direction: column; gap: 1rem;">
            <div>
                <label style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: #374151;">Nom complet</label>
                <input type="text" name="contact_name" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
            </div>
            <div>
                <label style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: #374151;">Adresse e-mail</label>
                <input type="email" name="contact_email" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
            </div>
            <div>
                <label style="display: block; font-weight: 500; margin-bottom: 0.5rem; color: #374151;">Message</label>
                <textarea name="contact_message" rows="5" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; resize: vertical;"></textarea>
            </div>
            <div style="text-align: center; margin-top: 1rem;">
                <button type="submit" style="background: #2563eb; color: white; padding: 0.75rem 2rem; border: none; border-radius: 0.375rem; font-weight: 600; cursor: pointer; transition: background 0.2s;">Envoyer</button>
            </div>
        </form>
    </div>
</section>
