<?php
/**
 * Template Name: Elementor Full Width
 * Template Post Type: page, post
 *
 * Header + footer, no content container. Use this (or Elementor's
 * "Full Width" page layout) for Elementor-built pages.
 *
 * @package YonksTEAM
 */

get_header();

while ( have_posts() ) :
	the_post();
	the_content();
endwhile;

get_footer();
