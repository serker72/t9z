<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see     http://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.3.8
 */

wc_print_notices();

$file_ext_work = array('pdf', 'docx', 'txt');

do_action( 'woocommerce_before_cart' ); ?>
<div class="print-cart">
<form id="ksk_wc_cart_form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">

<?php do_action( 'woocommerce_before_cart_table' ); ?>

<!--table class="shop_table shop_table_responsive cart" cellspacing="0"-->
<table class="print-cart-table cart" cellspacing="0">
	<thead>
		<tr>
			<!--th class="product-remove">&nbsp;</th>
			<th class="product-thumbnail">&nbsp;</th>
			<th class="product-name"><?php //_e( 'Product', 'woocommerce' ); ?></th>
			<th class="product-price"><?php //_e( 'Price', 'woocommerce' ); ?></th>
			<th class="product-quantity"><?php //_e( 'Quantity', 'woocommerce' ); ?></th>
			<th class="product-subtotal"><?php //_e( 'Total', 'woocommerce' ); ?></th-->
                    <th>Товар</th>
                    <th>Цена за шт.</th>
                    <th>Количество</th>
                    <th>Итого</th>
                    <th>Удалить</th>
		</tr>
	</thead>
	<tbody>
		<?php do_action( 'woocommerce_before_cart_contents' ); ?>

		<?php
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				?>
				<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

					<!--td class="product-thumbnail">
						<?php/*
							$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

							if ( ! $_product->is_visible() ) {
								echo $thumbnail;
							} else {
								printf( '<a href="%s">%s</a>', esc_url( $_product->get_permalink( $cart_item ) ), $thumbnail );
							}
						*/?>
					</td-->

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
                                                        $value = apply_filters('wpf_umf_cart_uploaded_file', [
                                                            'name' => $value['name'],
                                                            'pages' => $value['pages'],
                                                            'copies' => $value['copies'],
                                                        ], $value);
                                                        
                                                        $file_ext = explode(".", $value['name']);
                                                        $file_ext = $file_ext[1];

                                                        $return .= '<tr>';
                                                        $return .= '<td>'.$value['name'].'</td>';
                                                        //$return .= '<td><input type="text" id="pages_'.(!empty($cart_item['variation_id']) ? $cart_item['variation_id'] : $cart_item['product_id']).'_'.$key.'" name="pages_'.(!empty($cart_item['variation_id']) ? $cart_item['variation_id'] : $cart_item['product_id']).'_'.$key.'" value="'.esc_attr($value['pages']).'" title="" class="" size="4" readonly="true"></td>';
                                                        $return .= '<td'.(($cart_data->post->post_title == "Фотографии") ? ' style="display: none;"' : '').'><div class="print-options-photo-upload-image-item-num">';
                                                        $return .= '<span'.(!in_array($file_ext, $file_ext_work) ? ' class="print-options-photo-upload-image-item-num-selector"' : '').'>';
                                                        $return .= '<input type="text" step="1" min="1" max="" id="pages_'.(!empty($cart_item['variation_id']) ? $cart_item['variation_id'] : $cart_item['product_id']).'_'.$key.'" name="pages_'.(!empty($cart_item['variation_id']) ? $cart_item['variation_id'] : $cart_item['product_id']).'_'.$key.'" value="'.esc_attr($value['pages']).'" title="" class="" size="4"'.(in_array($file_ext, $file_ext_work) ? ' readonly="readonly"' : '').'>';
                                                        $return .= '</span></div></td>';
                                                        $return .= '<td><div class="print-options-photo-upload-image-item-num">';
                                                        $return .= '<span class="print-options-photo-upload-image-item-num-selector">';
                                                        $return .= '<input type="text" step="1" min="1" max="" id="copies_'.(!empty($cart_item['variation_id']) ? $cart_item['variation_id'] : $cart_item['product_id']).'_'.$key.'" name="copies_'.(!empty($cart_item['variation_id']) ? $cart_item['variation_id'] : $cart_item['product_id']).'_'.$key.'" value="'.esc_attr($value['copies']).'" title="" class="" size="4">';
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

					<td class="product-remove">
						<?php
                                                    echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
                                                            '<a href="%s" class="remove" title="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
                                                            esc_url( WC()->cart->get_remove_url( $cart_item_key ) ),
                                                            __( 'Remove this item', 'woocommerce' ),
                                                            esc_attr( $product_id ),
                                                            esc_attr( $_product->get_sku() )
                                                    ), $cart_item_key );
						?>
					</td>
				</tr>
				<?php
			}
		}

		//do_action( 'woocommerce_cart_contents' );
		?>
		<tr>
			<td colspan="6" class="actions">

				<?php /*if ( wc_coupons_enabled() ) { ?>
					<div class="coupon">

						<label for="coupon_code"><?php _e( 'Coupon', 'woocommerce' ); ?>:</label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" /> <input type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Apply Coupon', 'woocommerce' ); ?>" />

						<?php do_action( 'woocommerce_cart_coupon' ); ?>
					</div>
				<?php }*/ ?>

				<input type="submit" class="button" name="update_cart" value="<?php esc_attr_e( 'Update Cart', 'woocommerce' ); ?>" />

				<?php do_action( 'woocommerce_cart_actions' ); ?>

				<?php wp_nonce_field( 'woocommerce-cart' ); ?>
			</td>
		</tr>

		<?php do_action( 'woocommerce_after_cart_contents' ); ?>
	</tbody>
