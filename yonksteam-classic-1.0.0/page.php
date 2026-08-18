<?php
/**
 * Template Name: Full Width (Elementor)
 *
 * @package YonksTEAM
 */
get_header(); ?>

<div class="elementor-content">
    <?php
    while (have_posts()) :
        the_post();
        the_content();
    endwhile;
    ?>
</div>

<?php get_footer();