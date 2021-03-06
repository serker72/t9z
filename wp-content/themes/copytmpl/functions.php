<?php

function copytmpl_setup() {
	load_theme_textdomain( 'copytmpl', get_template_directory() . '/languages' );
	
	add_editor_style();

	register_nav_menu( 'primary', __( 'Navigation Menu', 'copytmpl' ) );

	add_theme_support( 'title-tag' );

	add_filter('widget_text', 'do_shortcode');
	add_filter('the_content', 'do_shortcode');
	add_filter('term_description', 'do_shortcode');

	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 604, 270, true );

	add_filter( 'use_default_gallery_style', '__return_false' );
}
add_action( 'after_setup_theme', 'copytmpl_setup' );


remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'rsd_link' );


// Add support woocommerce
add_action( 'after_setup_theme', 'woocommerce_support' );
function woocommerce_support() {
	add_theme_support( 'woocommerce' );
}


// Replace standard woocommerce.css
add_filter( 'woocommerce_enqueue_styles', 'copytmpl_woocommerce_enqueue_styles', 1);
function copytmpl_woocommerce_enqueue_styles ($styles) {
	$styles['woocommerce-general']['src'] = get_template_directory_uri() .'/woocommerce/assets/css/woocommerce.css';

	return $styles;
}


// Remove woocommerce actions
add_action( 'init', 'copytmpl_wc_actions_init' );
function copytmpl_wc_actions_init() {
	remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
	remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20, 0 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
	remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation', 10 );
}

//
add_action( 'woocommerce_single_product_summary', 'copytmpl_woocommerce_options_div_begin', 6 );
function copytmpl_woocommerce_options_div_begin(){
	echo '<div class="print-options-item">';
}

add_action( 'woocommerce_single_product_summary', 'copytmpl_woocommerce_options_div_end', 51 );
function copytmpl_woocommerce_options_div_end(){
	echo '</div>';
}

add_action( 'woocommerce_single_product_summary', 'copytmpl_woocommerce_single_description', 52 );
function copytmpl_woocommerce_single_description(){
	$post = get_queried_object();

	echo '<div class="print-options-item print-options-item-info">'. apply_filters( 'the_content', $post->post_content ) .'</div>';
}


// Add print steps list
add_action( 'woocommerce_before_main_content', 'copytmpl_woocommerce_before_main_content', 20 );
function copytmpl_woocommerce_before_main_content(){
	$order_steps = copytmpl_order_step_level_tree_array2html();
	
	echo $order_steps .'<div class="b-content">';
}


// Add print steps list
add_action( 'woocommerce_after_main_content', 'copytmpl_woocommerce_after_main_content', 20 );
function copytmpl_woocommerce_after_main_content(){
	echo '</div>';
}

add_action( 'woocommerce_before_shop_loop_item_title', 'copytmpl_woocommerce_shop_loop_item_title_before', 9 );
function copytmpl_woocommerce_shop_loop_item_title_before(){
	echo '<div class="print-type-select-thumb">';
}

add_action( 'woocommerce_before_shop_loop_item_title', 'copytmpl_woocommerce_shop_loop_item_title_after', 11 );
function copytmpl_woocommerce_shop_loop_item_title_after(){
	echo '</div>';
}


// Remove woocommerce sidebar
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10);


