<?php
/**
 * Thankyou page
 */

if ( $order ) : ?>

	<?php if ( $order->has_status( 'failed' ) ) : ?>

		<p class="woocommerce-thankyou-order-failed"><?php _e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?></p>

		<p class="woocommerce-thankyou-order-failed-actions">
			<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php _e( 'Pay', 'woocommerce' ) ?></a>
			<?php if ( is_user_logged_in() ) : ?>
				<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php _e( 'My Account', 'woocommerce' ); ?></a>
			<?php endif; ?>
		</p>

	<?php else : ?>

		<h1><?php printf( __( 'Your order #%1$s has been received.', 'copytmpl' ), $order->get_order_number() ); ?></h1>

		<div class="print-success cf">
			<div class="print-success-info">
				<p><?php _e( 'Send files to the printing', 'copytmpl' ); ?>.</p>
				<p><?php _e( 'Implementation status of their orders, you can view in your account.', 'copytmpl' ); ?></p>
				<p><a class="button" href="/"><?php _e( 'Go to home', 'copytmpl' ); ?></a></p>
			</div>
			<div class="print-success-thumb"><img height="147" width="220" alt="" src="<?php echo get_template_directory_uri() ?>/i/order-success.jpg"></div>
		</div>

		<?php /*<ul class="woocommerce-thankyou-order-details order_details">
			<li class="order">
				<?php _e( 'Order Number:', 'woocommerce' ); ?>
				<strong><?php echo $order->get_order_number(); ?></strong>
			</li>
			<li class="date">
				<?php _e( 'Date:', 'woocommerce' ); ?>
				<strong><?php echo date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) ); ?></strong>
			</li>
			<li class="total">
				<?php _e( 'Total:', 'woocommerce' ); ?>
				<strong><?php echo $order->get_formatted_order_total(); ?></strong>
			</li>
			<?php if ( $order->payment_method_title ) : ?>
			<li class="method">
				<?php _e( 'Payment Method:', 'woocommerce' ); ?>
				<strong><?php echo $order->payment_method_title; ?></strong>
			</li>
			<?php endif; ?>
		</ul> */ ?>
		<div class="clear"></div>

	<?php endif; ?>

	<?php /*do_action( 'woocommerce_thankyou_' . $order->payment_method, $order->id );*/ ?>
	<?php do_action( 'woocommerce_thankyou', $order->id ); ?>

<?php else : ?>

	<h1><?php printf( __( 'Your order #%1$s has been received.', 'copytpml' ), $order->get_order_number() ); ?></h1>

	<div class="print-success cf">
		<div class="print-success-info">
			<p><?php _e( 'Send files to the printing', 'copytmpl' ); ?>.</p>
			<p><?php _e( 'Implementation status of their orders, you can view in your account.', 'copytmpl' ); ?></p>
			<p><a class="button" href="/"><?php _e( 'Go to home', 'copytmpl' ); ?></a></p>
		</div>
		<div class="print-success-thumb"><img height="147" width="220" alt="" src="<?php echo get_template_directory_uri() ?>/i/order-success.jpg"></div>
	</div>

<?php endif; ?>
