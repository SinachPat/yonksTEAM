<?php
/**
 * CTA Section Block render template.
 *
 * @package YonksTEAM
 */

$headline         = get_field('headline');
$body             = get_field('body');
$cta_primary      = get_field('cta_primary');
$cta_secondary    = get_field('cta_secondary');
$background_color = get_field('background_color') ?: 'default';

if (! $headline && ! $body && ! $cta_primary) {
    return;
}
?>

<section class="cta-section cta-section--bg-<?php echo esc_attr($background_color); ?> alignfull">
    <div class="cta-section__inner">
        <?php if ($headline) : ?>
            <h2 class="cta-section__headline"><?php echo esc_html($headline); ?></h2>
        <?php endif; ?>

        <?php if ($body) : ?>
            <div class="cta-section__body"><?php echo wp_kses_post($body); ?></div>
        <?php endif; ?>

        <?php if ($cta_primary || $cta_secondary) : ?>
            <div class="cta-section__actions">
                <?php if ($cta_primary) : ?>
                    <a class="cta-section__cta cta-section__cta--primary btn btn--primary" href="<?php echo esc_url($cta_primary['url']); ?>" target="<?php echo esc_attr($cta_primary['target'] ?: '_self'); ?>">
                        <?php echo esc_html($cta_primary['title']); ?>
                    </a>
                <?php endif; ?>

                <?php if ($cta_secondary) : ?>
                    <a class="cta-section__cta cta-section__cta--secondary btn btn--secondary" href="<?php echo esc_url($cta_secondary['url']); ?>" target="<?php echo esc_attr($cta_secondary['target'] ?: '_self'); ?>">
                        <?php echo esc_html($cta_secondary['title']); ?>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>