<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link href="<?php bloginfo( 'template_directory' ); ?>/favicon.ico" rel="shortcut icon" type="image/x-icon" />
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<div class="header">
		<div class="wrap cf">
			<div class="header-logo">
				<a href="/"><span class="header-logo-title"><?php _e( 'Copy Center', 'copytmpl' ); ?></span> <span class="header-logo-descr"><?php _e( 'operational polygraphy', 'copytmpl' ); ?></span></a>
			</div>
			<?php if ( is_active_sidebar( 'header-area' ) ) : ?>
				<?php dynamic_sidebar( 'header-area' ); ?>
			<?php endif; ?>
		</div>
	</div>
	<div class="top-panel">
		<div class="wrap cf">
			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'top-panel-menu', 'container_class' => 'top-panel-menu-wrap', ) ); ?>
			<?php if ( is_active_sidebar( 'cart-area' ) ) : ?>
				<?php dynamic_sidebar( 'cart-area' ); ?>
			<?php endif; ?>
		</div>
	</div>

	<div class="main">
		<div class="wrap cf">
			