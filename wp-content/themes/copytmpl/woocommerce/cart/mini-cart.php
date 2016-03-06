<?php
/**
 * Mini-cart
 */

?>

<?php do_action( 'woocommerce_before_mini_cart' ); ?>

	<?php if ( ! WC()->cart->is_empty() ) : ?>

		<?php
			$count_cart = count( WC()->cart->get_cart() );

			$count_cart_text = sprintf( _nx( '%1$s item in cart', '%1$s items in cart', $count_cart, 'views', 'copytmpl' ), $count_cart );

			$cart_total_text = __( 'subtotal', 'copytmpl' ) .' '. WC()->cart->get_cart_subtotal();

		?>

		<div class="top-panel-orders"><a href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php echo $count_cart_text .' '. $cart_total_text; ?></a></div>

	<?php else : ?>

		<div class="top-panel-orders"><?php _e( 'No products in the cart', 'copytmpl' ); ?></div>

	<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
