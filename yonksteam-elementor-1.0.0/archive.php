<?php
/**
 * Archive / Blog template
 *
 * @package YonksTEAM
 */
get_header(); ?>

<div class="container-wide section-padding">
    <h1 class="archive-title">
        <?php
        if (is_home()) {
            single_post_title();
        } elseif (is_category()) {
            single_cat_title();
        } elseif (is_tag()) {
            single_tag_title();
        } elseif (is_author()) {
            the_archive_title();
        } else {
            the_archive_title();
        }
        ?>
    </h1>
    
    <?php if (have_posts()) : ?>
        <div class="blog-grid">
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('blog-card'); ?>>
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="blog-card__image">
                            <?php the_post_thumbnail('medium'); ?>
                        </div>
                    <?php endif; ?>
                    <div class="blog-card__body">
                        <h2 class="blog-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <div class="blog-card__excerpt"><?php the_excerpt(); ?></div>
                        <div class="blog-card__meta"><?php echo get_the_date(); ?></div>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
        
        <div class="pagination">
            <?php posts_nav_link(); ?>
        </div>
    <?php else : ?>
        <p><?php _e('No posts found.', 'yonksteam'); ?></p>
    <?php endif; ?>
</div>

<?php get_footer();