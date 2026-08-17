<?php
/**
 * The main template file (fallback)
 *
 * @package YonksTEAM
 */

get_header(); ?>

<main class="site-main">
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
</main>

<?php get_footer();