<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wc_print_notices();

$file_ext_work = array('pdf', 'docx', 'txt');

//do_action('woocommerce_cart_calculate_fees');
//do_action('woocommerce_cart_total');
//WC()->cart->calculate_totals();

//do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout
if ( ! $checkout->enable_signup && ! $checkout->enable_guest_checkout && ! is_user_logged_in() ) {
	echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) );
	return;
}

?>

<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

	<?php if ( sizeof( $checkout->checkout_fields ) > 0 ) : ?>

		<?php //do_action( 'woocommerce_checkout_before_customer_details' ); ?>

		<!--div class="col2-set" id="customer_details">
			<div class="col-1">
				<?php //do_action( 'woocommerce_checkout_billing' ); ?>
			</div>

			<div class="col-2">
				<?php //do_action( 'woocommerce_checkout_shipping' ); ?>
			</div>
		</div-->

<h1>Оформление заказа</h1>
<div class="print-checkout">
                <div class="print-checkout-item">
                        <h3 class="print-checkout-item-title">Проверьте выбранные товары и опции по доставке и оплате</h3>
                        <table>
		<?php
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				?>
				<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
					<td class="product-name" data-title="<?php _e( 'Product', 'woocommerce' ); ?>">
                                            <?php
                                            if ( ! $_product->is_visible() ) {
                                                    echo apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key ) . '&nbsp;';
                                            } else {
                                                    echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $_product->get_permalink( $cart_item ) ), $_product->get_title() ), $cart_item, $cart_item_key );
                                            }

                                            // Meta data
                                            echo WC()->cart->get_item_data( $cart_item );

                                            // KSK - вывод загруженных файлов
                                            $cart_data = $cart_item['data'];
                                            $url_args['show'] = 'uploads';
                                            if (!empty($cart_item['variation_id'])) {
                                              $url_args['vpid'] = $cart_item['variation_id'];
                                            }
                                            $url_args['ck'] = $cart_item_key;
                                            
                                            if (!empty($cart_item['variation_id'])) {
                                                $need_uploads = WPF_Uploads::product_needs_upload($cart_item['variation_id'], true);
                                            } else {
                                                $need_uploads = WPF_Uploads::product_needs_upload($cart_data->post->ID);
                                            }

                                            if ($need_uploads) {
                                                ?>
                                                <div class="wpf-umf-cart-uploaded-files-label"><?php echo __('Uploaded files:', 'woocommerce-uploads-before'); ?></div>
                                                <table class="ksk_cart_file" cellspacing="0">
                                                    <thead>
                                                        <tr>
                                                            <th>Имя файла</th>
                                                            <th width="20%"<?php echo ($cart_data->post->post_title == "Фотографии") ? ' style="display: none;"' : ''; ?> >Кол-во страниц</th>
                                                            <th width="30%">Кол-во копий</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                <?php
                                                
                                                $return = '';
                                                $current_uploads = WPF_Uploads_Before::get_cart_item_uploads($cart_item, $cart_item_key);

                                                if (is_array($current_uploads) && count($current_uploads)) {
                                                    foreach ($current_uploads AS $key => $value) {
                                                        $value = apply_filters('wpf_umf_cart_uploaded_file', array(
                                                            'name' => $value['name'],
                                                            'pages' => $value['pages'],
                                                            'copies' => $value['copies'],
                                                        ), $value);

                                                        $file_ext = explode(".", $value['name']);
                                                        $file_ext = $file_ext[1];

                                                        $return .= '<tr>';
                                                        $return .= '<td>'.$value['name'].'</td>';
                                                        //$return .= '<td><input type="text" id="pages_'.(!empty($cart_item['variation_id']) ? $cart_item['variation_id'] : $cart_item['product_id']).'_'.$key.'" name="pages_'.(!empty($cart_item['variation_id']) ? $cart_item['variation_id'] : $cart_item['product_id']).'_'.$key.'" value="'.esc_attr($value['pages']).'" title="" class="" size="4" readonly="true"></td>';
                                                        $return .= '<td'.(($cart_data->post->post_title == "Фотографии") ? ' style="display: none;"' : '').'><div class="print-options-photo-upload-image-item-num">';
                                                        $return .= '<span class="">';
                                                        $return .= '<input type="text" id="pages_'.(!empty($cart_item['variation_id']) ? $cart_item['variation_id'] : $cart_item['product_id']).'_'.$key.'" name="pages_'.(!empty($cart_item['variation_id']) ? $cart_item['variation_id'] : $cart_item['product_id']).'_'.$key.'" value="'.esc_attr($value['pages']).'" title="" class="" size="4" readonly="readonly">';
                                                        $return .= '</span></div></td>';
                                                        $return .= '<td><div class="print-options-photo-upload-image-item-num">';
                                                        $return .= '<span class="">';
                                                        $return .= '<input type="text" id="copies_'.(!empty($cart_item['variation_id']) ? $cart_item['variation_id'] : $cart_item['product_id']).'_'.$key.'" name="copies_'.(!empty($cart_item['variation_id']) ? $cart_item['variation_id'] : $cart_item['product_id']).'_'.$key.'" value="'.esc_attr($value['copies']).'" title="" class="" size="4" readonly="readonly">';
                                                        $return .= '</span></div></td>';
                                                        $return .= '</tr>';
                                                    }
                                                }
                                            
                                                //$return .= '<div style="clear: both;margin-bottom: 10px;"></div>';
                                                $return .= '<tr><td colspan="3">';
                                                $return .= '<div class="wpf-umf-cart-upload-button-container"><a href="'.add_query_arg($url_args, get_post_permalink($cart_data->post->ID)).'" class="wpf-umf-cart-upload-button button">'.__('Upload / View files', 'woocommerce-uploads-before').'</a></div>';
                                                $return .= '</td></tr>';
                                                $return .= '<div style="clear: both;margin-bottom: 10px;"></div>';

                                                $return .= '</tbody></table>';

                                                echo $return;
                                            }
                                            // KSK ==========================

                                            // Backorder notification
                                            if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
                                                    echo '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>';
                                            }
                                            ?>
					</td>

					<td class="product-price" data-title="<?php _e( 'Price', 'woocommerce' ); ?>">
						<?php
						echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
                                                //echo wc_price( $cart_data->price );
                                                //echo $cart_data->price;
						?>
					</td>

					<td class="product-quantity" data-title="<?php _e( 'Quantity', 'woocommerce' ); ?>">
						<?php
							/*if ( $_product->is_sold_individually() ) {
								$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
							} else {
								$product_quantity = woocommerce_quantity_input( array(
									'input_name'  => "cart[{$cart_item_key}][qty]",
									'input_value' => $cart_item['quantity'],
									'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
									'min_value'   => '0'
								), $_product, false );
							}

							echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item );*/
                                                        //echo '<div style="text-align: center;">'.$cart_item['quantity'].'</div>';
                                                        echo '<input type="text" id="copies_'.(!empty($cart_item['variation_id']) ? $cart_item['variation_id'] : $cart_item['product_id']).'" name="cart['.$cart_item_key.'][qty]" value="'.esc_attr($cart_item['quantity']).'" title="" class="qty" size="4" readonly="true">';
                                                        // cart[e8e81f480b531104a8061f4830f4bcf5][qty]
                                                        // cart[e5d7437f14963440287972fa09e63d36][qty]
						?>
					</td>

					<td class="product-subtotal" data-title="<?php _e( 'Total', 'woocommerce' ); ?>">
						<?php
							echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
						?>
					</td>
                            </tr>
				<?php
			}
		}

		//do_action( 'woocommerce_cart_contents' );
		?>
                        </table>
                </div>
                <div class="print-checkout-item">
                        <h3 class="print-checkout-item-title">Способ получения:</h3>
                        <?php echo isset($_POST['shipping-text-1']) ? '<p>'.$_POST['shipping-text-1'].'</p>' : ''; ?>
                        <?php echo isset($_POST['shipping-text-2']) ? '<p>'.$_POST['shipping-text-2'].'</p>' : ''; ?>
                        <?php echo isset($_POST['t9z_shipping_1_address']) && ($_POST['t9z_shipping_1_address'] != '') ? '<p>Адрес доставки: <strong>'.$_POST['t9z_shipping_1_address'].'</strong></p>' : ''; ?>
                        <?php if (ksk_check_var_in_session_post_get('natsenka-30', 'on')) { ?>
                            <p>Наценка за срочное выполнение - <strong><?php echo ksk_get_var_from_session_post_get('natsenka-amount', '0').' руб.'; ?></strong></p>
                        <?php } ?>
                        <?php if (ksk_check_var_in_session_post_get('user-bonus', 'on')) { ?>
                            <p>Сумма бонусов, использованных для оплаты - <strong><?php echo ksk_get_var_from_session_post_get('user-bonus-amount', '0').' руб.'; ?></strong></p>
                        <?php } ?>
                </div>
                <div class="print-checkout-item">
                        <h3 class="print-checkout-item-title">Способ оплаты:</h3>
                        <p><b><?php echo (isset($_POST['pay-method']) && ($_POST['pay-method'] == 2)) ? 'Банковской картой, электронные кошельки Яндекс-Деньги, Webmoney и пр.' : ((isset($_POST['pay-method']) && ($_POST['pay-method'] == 1)) ? 'Наличные при получении' : ''); ?></b></p>
                </div>
                <div class="print-checkout-item">
                    <h3>Комментарии:</h3>
                    <p><strong><?php echo ksk_get_var_from_session_post_get('comments', ''); ?></strong></p>
                </div>
                <div class="print-checkout-item">
                        <h3 class="print-checkout-item-title">Стоимость заказа с учётом доставки<?php echo ksk_check_var_in_session_post_get('natsenka-30', 'on') ? ' и наценки за срочность' : ''; ?>:</h3>
                        <div class="print-checkout-sum"><?php echo isset($_POST['total-amount']) ? $_POST['total-amount'] : ''; ?> руб.</div>
                        <div class="print-checkout-sum-bonus"><span class="print-checkout-sum-bonus-label">За этот заказ Вам будет начислено:</span> <?php echo isset($_POST['bonus-amount']) ? $_POST['bonus-amount'] : ''; ?> бонусов</div>
                </div>
                <div class="print-checkout-form">
                        <div class="form-item">
                                <label class="form-label" for="billing_first_name">Ваше имя <abbr class="required" title="обязательно">*</abbr></label>
                                <input type="text" class="input-text" name="billing_first_name" id="billing_first_name" placeholder=""  value="<?php echo $checkout->get_value('billing_first_name'); ?>">
                        </div>
                        <div class="form-item">
                                <label class="form-label" for="billing_last_name">Ваша фамилия <abbr class="required" title="обязательно">*</abbr></label>
                                <input type="text" class="input-text" name="billing_last_name" id="billing_last_name" placeholder=""  value="<?php echo $checkout->get_value('billing_last_name'); ?>">
                        </div>
                        <div class="form-item">
                                <label class="form-label" for="billing_phone">Телефон для связи <abbr class="required" title="обязательно">*</abbr></label>
                                <input type="tel" class="input-text" name="billing_phone" id="billing_phone" placeholder=""  value="<?php echo $checkout->get_value('billing_phone'); ?>">
                        </div>
                        <div class="form-item">
                                <label class="form-label" for="billing_email">E-mail <abbr class="required" title="обязательно">*</abbr></label>
                                <input type="email" class="input-text" name="billing_email" id="billing_email" placeholder=""  value="<?php echo $checkout->get_value('billing_email'); ?>">
                        </div>
                        <div id="ksk-checkout-submit" class="form-item form-item-submit">
                                <!--input type="submit" value="Подтвердить заказ"-->
                        </div>
                </div>
    <input type="hidden" id="pay-method" name="pay-method" value="<?php echo ksk_get_var_from_session_post_get('pay-method', ''); ?>">
</div>
                
		<?php //do_action( 'woocommerce_checkout_after_customer_details' ); ?>

	<?php endif; ?>

	<h3 id="order_review_heading"><?php //_e( 'Your order', 'woocommerce' ); ?></h3>

	<?php //do_action( 'woocommerce_checkout_before_order_review' ); ?>

        <div id="order_review" class="woocommerce-checkout-review-order"><!-- style="display: none;"-->
		<?php do_action( 'woocommerce_checkout_order_review' ); ?>
	</div>

	<?php //do_action( 'woocommerce_checkout_after_order_review' ); ?>

</form>

<?php //do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
