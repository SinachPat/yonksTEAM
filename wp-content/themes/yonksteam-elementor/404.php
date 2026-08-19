<?php
/**
 * 404 template.
 *
 * @package YonksTEAM
 */

get_header();
?>
<div class="container-narrow section-padding error-404">
	<h1><?php esc_html_e( 'Page not found', 'yonksteam' ); ?></h1>
	<p><?php esc_html_e( "The page you're looking for doesn't exist. Let's get you back on track.", 'yonksteam' ); ?></p>
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn--primary"><?php esc_html_e( 'Go Home', 'yonksteam' ); ?></a>
</div>
<?php
get_footer();
