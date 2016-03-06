<?php
	/*
		Template Name: Order steps
	*/
?>

<?php get_header(); ?>

	<?php echo copytmpl_order_step_level_tree_shortcode_func($atts); ?>

	<div class="b-content">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<?php the_content(); ?>
			<?php endwhile; ?>
		<?php else : ?>
			<?php get_template_part( 'partials/content', 'not-found' ); ?>
		<?php endif; ?>
	
		<?php if ( function_exists('wp_paginate') ) : wp_paginate(); endif; ?>
	</div>

<?php get_footer(); ?>