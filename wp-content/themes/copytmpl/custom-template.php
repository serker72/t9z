<?php

/* Template Name: Custom Template */

get_header(); ?>

	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<?php the_content(); ?>
		<?php endwhile; ?>
	<?php else : ?>
		<div class="b-content">
			<?php get_template_part( 'partials/content', 'not-found' ); ?>
		</div>
	<?php endif; ?>

<?php get_footer(); ?>