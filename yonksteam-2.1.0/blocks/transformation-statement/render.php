<?php
/**
 * Transformation Statement Block render template.
 *
 * @package YonksTEAM
 */

$from_text = get_field('from_text');
$to_text   = get_field('to_text');
$body      = get_field('body');

if (! $from_text && ! $to_text && ! $body) {
    return;
}
?>

<section class="transformation-statement alignfull">
    <div class="transformation-statement__inner">
        <div class="transformation-statement__transform">
            <?php if ($from_text) : ?>
                <span class="transformation-statement__from"><?php echo esc_html($from_text); ?></span>
            <?php endif; ?>

            <span class="transformation-statement__arrow">&rarr;</span>

            <?php if ($to_text) : ?>
                <span class="transformation-statement__to"><?php echo esc_html($to_text); ?></span>
            <?php endif; ?>
        </div>

        <?php if ($body) : ?>
            <div class="transformation-statement__body"><?php echo wp_kses_post($body); ?></div>
        <?php endif; ?>
    </div>
</section>