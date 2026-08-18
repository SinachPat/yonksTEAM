<?php
/**
 * Header template for YonksTEAM Classic
 *
 * @package YonksTEAM
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header" id="site-header">
    <div class="site-header__inner">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="site-header__logo" aria-label="<?php bloginfo('name'); ?> — Home">
            <?php if (has_custom_logo()) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <?php bloginfo('name'); ?>
            <?php endif; ?>
        </a>

        <nav class="site-header__nav" id="primary-nav" aria-label="<?php esc_attr_e('Primary navigation', 'yonksteam'); ?>">
            <?php
            if (has_nav_menu('primary')) {
                wp_nav_menu([
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => 'site-header__menu',
                    'fallback_cb'    => false,
                    'depth'          => 1,
                ]);
            } else {
                // Fallback nav when no menu is assigned
                $pages = ['for-advisors' => 'For Advisors', 'exit-to-client' => 'Exit to Client', 'about' => 'About'];
                foreach ($pages as $slug => $label) {
                    echo '<a href="' . esc_url(home_url('/' . $slug)) . '" class="site-header__link">' . esc_html($label) . '</a>';
                }
            }
            ?>
            <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn--primary btn--sm">Start the Conversation</a>
        </nav>

        <button type="button" class="nav-toggle" aria-expanded="false" aria-controls="primary-nav" aria-label="<?php esc_attr_e('Toggle navigation menu', 'yonksteam'); ?>">
            <span class="nav-toggle__icon"></span>
        </button>
    </div>
</header>

<main id="main-content" class="site-main">