<?php
/**
 * Two Paths Block render template.
 *
 * @package YonksTEAM
 */

$headline = get_field('headline');

$paths = [
    1 => [
        'title' => get_field('path_1_title'),
        'body'  => get_field('path_1_body'),
        'cta'   => get_field('path_1_cta'),
    ],
    2 => [
        'title' => get_field('path_2_title'),
        'body'  => get_field('path_2_body'),
        'cta'   => get_field('path_2_cta'),
    ],
];

$has_any = $headline || $paths[1]['title'] || $paths[2]['title'];

if (! $has_any) {
    return;
}
?>

<section class="two-paths alignfull">
    <div class="two-paths__inner">
        <?php if ($headline) : ?>
            <h2 class="two-paths__headline"><?php echo esc_html($headline); ?></h2>
        <?php endif; ?>

        <div class="two-paths__columns">
            <?php foreach ($paths as $index => $path) : ?>
                <?php if ($path['title'] || $path['body']) : ?>
                    <div class="two-paths__path two-paths__path--<?php echo esc_attr($index); ?>">
                        <?php if ($path['title']) : ?>
                            <h3 class="two-paths__path-title"><?php echo esc_html($path['title']); ?></h3>
                        <?php endif; ?>

                        <?php if ($path['body']) : ?>
                            <div class="two-paths__path-body"><?php echo wp_kses_post(wpautop($path['body'])); ?></div>
                        <?php endif; ?>

                        <?php if ($path['cta']) : ?>
                            <a class="two-paths__path-cta btn btn--primary" href="<?php echo esc_url($path['cta']['url']); ?>" target="<?php echo esc_attr($path['cta']['target'] ?: '_self'); ?>">
                                <?php echo esc_html($path['cta']['title']); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>