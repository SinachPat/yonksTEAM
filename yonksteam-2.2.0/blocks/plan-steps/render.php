<?php
/**
 * Plan Steps Block render template.
 *
 * @package YonksTEAM
 */

$headline    = get_field('headline');
$subheadline = get_field('subheadline');

$steps = [
    1 => [
        'title' => get_field('step_1_title'),
        'body'  => get_field('step_1_body'),
    ],
    2 => [
        'title' => get_field('step_2_title'),
        'body'  => get_field('step_2_body'),
    ],
    3 => [
        'title' => get_field('step_3_title'),
        'body'  => get_field('step_3_body'),
    ],
];

$has_any = $headline || $steps[1]['title'] || $steps[2]['title'] || $steps[3]['title'];

if (! $has_any) {
    return;
}
?>

<section class="plan-steps alignfull">
    <div class="plan-steps__inner">
        <?php if ($headline) : ?>
            <h2 class="plan-steps__headline"><?php echo esc_html($headline); ?></h2>
        <?php endif; ?>

        <?php if ($subheadline) : ?>
            <p class="plan-steps__subheadline"><?php echo esc_html($subheadline); ?></p>
        <?php endif; ?>

        <div class="plan-steps__list">
            <?php foreach ($steps as $index => $step) : ?>
                <?php if ($step['title'] || $step['body']) : ?>
                    <div class="plan-steps__step plan-steps__step--<?php echo esc_attr($index); ?>">
                        <span class="plan-steps__step-number"><?php echo esc_html($index); ?></span>
                        <?php if ($step['title']) : ?>
                            <h3 class="plan-steps__step-title"><?php echo esc_html($step['title']); ?></h3>
                        <?php endif; ?>
                        <?php if ($step['body']) : ?>
                            <div class="plan-steps__step-body"><?php echo wp_kses_post(wpautop($step['body'])); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>