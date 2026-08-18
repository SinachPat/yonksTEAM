<?php
/**
 * Index / Fallback template
 *
 * @package YonksTEAM
 */
get_header(); ?>

<div class="container-wide section-padding">
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