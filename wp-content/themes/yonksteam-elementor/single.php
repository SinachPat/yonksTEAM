<?php
/**
 * Single post.
 *
 * @package YonksTEAM
 */

get_header();

if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'single' ) ) :
	if ( yonksteam_is_elementor_page() ) :
		while ( have_posts() ) :
			the_post();
			the_content();
		endwhile;
	else :
		?>
		<div class="container-narrow section-padding">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-post' ); ?>>
					<h1 class="single-post__title"><?php the_title(); ?></h1>
					<div class="single-post__meta">
						<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
						<?php if ( has_category() ) : ?>
							<span class="single-post__categories"><?php the_category( ', ' ); ?></span>
						<?php endif; ?>
					</div>
					<div class="single-post__content">
						<?php
						the_content();
						wp_link_pages(
							array(
								'before' => '<nav class="page-links">' . esc_html__( 'Pages:', 'yonksteam' ),
								'after'  => '</nav>',
							)
						);
						?>
					</div>
				</article>
				<?php
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
			endwhile;
			?>
		</div>
		<?php
	endif;
endif;

get_footer();
