<?php
/**
 * Title: Newsletter Sign-up
 * Slug: yonksteam/newsletter-signup
 * Categories: yonksteam
 * Description: Newsletter sign-up form with email input and submit button. Includes social proof text.
 *
 * @package YonksTEAM
 */
?>
<!-- wp:group {"className":"newsletter-signup","align":"full","layout":{"type":"constrained"}} -->
<div class="wp-block-group newsletter-signup alignfull">
    <!-- wp:group {"className":"newsletter-signup__inner","layout":{"type":"constrained"}} -->
    <div class="wp-block-group newsletter-signup__inner">
        <!-- wp:heading {"textAlign":"center","level":3,"className":"newsletter-signup__headline"} -->
        <h3 class="wp-block-heading has-text-align-center newsletter-signup__headline">Get the weekly email for burned-out advisors.</h3>
        <!-- /wp:heading -->

        <!-- wp:paragraph {"align":"center","className":"newsletter-signup__body"} -->
        <p class="has-text-align-center newsletter-signup__body">Join hundreds of advisors who read our honest, no-fluff take on escaping the grind and building a life you actually want.</p>
        <!-- /wp:paragraph -->

        <!-- wp:fluentform/form {"formId":1,"renderer":"classic"} /-->

        <!-- wp:paragraph {"align":"center","fontSize":"small"} -->
        <p class="has-text-align-center has-small-font-size" style="margin-top:1rem;">No spam. Unsubscribe anytime. We mean it.</p>
        <!-- /wp:paragraph -->
    </div>
    <!-- /wp:group -->
</div>
<!-- /wp:group -->