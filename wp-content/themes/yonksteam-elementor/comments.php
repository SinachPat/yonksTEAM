<?php
/**
 * Comments template.
 *
 * @package YonksTEAM
 */

if ( post_password_required() ) {
	return;
}
?>
<div id="comments" class="comments-area container-narrow">
	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
			printf(
				esc_html( _n( '%s comment', '%s comments', get_comments_number(), 'yonksteam' ) ),
				esc_html( number_format_i18n( get_comments_number() ) )
			);
			?>
		</h2>
		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'      => 'ol',
					'short_ping' => true,
				)
			);
			?>
		</ol>
		<?php the_comments_pagination(); ?>
	<?php endif; ?>

	<?php if ( ! comments_open() ) : ?>
		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'yonksteam' ); ?></p>
	<?php endif; ?>

	<?php comment_form(); ?>
</div>
