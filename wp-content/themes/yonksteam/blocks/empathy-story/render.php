<?php
/**
 * Empathy Story Block render template.
 *
 * @package YonksTEAM
 */

$headline   = get_field('headline');
$story_1_title = get_field('story_1_title');
$story_1_body  = get_field('story_1_body');
$story_1_image = get_field('story_1_image');
$story_2_title = get_field('story_2_title');
$story_2_body  = get_field('story_2_body');
$story_2_image = get_field('story_2_image');

if (! $headline && ! $story_1_title && ! $story_2_title) {
    return;
}
?>

<section class="empathy-story alignfull">
    <div class="empathy-story__inner">
        <?php if ($headline) : ?>
            <h2 class="empathy-story__headline"><?php echo esc_html($headline); ?></h2>
        <?php endif; ?>

        <div class="empathy-story__stories">
            <div class="empathy-story__story empathy-story__story--1">
                <?php if ($story_1_image) : ?>
                    <div class="empathy-story__image-wrap">
                        <img class="empathy-story__image" src="<?php echo esc_url($story_1_image['url']); ?>" alt="<?php echo esc_attr($story_1_image['alt'] ?: $story_1_title); ?>" width="<?php echo esc_attr($story_1_image['width']); ?>" height="<?php echo esc_attr($story_1_image['height']); ?>" loading="lazy" />
                    </div>
                <?php endif; ?>

                <?php if ($story_1_title) : ?>
                    <h3 class="empathy-story__story-title"><?php echo esc_html($story_1_title); ?></h3>
                <?php endif; ?>

                <?php if ($story_1_body) : ?>
                    <div class="empathy-story__story-body"><?php echo wp_kses_post(wpautop($story_1_body)); ?></div>
                <?php endif; ?>
            </div>

            <div class="empathy-story__story empathy-story__story--2">
                <?php if ($story_2_image) : ?>
                    <div class="empathy-story__image-wrap">
                        <img class="empathy-story__image" src="<?php echo esc_url($story_2_image['url']); ?>" alt="<?php echo esc_attr($story_2_image['alt'] ?: $story_2_title); ?>" width="<?php echo esc_attr($story_2_image['width']); ?>" height="<?php echo esc_attr($story_2_image['height']); ?>" loading="lazy" />
                    </div>
                <?php endif; ?>

                <?php if ($story_2_title) : ?>
                    <h3 class="empathy-story__story-title"><?php echo esc_html($story_2_title); ?></h3>
                <?php endif; ?>

                <?php if ($story_2_body) : ?>
                    <div class="empathy-story__story-body"><?php echo wp_kses_post(wpautop($story_2_body)); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>