<?php
/**
 * Theme footer.
 *
 * @package YonksTEAM
 */
?>
</main>
<?php
if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) :
	?>
<footer class="site-footer" role="contentinfo">
	<div class="site-footer__inner">
		<div class="site-footer__content">
			<p class="site-footer__tagline"><?php echo esc_html( get_theme_mod( 'yonksteam_footer_tagline', __( 'Building ♾️ WeOwnNet 🌐 ecosystem', 'yonksteam' ) ) ); ?></p>
			<nav class="site-footer__nav" aria-label="<?php esc_attr_e( 'Footer navigation', 'yonksteam' ); ?>">
				<?php
				if ( has_nav_menu( 'footer' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => false,
							'menu_class'     => 'site-footer__menu',
							'fallback_cb'    => false,
							'depth'          => 1,
						)
					);
				} else {
					$pages = array(
						'blog'    => __( 'Blog', 'yonksteam' ),
						'about'   => __( 'About', 'yonksteam' ),
						'contact' => __( 'Contact', 'yonksteam' ),
					);
					foreach ( $pages as $slug => $label ) {
						echo '<a href="' . esc_url( home_url( '/' . $slug ) ) . '" class="site-footer__link">' . esc_html( $label ) . '</a>';
					}
				}
				?>
			</nav>
		</div>
		<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
			<div class="site-footer__widgets">
				<?php dynamic_sidebar( 'footer-1' ); ?>
			</div>
		<?php endif; ?>
		<p class="site-footer__copyright">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'yonksteam' ); ?></p>
	</div>
</footer>
	<?php
endif;
wp_footer();
?>
</body>
</html>
