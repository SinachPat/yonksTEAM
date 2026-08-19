<?php
/**
 * Default / fallback template.
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
	<div class="container-wide section-padding">
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				the_content();
			endwhile;
		endif;
		?>
	</div>
	<?php
endif;

get_footer();
