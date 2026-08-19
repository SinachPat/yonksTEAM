<?php
/**
 * Template Name: Elementor Canvas
 * Template Post Type: page, post
 *
 * Blank canvas — no theme header or footer. Use for landing pages
 * or when Elementor Theme Builder supplies the chrome.
 *
 * @package YonksTEAM
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'elementor-template-canvas' ); ?>>
<?php wp_body_open(); ?>
<?php
while ( have_posts() ) :
	the_post();
	the_content();
endwhile;
wp_footer();
?>
</body>
</html>