// Embeded scripts and styles
function copytmpl_scripts_styles() {
	// Deactivate 'Open Sans' font
	wp_deregister_style( 'open-sans' );
	wp_register_style( 'open-sans', false );

	// Activate google 'Open Sans' with cyrillic glyphs
	wp_enqueue_style( 'copytmpl-open-sans-style', '//fonts.googleapis.com/css?family=Open+Sans:400,300,400italic,600,700,700italic&amp;subset=latin,cyrillic,cyrillic-ext', array(), '20160121' );

	// Colorbox
	wp_register_script( 'copytmpl-colorbox-js', get_template_directory_uri() . '/js/colorbox/jquery.colorbox-min.js', array('jquery'), '1.6.0', true );
	wp_enqueue_script('copytmpl-colorbox-js');
	wp_register_style('copytmpl-colorbox', get_template_directory_uri() . '/js/colorbox/colorbox.css', array(), '20160121');
	wp_enqueue_style('copytmpl-colorbox');

	// Common scripts
	wp_register_script( 'copytmpl-common-js', get_template_directory_uri() . '/js/common.js', array('jquery'), '20160121', true );
	wp_enqueue_script('copytmpl-common-js');

	// Masked Input Plugin scripts
	wp_register_script( 'maskedinput-js', get_template_directory_uri() . '/js/jquery.maskedinput.min.js', array('jquery'), '20160121', true );
	wp_enqueue_script('maskedinput-js');

	// Styles
	wp_enqueue_style( 'copytmpl-style', get_stylesheet_uri(), array(), '20160121' );
}
add_action( 'wp_enqueue_scripts', 'copytmpl_scripts_styles' );


// Add widgets areas
function copytmpl_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Header Widget Area', 'copytmpl' ),
		'id'            => 'header-area',
		'before_widget' => '<div id="%1$s" class="%2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '',
		'after_title'   => '',
	) );

	register_sidebar( array(
		'name'          => __( 'Cart Widget Area', 'copytmpl' ),
		'id'            => 'cart-area',
		'before_widget' => '<div id="%1$s" class="%2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '',
		'after_title'   => '',
	) );

	register_sidebar( array(
		'name'          => __( 'After Content Widget Area', 'copytmpl' ),
		'id'            => 'after-content-area',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '',
		'after_title'   => '',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer Widget Area', 'copytmpl' ),
		'id'            => 'footer-area',
		'before_widget' => '<div id="%1$s" class="%2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '',
		'after_title'   => '',
	) );

	register_sidebar( array(
		'name'          => __( 'Hidden Widget Area', 'copytmpl' ),
		'id'            => 'hidden-area',
		'before_widget' => '<div id="%1$s" class="%2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '',
		'after_title'   => '',
	) );
}
add_action( 'widgets_init', 'copytmpl_widgets_init' );


// Add extra class to body
function copytmpl_add_body_class_slug($classes){
	global $wp_query;

	$slug = $wp_query->post->post_name;
	$classes[] = 'page-custom-'. esc_attr( $slug );

	return $classes;
}
add_filter( 'body_class', 'copytmpl_add_body_class_slug' );


// Order steps level tree array into html list
function copytmpl_order_step_level_tree_array2html(){
	$step = 0;

	if ( is_product() )
		$step = 1;

	$step_list_arr = array(
		__( 'Select the type of printing', 'copytmpl' ),
		__( 'Select options', 'copytmpl' ),
		__( 'A method of obtaining and paying', 'copytmpl' ),
		__( 'Checkout', 'copytmpl' ),
		__( 'Send to print', 'copytmpl' ),
	);

	foreach ($step_list_arr as $step_list_item_key => $step_list_item_value ) {
		$active = ( $step_list_item_key > $step )? '' : ' active';

		$post_order_step_levels_output .= '<div class="print-step'. $active .'"><span class="print-step-inner">'. $step_list_item_value .'</span></div>';
	}

	$output = '<div class="print-steps-wrap"><div class="print-steps">'. $post_order_step_levels_output .'</div></div>';

	echo $output;
}


// Header user navigation
function copytmpl_header_user_nav_shortcode_func( $atts ) {
	extract( shortcode_atts( array(
	), $atts ) );

	$output = '<div class="header-user-nav">';

	if ( is_user_logged_in() ) {
		$output .= '<a href="'. wc_customer_edit_account_url() .'" class="header-user-nav-item">'. __( 'Info', 'copytmpl' ) .'</a> <a href="'. wc_get_page_permalink( 'myaccount' ) .'" class="header-user-nav-item">'. __( 'Orders', 'copytmpl' ) .'</a> <a href="'.wc_get_endpoint_url( 'customer-logout', '', wc_get_page_permalink( 'myaccount' ) ). '" class="header-user-nav-item">'. __( 'Exit', 'copytmpl' ) .'</a>';
	}
	else {
		$output .= '<a href="'. wc_get_page_permalink( 'myaccount' ) .'" class="header-user-nav-item popup-inline-show" data-target-popup="form-auth">'. __( 'Login', 'copytmpl' ) .'</a> ';

		if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) {
			$output .= '<a href="'. wc_get_page_permalink( 'myaccount' ) .'" class="header-user-nav-item popup-inline-show" data-target-popup="form-reg">'. __( 'Sign Up', 'copytmpl' ) .'</a>';
		}

		else {
			$output .= '<a href="'. wc_get_endpoint_url( 'lost-password', '', wc_get_page_permalink( 'myaccount' ) ) .'" class="header-user-nav-item">'. __( 'Lost your password?', 'woocommerce' ) .'</a>';
		}
	}

	$output .= '</div>';

	return $output;
}
add_shortcode( 'header_user_nav', 'copytmpl_header_user_nav_shortcode_func' );


