		</div>
	</div>
	
	<?php if ( is_active_sidebar( 'after-content-area' ) ) : ?>
		<div class="after-content-area">
			<?php dynamic_sidebar( 'after-content-area' ); ?>
		</div>
	<?php endif; ?>

	<div class="footer cf">
		<div class="wrap cf">
			<?php if ( is_active_sidebar( 'footer-area' ) ) : ?>
				<?php dynamic_sidebar( 'footer-area' ); ?>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php if ( is_active_sidebar( 'hidden-area' ) ) : ?>
	<div class="hidden"><?php dynamic_sidebar( 'hidden-area' ); ?></div>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>