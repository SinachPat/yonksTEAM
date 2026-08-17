<?php
/**
 * Testimonial Block render template.
 *
 * @package YonksTEAM
 */

$quote = get_field('quote');
$name  = get_field('name');
$title = get_field('title');
$photo = get_field('photo');
$link  = get_field('link');

if (! $quote && ! $name) {
    return;
}
?>

<section class="testimonial-block alignfull">
    <div class="testimonial-block__inner">
        <blockquote class="testimonial-block__quote">
            <?php if ($quote) : ?>
                <div class="testimonial-block__quote-text"><?php echo wp_kses_post(wpautop($quote)); ?></div>
            <?php endif; ?>

            <footer class="testimonial-block__attribution">
                <?php if ($photo) : ?>
                    <img class="testimonial-block__photo" src="<?php echo esc_url($photo['url']); ?>" alt="<?php echo esc_attr($photo['alt'] ?: $name); ?>" width="<?php echo esc_attr($photo['width']); ?>" height="<?php echo esc_attr($photo['height']); ?>" loading="lazy" />
                <?php endif; ?>

                <div class="testimonial-block__attribution-text">
                    <?php if ($name) : ?>
                        <cite class="testimonial-block__name"><?php echo esc_html($name); ?></cite>
                    <?php endif; ?>

                    <?php if ($title) : ?>
                        <span class="testimonial-block__title"><?php echo esc_html($title); ?></span>
                    <?php endif; ?>

                    <?php if ($link) : ?>
                        <a class="testimonial-block__link" href="<?php echo esc_url($link['url']); ?>" target="<?php echo esc_attr($link['target'] ?: '_self'); ?>">
                            <?php echo esc_html($link['title']); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </footer>
        </blockquote>
    </div>
</section>