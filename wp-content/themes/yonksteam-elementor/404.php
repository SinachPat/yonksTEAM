<?php
/**
 * 404 template
 *
 * @package YonksTEAM
 */
get_header(); ?>

<div class="container-narrow section-padding" style="text-align:center;">
    <h1><?php _e('Page not found', 'yonksteam'); ?></h1>
    <p><?php _e('The page you\'re looking for doesn\'t exist. Let\'s get you back on track.', 'yonksteam'); ?></p>
    <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn--primary"><?php _e('Go Home', 'yonksteam'); ?></a>
</div>

<?php get_footer();