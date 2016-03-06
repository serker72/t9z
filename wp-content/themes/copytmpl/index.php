<?php get_header(); ?>

	<div class="b-content">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<h1><?php the_title(); ?></h1>
				<?php the_content(); ?>
			<?php endwhile; ?>
		<?php else : ?>
			<?php get_template_part( 'partials/content', 'not-found' ); ?>
		<?php endif; ?>
	
		<?php if ( function_exists('wp_paginate') ) : wp_paginate(); endif; ?>
	</div>

<?php get_footer(); ?>