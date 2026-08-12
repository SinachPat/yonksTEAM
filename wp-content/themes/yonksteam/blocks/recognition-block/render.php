<?php
/**
 * Recognition Block render template.
 *
 * @package YonksTEAM
 */

$headline = get_field('headline');
$body     = get_field('body');

if (! $headline && ! $body) {
    return;
}
?>

<section class="recognition-block alignfull">
    <div class="recognition-block__inner">
        <?php if ($headline) : ?>
            <h2 class="recognition-block__headline"><?php echo esc_html($headline); ?></h2>
        <?php endif; ?>

        <?php if ($body) : ?>
            <div class="recognition-block__body"><?php echo wp_kses_post($body); ?></div>
        <?php endif; ?>
    </div>
</section>