<?php
/**
 * Single post template
 *
 * @package YonksTEAM
 */
get_header(); ?>

<div class="container-narrow section-padding">
    <?php
    while (have_posts()) :
        the_post(); ?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>
            <h1 class="single-post__title"><?php the_title(); ?></h1>
            <div class="single-post__meta">
                <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
                <?php if (has_category()) : ?>
                    <span class="single-post__categories"><?php the_category(', '); ?></span>
                <?php endif; ?>
            </div>
            <div class="single-post__content">
                <?php the_content(); ?>
            </div>
        </article>
        
        <?php
    endwhile;
    ?>
</div>

<?php get_footer();