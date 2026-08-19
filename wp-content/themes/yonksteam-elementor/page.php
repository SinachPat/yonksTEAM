<?php
/**
 * Default page template.
 *
 * @package YonksTEAM
 */

get_header();

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
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<h1 class="single-post__title"><?php the_title(); ?></h1>
				<div class="single-post__content">
					<?php the_content(); ?>
				</div>
			</article>
			<?php
		endwhile;
		?>
	</div>
	<?php
endif;

get_footer();