// User navigation sidebar
function copytmpl_user_nav_tree_array2html() {
	$current_url = ( is_ssl()? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

	$output = '';

	$user_list_arr = array(
		'info' => array(
			'text' => __( 'Info', 'copytmpl' ),
			'url' => wc_customer_edit_account_url(),
		),
		'orders' => array(
			'text' => __( 'Orders', 'copytmpl' ),
			'url' => wc_get_page_permalink( 'myaccount' ),
		),
		'exit' => array(
			'text' => __( 'Exit', 'copytmpl' ),
			'url' => wc_get_endpoint_url( 'customer-logout', '', wc_get_page_permalink( 'myaccount' ) ),
		),
	);

	foreach ($user_list_arr as $user_list_arr_key => $user_list_arr_value ) {
		$active = ( $current_url == $user_list_arr_value['url'] )? ' active' : '';

		$output .= '<a class="lk-menu-item lk-menu-item-'. $user_list_arr_key . $active .'" href="'. $user_list_arr_value['url'] .'"><span class="lk-menu-item-inner">'. $user_list_arr_value['text'] .'</span></a>';
	}

	$output = '<div class="lk-menu-wrap"><div class="lk-menu">'. $output .'</div></div>';

	echo $output;
}


// Order steps level tree shortcode
function copytmpl_order_step_level_tree_shortcode_func( $atts ) {
	extract( shortcode_atts( array(
	), $atts ) );

	global $wp_query;

	$post_id = get_the_ID();
	$acf_fields_object = get_field_objects( $post_id );
	$post_order_step_levels = $acf_fields_object['order_step_level'];

	if ( ! empty( $wp_query->query['order-received'] ) )
		$post_order_step_levels['value'] = 5;

	$post_order_step_levels_output = '';

	foreach ($post_order_step_levels['choices'] as $post_order_step_level_key => $post_order_step_level_value ) {
		$active = ( $post_order_step_level_key > $post_order_step_levels['value'] )? '' : ' active';

		$post_order_step_levels_output .= '<div class="print-step'. $active .'"><span class="print-step-inner">'. $post_order_step_level_value .'</span></div>';
	}

	$output = '<div class="print-steps-wrap"><div class="print-steps">'. $post_order_step_levels_output .'</div></div>';

	return $output;
}
add_shortcode( 'order_step_level_tree', 'copytmpl_order_step_level_tree_shortcode_func' );


// WooCommerce login form shortcode
function copytmpl_woocommerce_login_form_shortcode_func($atts){
	extract( shortcode_atts( array(
		'part' => 'login',
	), $atts ) );

	ob_start();

	wc_get_template('myaccount/form-login.php', array( 'part' => $part ) );

	return ob_get_clean();
}
add_shortcode( 'account_login_form', 'copytmpl_woocommerce_login_form_shortcode_func' );


// WooCommerce mini cart shortcode
function copytmpl_woocommerce_mini_cart_shortcode_func(){
	ob_start();

	wc_get_template( 'cart/mini-cart.php' );

	return ob_get_clean();
}
add_shortcode( 'mini_cart', 'copytmpl_woocommerce_mini_cart_shortcode_func' );

// KSK - start
include_once ABSPATH . '/wp-content/themes/copytmpl/ksk_functions.php';
// KSK - end