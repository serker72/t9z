<?php
// KSK - Start session
if (!session_id()) {
    session_start();
}
?>
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
                        <?php $city = isset($_SESSION['shipping_city']) ? $_SESSION['shipping_city'] : (isset($_POST['shipping_city']) ? $_POST['shipping_city'] : ''); ?>
                        <?php //$city = isset($_SESSION['shipping_city']) ? $_SESSION['shipping_city'] : ''; ?>
                        <div class="header-location-select-wrap">
                            <div class="header-location-select-toggle"><span class="pseudo-link">Выберите Ваш город</span></div>
                            <div class="header-location-select-v"><?php echo $city; ?></div>
                            <ul class="header-location-select-opts">
                                <?php
                                $cities = ksk_get_shipping_cities();
                                foreach ($cities as $key => $value) {
                                    echo '<li '.(($value['city'] == $city) ? 'class="active"' : '').'>'.$value['city'].'</li>';
                                }
                                ?>
                            </ul>
                        </div>
			<?php if ( is_active_sidebar( 'header-area' ) ) : ?>
				<?php //dynamic_sidebar( 'header-area' ); 
                                echo copytmpl_header_user_nav_shortcode_func(null); ?>
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
			