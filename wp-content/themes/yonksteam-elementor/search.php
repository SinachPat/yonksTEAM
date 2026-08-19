<?php
/**
 * Search results.
 *
 * @package YonksTEAM
 */

get_header();
?>
<div class="container-wide section-padding">
	<h1 class="archive-title">
		<?php
		printf(
			/* translators: %s: search query */
			esc_html__( 'Search: %s', 'yonksteam' ),
			esc_html( get_search_query() )
		);
		?>
	</h1>
	<?php if ( have_posts() ) : ?>
		<div class="blog-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'blog-card' ); ?>>
					<div class="blog-card__body">
						<h2 class="blog-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<div class="blog-card__excerpt"><?php the_excerpt(); ?></div>
					</div>
				</article>
			<?php endwhile; ?>
		</div>
		<div class="pagination"><?php the_posts_pagination(); ?></div>
	<?php else : ?>
		<p><?php esc_html_e( 'Nothing matched your search.', 'yonksteam' ); ?></p>
		<?php get_search_form(); ?>
	<?php endif; ?>
</div>
<?php
get_footer();
