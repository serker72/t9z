<?php
/**
 * Order details table shown in emails.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-order-details.php.
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

//do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text, $email ); ?>

<?php if ( ! $sent_to_admin ) : ?>
	<h2><?php printf( __( 'Order #%s', 'woocommerce' ), $order->get_order_number() ); ?></h2>
<?php else : ?>
	<h2><a class="link" href="<?php echo esc_url( admin_url( 'post.php?post=' . $order->id . '&action=edit' ) ); ?>"><?php printf( __( 'Order #%s', 'woocommerce'), $order->get_order_number() ); ?></a> (<?php printf( '<time datetime="%s">%s</time>', date_i18n( 'c', strtotime( $order->order_date ) ), date_i18n( wc_date_format(), strtotime( $order->order_date ) ) ); ?>)</h2>
<?php endif; ?>

<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
	<thead>
		<tr>
			<th class="td" scope="col" style="text-align:left;"><?php _e( 'Product', 'woocommerce' ); ?></th>
			<th class="td" scope="col" style="text-align:left;"><?php _e( 'Quantity', 'woocommerce' ); ?></th>
			<th class="td" scope="col" style="text-align:left;"><?php _e( 'Price', 'woocommerce' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php echo $order->email_order_items_table( array(
			'show_sku'      => $sent_to_admin,
			'show_image'    => false,
			'image_size'    => array( 32, 32 ),
			'plain_text'    => $plain_text,
			'sent_to_admin' => $sent_to_admin
		) ); ?>
	</tbody>
	<tfoot>
		<?php
			if ( $totals = $order->get_order_item_totals() ) {
				$i = 0;
				foreach ( $totals as $total ) {
					$i++;
                                        
					?><tr>
						<th class="td" scope="row" colspan="2" style="text-align:left; <?php if ( $i === 1 ) echo 'border-top-width: 4px;'; ?>"><?php echo $total['label']; ?></th>
						<td class="td" style="text-align:left; <?php if ( $i === 1 ) echo 'border-top-width: 4px;'; ?>"><?php echo $total['value']; ?></td>
					</tr><?php
                                        
                                        // KSK
                                        if ($total['label'] == 'Подитог:') {
                                            $m_t9z_shipping_1 = get_post_meta($order->id, 'ksk_wc_order_t9z_shipping_1', true);
                                            $m_t9z_shipping_2 = get_post_meta($order->id, 'ksk_wc_order_t9z_shipping_2', true);
                                            $m_shipping_city = get_post_meta($order->id, 'ksk_wc_order_shipping_city', true);
                                            $m_shipping_office = get_post_meta($order->id, 'ksk_wc_order_shipping-office', true);
                                            $m_shipping_amount = get_post_meta($order->id, 'ksk_wc_order_shipping-amount', true);
                                            $m_shipping_address = get_post_meta($order->id, 'ksk_wc_order_shipping-address', true);
                                            
                                            if ($m_t9z_shipping_1 == 'city') {
                                                $m_shipping_text1 = 'Доставка по г.' . $m_shipping_city;
                                            } else {
                                                $m_shipping_text1 = 'Получение в офисе';
                                            }
					?><tr>
						<th class="td" scope="row" colspan="2" style="text-align:left; <?php if ( $i === 1 ) echo 'border-top-width: 4px;'; ?>">Вид доставки:</th>
						<td class="td" style="text-align:left; <?php if ( $i === 1 ) echo 'border-top-width: 4px;'; ?>"><?php echo $m_shipping_text1; ?></td>
					</tr><?php
                                        
                                            if ($m_t9z_shipping_1 == 'city') {
					?><tr>
						<th class="td" scope="row" colspan="2" style="text-align:left; <?php if ( $i === 1 ) echo 'border-top-width: 4px;'; ?>">Адрес доставки:</th>
						<td class="td" style="text-align:left; <?php if ( $i === 1 ) echo 'border-top-width: 4px;'; ?>"><?php echo $m_shipping_address; ?></td>
					</tr>
                                            <?php } else {
                                                $m_shipping_office_ex = explode(':', $m_shipping_office);
					?><tr>
						<th class="td" scope="row" colspan="2" style="text-align:left; <?php if ( $i === 1 ) echo 'border-top-width: 4px;'; ?>"><?php echo $m_shipping_office_ex[0]; ?>:</th>
						<td class="td" style="text-align:left; <?php if ( $i === 1 ) echo 'border-top-width: 4px;'; ?>"><?php echo $m_shipping_office_ex[1]; ?></td>
					</tr>
					<tr>
						<th class="td" scope="row" colspan="2" style="text-align:left; <?php if ( $i === 1 ) echo 'border-top-width: 4px;'; ?>">Стоимость доставки:</th>
						<td class="td" style="text-align:left; <?php if ( $i === 1 ) echo 'border-top-width: 4px;'; ?>"><?php echo $m_shipping_amount; ?> руб.</td>
					</tr><?php
                                            }
                                        }
				}
			}
		?>
	</tfoot>
</table>

<?php //do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text, $email ); ?>
