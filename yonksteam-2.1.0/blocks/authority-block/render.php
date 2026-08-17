<?php
/**
 * Authority Block render template.
 *
 * @package YonksTEAM
 */

$headline = get_field('headline');

$credentials = [
    1 => [
        'title' => get_field('credential_1_title'),
        'body'  => get_field('credential_1_body'),
    ],
    2 => [
        'title' => get_field('credential_2_title'),
        'body'  => get_field('credential_2_body'),
    ],
    3 => [
        'title' => get_field('credential_3_title'),
        'body'  => get_field('credential_3_body'),
    ],
];

$has_any = $headline || $credentials[1]['title'] || $credentials[2]['title'] || $credentials[3]['title'];

if (! $has_any) {
    return;
}
?>

<section class="authority-block alignfull">
    <div class="authority-block__inner">
        <?php if ($headline) : ?>
            <h2 class="authority-block__headline"><?php echo esc_html($headline); ?></h2>
        <?php endif; ?>

        <div class="authority-block__grid">
            <?php foreach ($credentials as $index => $credential) : ?>
                <?php if ($credential['title'] || $credential['body']) : ?>
                    <div class="authority-block__credential authority-block__credential--<?php echo esc_attr($index); ?>">
                        <?php if ($credential['title']) : ?>
                            <h3 class="authority-block__credential-title"><?php echo esc_html($credential['title']); ?></h3>
                        <?php endif; ?>

                        <?php if ($credential['body']) : ?>
                            <div class="authority-block__credential-body"><?php echo wp_kses_post(wpautop($credential['body'])); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>