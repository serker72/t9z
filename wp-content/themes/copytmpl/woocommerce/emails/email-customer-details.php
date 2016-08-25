<?php
/**
 * Additional Customer Details
 *
 * This is extra customer data which can be filtered by plugins. It outputs below the order item table.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-addresses.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails
 * @version     2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// KSK
$user_id = get_current_user_id();
$user_last_name = get_user_meta($user_id, 'last_name', true);
$user_first_name = get_user_meta($user_id, 'first_name', true);

?>
<h2><?php _e( 'Customer details', 'woocommerce' ); ?></h2>
<ul>
    <!--li><strong>Фамилия и имя:</strong> <span class="text"><?php //echo wp_kses_post( $user_last_name . ' ' . $user_first_name ); ?></span></li-->
    <?php
    ksort($fields);
    foreach ( $fields as $field ) : ?>
        <li><strong><?php echo wp_kses_post( $field['label'] ); ?>:</strong> <span class="text"><?php echo wp_kses_post( $field['value'] ); ?></span></li>
    <?php endforeach; ?>
</ul>

<h2>Информация о заказе</h2>
<p>Посмотреть заказ можно в <a class="link" href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>">Личном кабинете</a></p>
