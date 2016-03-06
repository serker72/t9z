<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php do_action('woocommerce_email_header', $reason); ?>

<p><?php echo sprintf(__( "You order with order number %s now has the following status: %s", $plugin_id ),$order->get_order_number(),'<b>'.__( $reason, $plugin_id).'</b>'); ?></p>

<p><?php echo $message; ?></p>

<?php if ($reason == 'Files rejected'): ?>
    <p><?php _e('Please log into your account and upload your file(s) again.', $plugin_id); ?></p>
    <p><a href="<?php echo $my_order_url; ?>"><?php _e( 'Login to upload your files again.', $plugin_id); ?></a></p>
<?php endif; ?>

<?php echo woocommerce_get_page_id('view_order'); ?>

<?php do_action('woocommerce_email_footer'); ?>