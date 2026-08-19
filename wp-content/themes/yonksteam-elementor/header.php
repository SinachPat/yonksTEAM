<?php
/**
 * Theme header.
 *
 * @package YonksTEAM
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="https://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main-content"><?php esc_html_e( 'Skip to content', 'yonksteam' ); ?></a>
<?php
if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) :
	?>
<header class="site-header" id="site-header">
	<div class="site-header__inner">
		<?php if ( has_custom_logo() ) : ?>
			<?php the_custom_logo(); ?>
		<?php else : ?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-header__logo">
				<?php bloginfo( 'name' ); ?>
			</a>
		<?php endif; ?>

		<nav class="site-header__nav" id="primary-nav" aria-label="<?php esc_attr_e( 'Primary navigation', 'yonksteam' ); ?>">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'site-header__menu',
						'fallback_cb'    => false,
						'depth'          => 2,
					)
				);
			} else {
				$pages = array(
					'for-advisors'   => __( 'For Advisors', 'yonksteam' ),
					'exit-to-client' => __( 'Exit to Client', 'yonksteam' ),
					'about'          => __( 'About', 'yonksteam' ),
				);
				foreach ( $pages as $slug => $label ) {
					echo '<a href="' . esc_url( home_url( '/' . $slug ) ) . '" class="site-header__link">' . esc_html( $label ) . '</a>';
				}
			}
			?>
			<a href="<?php echo esc_url( yonksteam_cta_url() ); ?>" class="btn btn--primary btn--sm"><?php echo esc_html( yonksteam_cta_label() ); ?></a>
		</nav>

		<button type="button" class="nav-toggle" aria-expanded="false" aria-controls="primary-nav" aria-label="<?php esc_attr_e( 'Toggle navigation menu', 'yonksteam' ); ?>">
			<span class="nav-toggle__icon"></span>
		</button>
	</div>
</header>
	<?php
endif;
?>
<main id="main-content" class="site-main">
