<?php
/**
 * Archive / blog index.
 *
 * @package YonksTEAM
 */

get_header();

if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'archive' ) ) :
	?>
	<div class="container-wide section-padding">
		<h1 class="archive-title">
			<?php
			if ( is_home() ) {
				single_post_title();
			} else {
				the_archive_title();
			}
			?>
		</h1>

		<?php if ( have_posts() ) : ?>
			<div class="blog-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'blog-card' ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<a class="blog-card__image" href="<?php the_permalink(); ?>">
								<?php the_post_thumbnail( 'yonksteam-card' ); ?>
							</a>
						<?php endif; ?>
						<div class="blog-card__body">
							<h2 class="blog-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<div class="blog-card__excerpt"><?php the_excerpt(); ?></div>
							<div class="blog-card__meta"><?php echo esc_html( get_the_date() ); ?></div>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
			<div class="pagination">
				<?php the_posts_pagination(); ?>
			</div>
		<?php else : ?>
			<p><?php esc_html_e( 'No posts found.', 'yonksteam' ); ?></p>
		<?php endif; ?>
	</div>
	<?php
endif;

get_footer();
