<?php
/**
 * Front page template
 *
 * @package YonksTEAM
 */
get_header(); ?>

<div class="container-wide">
    <?php
    if (have_posts()) :
        while (have_posts()) :
            the_post();
            the_content();
        endwhile;
    endif;
    ?>
</div>

<?php get_footer();