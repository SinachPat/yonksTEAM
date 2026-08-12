<?php
/**
 * Success / Failure Split Block render template.
 *
 * @package YonksTEAM
 */

$headline      = get_field('headline');
$success_items = get_field('success_items');
$failure_items = get_field('failure_items');

if (! $headline && empty($success_items) && empty($failure_items)) {
    return;
}
?>

<section class="success-failure-split alignfull">
    <div class="success-failure-split__inner">
        <?php if ($headline) : ?>
            <h2 class="success-failure-split__headline"><?php echo esc_html($headline); ?></h2>
        <?php endif; ?>

        <div class="success-failure-split__columns">
            <div class="success-failure-split__column success-failure-split__column--success">
                <?php if ($success_items) : ?>
                    <ul class="success-failure-split__list success-failure-split__list--success">
                        <?php foreach ($success_items as $item) : ?>
                            <li class="success-failure-split__list-item"><?php echo esc_html($item['text']); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="success-failure-split__column success-failure-split__column--failure">
                <?php if ($failure_items) : ?>
                    <ul class="success-failure-split__list success-failure-split__list--failure">
                        <?php foreach ($failure_items as $item) : ?>
                            <li class="success-failure-split__list-item"><?php echo esc_html($item['text']); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>