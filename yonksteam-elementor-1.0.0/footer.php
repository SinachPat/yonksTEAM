<?php
/**
 * Footer template for YonksTEAM Classic
 *
 * @package YonksTEAM
 */
?>
</main><!-- .site-main -->

<footer class="site-footer" role="contentinfo">
    <div class="site-footer__inner">
        <div class="site-footer__content">
            <p class="site-footer__tagline">Building ♾️ WeOwnNet 🌐 ecosystem</p>
            
            <nav class="site-footer__nav" aria-label="<?php esc_attr_e('Footer navigation', 'yonksteam'); ?>">
                <?php
                if (has_nav_menu('footer')) {
                    wp_nav_menu([
                        'theme_location' => 'footer',
                        'container'      => false,
                        'menu_class'     => 'site-footer__menu',
                        'fallback_cb'    => false,
                        'depth'          => 1,
                    ]);
                } else {
                    $pages = ['blog' => 'Blog', 'about' => 'About', 'contact' => 'Contact'];
                    foreach ($pages as $slug => $label) {
                        echo '<a href="' . esc_url(home_url('/' . $slug)) . '" class="site-footer__link">' . esc_html($label) . '</a>';
                    }
                }
                ?>
            </nav>
        </div>
        
        <p class="site-footer__copyright">&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.</p>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>