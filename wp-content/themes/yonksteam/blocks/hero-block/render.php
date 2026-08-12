<?php
/**
 * Hero Block render template.
 *
 * @package YonksTEAM
 */

$headline     = get_field('headline');
$subheadline  = get_field('subheadline');
$body         = get_field('body');
$cta_primary  = get_field('cta_primary');
$cta_secondary = get_field('cta_secondary');
$image        = get_field('image');
$variant      = get_field('variant') ?: 'default';

if (! $headline && ! $body && ! $image) {
    return;
}
?>

<section class="hero-block hero-block--variant-<?php echo esc_attr($variant); ?> alignfull">
    <div class="hero-block__inner">
        <div class="hero-block__content">
            <?php if ($headline) : ?>
                <h1 class="hero-block__headline"><?php echo esc_html($headline); ?></h1>
            <?php endif; ?>

            <?php if ($subheadline) : ?>
                <p class="hero-block__subheadline"><?php echo esc_html($subheadline); ?></p>
            <?php endif; ?>

            <?php if ($body) : ?>
                <div class="hero-block__body"><?php echo wp_kses_post($body); ?></div>
            <?php endif; ?>

            <?php if ($cta_primary || $cta_secondary) : ?>
                <div class="hero-block__actions">
                    <?php if ($cta_primary) : ?>
                        <a class="hero-block__cta hero-block__cta--primary btn btn--primary" href="<?php echo esc_url($cta_primary['url']); ?>" target="<?php echo esc_attr($cta_primary['target'] ?: '_self'); ?>">
                            <?php echo esc_html($cta_primary['title']); ?>
                        </a>
                    <?php endif; ?>

                    <?php if ($cta_secondary) : ?>
                        <a class="hero-block__cta hero-block__cta--secondary btn btn--secondary" href="<?php echo esc_url($cta_secondary['url']); ?>" target="<?php echo esc_attr($cta_secondary['target'] ?: '_self'); ?>">
                            <?php echo esc_html($cta_secondary['title']); ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($image) : ?>
            <div class="hero-block__media">
                <img class="hero-block__image" src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt'] ?: $headline); ?>" width="<?php echo esc_attr($image['width']); ?>" height="<?php echo esc_attr($image['height']); ?>" loading="lazy" />
            </div>
        <?php endif; ?>
    </div>
</section>