</table>
<?php $output = ksk_woocommerce_t9z_shipping_cart_print(); ?>
<div class="woocommerce-info" id="ksk_woocommerce_t9z_shipping_info" style="display: none;"></div>
<div class="woocommerce-error" id="ksk_woocommerce_t9z_shipping_error" style="display: none;"></div>
<div class="print-cart-urgently">
    <label>
        <input type="checkbox" id="natsenka-30" name="natsenka-30" <?php echo isset($_SESSION['natsenka-30']) ? 'checked="checked"' : ''; ?> >
        Срочное выполнение, наценка 30% - <strong><span id="natsenka-30-amount"><?php echo (isset($output['surcharge']) ? $output['surcharge'] : '0').' руб.'; ?></span></strong>
    </label>
</div>
<?php if (is_user_logged_in()) { ?>
<div class="print-cart-urgently">
    <label>
        <input type="checkbox" id="user-bonus" name="user-bonus" <?php echo isset($_SESSION['user-bonus']) ? 'checked="checked"' : ''; ?> >
        Использовать бонусы для частичной оплаты заказа - <strong><span id="user-bonus-amount-label">
            <?php 
            $user_bonus_amount = get_user_meta(get_current_user_id(), 'bonus_amount', true); 
            echo (!empty($user_bonus_amount) ? $user_bonus_amount : '0').' руб.'; 
            ?></span></strong>
    </label>
</div>
<?php } ?>
<!-- Выбор способа доставки -->
<div class="print-cart-item">
    <h3>Способ получения:</h3>
    <div id="woocommerce_t9z_shipping_settings">
    <?php echo isset($output['shipping_method']) ? $output['shipping_method'] : ''; ?>
    </div>
</div>
<!-- Выбор способа оплаты -->
<div class="print-cart-item">
    <h3>Способ оплаты:</h3>
    <div class="print-cart-item-field"><label><input type="radio" name="pay-method" value="1"> Наличные при получении</label></div>
    <div class="print-cart-item-field"><label><input type="radio" name="pay-method" value="2" checked="checked"> Банковской картой, электронные кошельки Яндекс.Деньги, Webmoney и пр.</label></div>
</div>
<!-- Стоимость заказа -->
<div class="print-cart-item">
    <h3>Стоимость заказа с учётом доставки<span id="total-amount-label"><?php echo (isset($_POST['natsenka-30']) || isset($_GET['natsenka-30']) || isset($_SESSION['natsenka-30'])) ? ' и наценки за срочность' : ''; ?></span>:</h3>
    <div class="print-cart-sum"><?php echo (isset($output['total']) ? $output['total'] : '0').' руб.'; ?></div>
    <div class="print-cart-sum-bonus"><span class="print-cart-sum-bonus-label">За этот заказ Вам будет начислено:</span> <span id="bonus_amount" style="font-weight: bold;"><?php echo isset($output['bonus_amount']) ? $output['bonus_amount'] : '0'; ?></span> бонусов</div>
</div>

<input type="hidden" id="natsenka-amount" name="natsenka-amount" value="<?php echo isset($output['surcharge']) ? $output['surcharge'] : '0'; ?>">
<input type="hidden" id="natsenka-percent" name="natsenka-percent" value="<?php echo isset($output['natsenka_percent']) ? $output['natsenka_percent'] : '0'; ?>">
<input type="hidden" id="shipping-amount" name="shipping-amount" value="<?php echo isset($output['shipping_cost']) ? $output['shipping_cost'] : '0'; ?>">
<input type="hidden" id="subtotal-amount" name="subtotal-amount" value="<?php echo WC()->cart->subtotal; ?>">
<input type="hidden" id="total-amount" name="total-amount" value="<?php echo isset($output['total']) ? $output['total'] : '0'; ?>">
<input type="hidden" id="bonus-amount" name="bonus-amount" value="<?php echo isset($output['bonus_amount']) ? $output['bonus_amount'] : '0'; ?>">
<input type="hidden" id="bonus-percent" name="bonus-percent" value="<?php echo isset($output['bonus_percent']) ? $output['bonus_percent'] : '0'; ?>">
<input type="hidden" id="shipping-text-1" name="shipping-text-1" value="">
<input type="hidden" id="shipping-text-2" name="shipping-text-2" value="">
<?php if (is_user_logged_in()) { ?>
<input type="hidden" id="user-bonus-amount" name="user-bonus-amount" value="<?php echo !empty($user_bonus_amount) ? $user_bonus_amount : '0'; ?>">
<?php } ?>

<?php do_action( 'woocommerce_after_cart_table' ); ?>

<div class="form-item form-item-submit">
    <a href="#" id="ksk-wc-proceed-to-checkout" class="checkout-button button alt wc-forward">Оформить заказ</a>
</div>
</form>
</div>
<div class="cart-collaterals" style="display: none;">

	<?php do_action( 'woocommerce_cart_collaterals' ); ?>

</div>

<?php //do_action( 'woocommerce_proceed_to_checkout'); ?>

<?php do_action( 'woocommerce_after_cart' ); ?>