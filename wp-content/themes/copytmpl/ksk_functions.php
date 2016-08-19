<?php

/*
 * Description: My functions for T9Z
 * Version: 1.0.0
 * Author: KSK
 */

//-----------------------------------------------------------
// Добавить произвольное поле на вкладку «вариативный товар»
//-----------------------------------------------------------
// Выводим произвольные поля
add_action( 'woocommerce_product_after_variable_attributes', 'ksk_variable_fields', 10, 3 );
function ksk_variable_fields( $loop, $variation_data, $variation ) {
    // Textarea
    woocommerce_wp_textarea_input( 
        array( 
            'id'          => '_ksk_var_price[' . $variation->ID . ']', 
            'label'       => 'Варианты стоимости в зависимости от количества страниц:', 
            'placeholder' => '', 
            'description' => '',
            'value'       => get_post_meta( $variation->ID, '_ksk_var_price', true ),
        )
    );
}

//Сохраняем вариативные поля
add_action( 'woocommerce_save_product_variation', 'ksk_variable_fields_save', 10, 2 );
function ksk_variable_fields_save( $post_id ) {
    // Textarea
    $textarea = $_POST['_ksk_var_price'][ $post_id ];
    if( ! empty( $textarea ) ) {
            update_post_meta( $post_id, '_ksk_var_price', esc_attr( $textarea ) );
    }    
}

// Изменение стоимости заказа
add_action( 'woocommerce_before_calculate_totals', 'ksk_add_custom_price' );
function ksk_add_custom_price( $cart_object ) {
    $locale_info = localeconv();
    
    foreach ( $cart_object->cart_contents as $key => $value ) {
        $pr_attributes = (array) maybe_unserialize( get_post_meta( $value['product_id'], '_product_attributes', true ) );
        $post_name = $value['data']->post;
        $post_name = $post_name->post_name;
        $orig_price = (int)$value['data']->price;
        
        $var_ksk_prices = get_post_meta( $value['variation_id'], '_ksk_var_price', true );

        $pr_quantity = explode('|', $pr_attributes['kolichestvo']['value']);
        $var_ksk_prices = explode('|', $var_ksk_prices);

        for ($i=0;$i < count($pr_quantity); $i++) {
            $pr_quantity[$i] = explode('-', $pr_quantity[$i]);
            if ($locale_info['decimal_point'] !== ',') {
                $var_ksk_prices[$i] = str_replace(',', $locale_info['decimal_point'], $var_ksk_prices[$i]);
            }
        }

        for ($i=0;$i < count($pr_quantity); $i++) {
            $calc_price = $var_ksk_prices[$i];

            if (count($pr_quantity[$i]) == 2) {
                if ($value['quantity']>=$pr_quantity[$i][0] && $value['quantity']<=$pr_quantity[$i][1]) {
                    $value['data']->price = $var_ksk_prices[$i];
                    break;
                }
            } else {
                if ($value['quantity']>=$pr_quantity[$i][0]) {
                    $value['data']->price = $var_ksk_prices[$i];
                    break;
                }
            }
        }

        $cart_amount = $cart_amount + ((int)$value['data']->price * (int)$value['quantity']);
    }
    
    //WC()->cart->add_fee( __('Shipping Cost', 'woocommerce'), 35);
}

// Обновление кол-ва копий и страниц в данных загрузок
add_action( 'woocommerce_after_cart_item_quantity_update', 'ksk_update_uploads_copies' );
//add_action('woocommerce_cart_updated', 'ksk_update_uploads_copies');
function ksk_update_uploads_copies() {
    $cart_totals  = isset( $_POST['cart'] ) ? $_POST['cart'] : '';
    if ( ! WC()->cart->is_empty() && is_array( $cart_totals ) ) {
        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            if (!empty($cart_item['variation_id'])) {
              $need_uploads = WPF_Uploads::product_needs_upload($cart_item['variation_id'], true);
            } else {
              $need_uploads = WPF_Uploads::product_needs_upload($cart_data->post->ID);
            }

            if ($need_uploads) {
                $current_uploads = WPF_Uploads_Before::get_cart_item_uploads($cart_item, $cart_item_key);
                
                if (is_array($current_uploads) && count($current_uploads)) {
                    foreach ($current_uploads AS $key => $value) {
                        $value = apply_filters('wpf_umf_cart_uploaded_file', array(
                            'name' => $value['name'],
                            'extension' => $value['extension'],
                            'path' =>  $value['path'],
                            'thumb' => $value['thumb'],
                            'status' => $value['status'],
                            'type' => $value['type'],
                            'pages' => $value['pages'],
                            'copies' => $value['copies'],
                        ), $value);
                        
                        $pages = 'pages_'.(!empty($cart_item['variation_id']) ? $cart_item['variation_id'] : $cart_item['product_id']).'_'.$key;
                        $copies = 'copies_'.(!empty($cart_item['variation_id']) ? $cart_item['variation_id'] : $cart_item['product_id']).'_'.$key;
                        
                        $value['pages'] = isset( $_POST[$pages] ) ? $_POST[$pages] : '';
                        $value['copies'] = isset( $_POST[$copies] ) ? $_POST[$copies] : '';
                        
                        //$upload_data[$cart_item['product_id']][$key]['"'.$value['type'].'"'][$key] = array(
                        //$upload_data[$cart_item['product_id']][$key][1][$key] = array(
                        //$upload_data = [];
                        $upload_data[!empty($cart_item['variation_id']) ? $cart_item['variation_id'] : $cart_item['product_id']][1][1][$key+1] = array(
                            'name' => $value['name'],
                            'extension' => $value['extension'],
                            'path' =>  $value['path'],
                            'thumb' => $value['thumb'],
                            'status' => $value['status'],
                            'type' => $value['type'],
                            'pages' => $value['pages'],
                            'copies' => $value['copies']
                        );
                        
                        WPF_Uploads_Before::save_temp_upload_data($upload_data);
                    }
                }
            }
        }
        
        // Добавим надбавку
        /*if (isset($_POST['natsenka-30']) || isset($_GET['natsenka-30'])) {
            $_SESSION['natsenka-30'] = 'on';
        } else {
            unset($_SESSION['natsenka-30']);
        }
        
        if (isset($_POST['user-bonus']) || isset($_GET['user-bonus'])) {
            $_SESSION['user-bonus'] = 'on';
        } else {
            unset($_SESSION['user-bonus']);
        }*/
    }
}

add_action( 'woocommerce_cart_calculate_fees', 'ksk_woocommerce_custom_surcharge' );
function ksk_woocommerce_custom_surcharge() {
    global $woocommerce;
    
    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;

    $surcharge = 0;
    $shipping_cost = 0;
    $subtotal = $woocommerce->cart->subtotal;
 
    /*if (isset($_POST['natsenka-30']) || isset($_GET['natsenka-30'])) {
        $_SESSION['natsenka-30'] = 'on';
    } else {
        //unset($_SESSION['natsenka-30']);
    }*/
    
    //if (isset($_SESSION['natsenka-30']) && ($_SESSION['natsenka-30']== 'on')) {
    if (ksk_check_var_in_session_post_get('natsenka-30', 'on')) {
        $shipping_settings = maybe_unserialize(get_option('woocommerce_t9z_shipping_settings', null));
        if ((count($shipping_settings) > 0) && ($shipping_settings['enabled'] == 1) && (count($shipping_settings['shipping_sets']) > 0)) {
            $percentage = (int)$shipping_settings['natsenka_rate'] / 100;
        } else {
            $percentage = 0.3;
        }
	//$surcharge = ( $woocommerce->cart->cart_contents_total + $woocommerce->cart->shipping_total ) * $percentage;	
	$surcharge = $woocommerce->cart->cart_contents_total * $percentage;	
	$woocommerce->cart->add_fee( 'Наценка за срочное выполнение', $surcharge, true, '' );
    }
    
    if (ksk_check_var_in_session_post_get('shipping-amount')) {
        //$_SESSION['shipping-amount'] = isset($_POST['shipping-amount']) ? $_POST['shipping-amount'] : (isset($_POST['shipping_cost']) ? $_POST['shipping_cost'] : $_SESSION['shipping-amount']);
        $shipping_cost = ksk_get_var_from_session_post_get('shipping-amount', 0);
    } else {
        $shipping_cost = ksk_shipping_cost_calc();
    }
    
    if ($shipping_cost > 0) {
        $woocommerce->cart->add_fee( 'Стоимость доставки', $shipping_cost, true, '' );
    }
    /*if (isset($_POST['user-bonus']) || isset($_GET['user-bonus'])) {
        $_SESSION['user-bonus'] = 'on';
    } else {
        //unset($_SESSION['user-bonus']);
    }*/
    
    //if (is_user_logged_in() && isset($_SESSION['user-bonus']) && ($_SESSION['user-bonus']== 'on')) {
    if (is_user_logged_in() && ksk_check_var_in_session_post_get('user-bonus', 'on')) {
        $user_bonus_amount = get_user_meta(get_current_user_id(), 'bonus_amount', true);
        $user_bonus_amount = !empty($user_bonus_amount) ? $user_bonus_amount : 0;
        $total = $subtotal + $surcharge + $shipping_cost;
        if ($total > $user_bonus_amount) {
            $order_bonus_amount = $user_bonus_amount;
        } else {
            $order_bonus_amount = $user_bonus_amount - $total;
        }
	$woocommerce->cart->add_fee( 'Сумма бонусов, использованных для оплаты', ($order_bonus_amount * (-1)), true, '' );
    }
}

// Выбор списка городов из настроек метода доставки 'woocommerce_t9z_shipping_settings'
function ksk_get_shipping_cities() {
    $cities = array();
    $shipping_settings = maybe_unserialize(get_option('woocommerce_t9z_shipping_settings', null));
    if ((count($shipping_settings) > 0) && ($shipping_settings['enabled'] == 1) && (count($shipping_settings['shipping_sets']) > 0)) {
        $cities = $shipping_settings['shipping_sets'];
    }
    
    return $cities;
}

// Выбор города по умолчанию из настроек метода доставки 'woocommerce_t9z_shipping_settings'
function ksk_get_shipping_default_city() {
    $city = '';
    $shipping_settings = maybe_unserialize(get_option('woocommerce_t9z_shipping_settings', null));
    if ((count($shipping_settings) > 0) && ($shipping_settings['enabled'] == 1) && (count($shipping_settings['shipping_sets']) > 0)) {
        foreach ($shipping_settings['shipping_sets'] as $key => $value) {
            //echo '<br>$shipping_settings[default_city] = '.$shipping_settings['default_city'];
            //echo '<br>$value[city] = '.$value['city'];
            if ($value['city'] == $shipping_settings['default_city']) {
                $city = $value['city'];
                break;
            }
        }
    }
    
    return $city;
}

// Расчет стоимости доставки в зависимости от выбранного города из списка настроек метода доставки 'woocommerce_t9z_shipping_settings'
function ksk_shipping_cost_calc() {
    global $woocommerce;
 
    $shipping_cost = 0;
    $total = $woocommerce->cart->subtotal;
    $shipping_settings = maybe_unserialize(get_option('woocommerce_t9z_shipping_settings', null));
    $city = ksk_get_var_from_session_post_get('shipping_city', '');
    
    if ($shipping_settings && ($total < (int)$shipping_settings['free_shipping_amount']) && ($city != '') && (count($shipping_settings) > 0) && ($shipping_settings['enabled'] == 1) && (count($shipping_settings['shipping_sets']) > 0)) {
        foreach ($shipping_settings['shipping_sets'] as $key => $value) {
            if ($value['city'] == $city) {
                $shipping_cost = (float)$value['amount'];
                break;
            }
        }
    }
    
    return $shipping_cost;
}

// Запись выбранного города в $_SESSION
function ksk_shipping_city_session_set() {
    $shipping_city = isset($_POST['shipping_city']) && is_string($_POST['shipping_city']) ? $_POST['shipping_city'] : '';
    
    if (!isset($_SESSION['shipping_city']) || ($_SESSION['shipping_city'] != $shipping_city)) {
        $_SESSION['shipping_city'] = $shipping_city;
    }
    
    // Если мы в корзине, то нужно вернуть стоимость доставки и перестроить список
    if (isset($_POST['cart']) && ($_POST['cart'] == '1')) {
        $output = ksk_woocommerce_t9z_shipping_cart_print($_POST['shipping_city']);
        echo json_encode($output);
    }
        
    wp_die();
}
add_action("wp_ajax_ksk_shipping_city_session_set", "ksk_shipping_city_session_set");
add_action("wp_ajax_nopriv_ksk_shipping_city_session_set", "ksk_shipping_city_session_set");

/**
 * функция возвращет конкретное значение из полученного массива данных по ip
 * @param string - ключ массива. Если интересует конкретное значение. 
 * Ключ может быть равным 'inetnum', 'country', 'city', 'region', 'district', 'lat', 'lng'
 * @param bolean - устанавливаем хранить данные в куки или нет
 * Если true, то в куки будут записаны данные по ip и повторные запросы на ipgeobase происходить не будут.
 * Если false, то данные постоянно будут запрашиваться с ipgeobase
 * @return array OR string - дополнительно читайте комментарии внутри функции.
 */
function get_the_user_geo_data($key = null, $cookie = true) {
    include_once ABSPATH . '/wp-content/themes/copytmpl/geo.php';
    
    $geo = new Geo(array('ip' => '77.66.129.10')); // запускаем класс
    $data_geo = $geo->get_value($key, $cookie);
    
    return $data_geo;
}

// Обновление блока с методами доставки с помощью Ajax
function ksk_wc_t9z_shipping_cart_print() {
    if (isset($_SESSION['shipping_city']) && isset($_POST['shipping_city']) && ($_SESSION['shipping_city'] != $_POST['shipping_city'])) {
        $_SESSION['shipping_city'] = $_POST['shipping_city'];
    }
    $output = ksk_woocommerce_t9z_shipping_cart_print($_POST['shipping_city']);
    echo json_encode($output);
    wp_die();
}
add_action("wp_ajax_ksk_wc_t9z_shipping_cart_print", "ksk_wc_t9z_shipping_cart_print");
add_action("wp_ajax_nopriv_ksk_wc_t9z_shipping_cart_print", "ksk_wc_t9z_shipping_cart_print");

// Обновление блока с методами доставки с помощью Ajax
function ksk_wc_t9z_shipping_cart_calc() {
    global $woocommerce;
    
    do_action('woocommerce_cart_calculate_fees');
    do_action('woocommerce_cart_total');
    
    ob_start();
    do_action( 'woocommerce_after_cart_table' );
    do_action( 'woocommerce_cart_collaterals' );
    do_action( 'woocommerce_after_cart' ); 
    $after_cart_html = ob_get_clean();
        
    $output = array(
        'total' => number_format($woocommerce->cart->total, 2, '.', ' ').' руб.',
        'after_cart_html' => $after_cart_html,
    );
    echo json_encode($output);
    wp_die();
}
add_action("wp_ajax_ksk_wc_t9z_shipping_cart_calc", "ksk_wc_t9z_shipping_cart_calc");
add_action("wp_ajax_nopriv_ksk_wc_t9z_shipping_cart_calc", "ksk_wc_t9z_shipping_cart_calc");

// Вывод способов доставки в корзине
function ksk_woocommerce_t9z_shipping_cart_print($city = null) {
    global $woocommerce;
    
    $output = '';
    $shipping_cost = 0;
    $bonus_amount = 0;
    $surcharge = 0;
    $user_bonus_amount = 0; 
    
    //$user_geo_data = get_the_user_geo_data();
    $city = isset($city) ? $city : (isset($_SESSION['shipping_city']) ? $_SESSION['shipping_city'] : '');
    if ($city == '') {
        $output .= '<div class="woocommerce-error">Необходимо выбрать город доставки в шапке сайта.</div>';
        return $output;
    }
        
    $shipping_settings = maybe_unserialize(get_option('woocommerce_t9z_shipping_settings', null));
    if ((count($shipping_settings) > 0) && ($shipping_settings['enabled'] == 1) && (count($shipping_settings['shipping_sets']) > 0)) {
        //$key = array_search($user_geo_data['city'], $shipping_settings['shipping_sets']);
        $key = null;
        foreach ($shipping_settings['shipping_sets'] AS $id => $data) {
            if ($data['city'] == $city) {
                $key = $id;
                break;
            }
        }
        
        if ($key) { 
            //$total = WC()->cart->get_cart_total();
            $total = $woocommerce->cart->subtotal;
            $bonus_amount = round(($total * (int)$shipping_settings['bonus_rate']) / 100, 2);
            if (ksk_check_var_in_session_post_get('natsenka-30', 'on')) {
                $surcharge = $woocommerce->cart->cart_contents_total * (int)$shipping_settings['natsenka_rate'] / 100;
            }
            
            if (ksk_check_var_in_session_post_get('user-bonus', 'on')) {
                $user_bonus_amount = get_user_meta(get_current_user_id(), 'bonus_amount', true);
                $user_bonus_amount = !empty($user_bonus_amount) ? $user_bonus_amount : 0;
                $total = $total - $user_bonus_amount;
            }
                    
            if ($total >= (int)$shipping_settings['free_shipping_amount']) {
            } else {
                $shipping_cost = $shipping_settings['shipping_sets'][$key]['amount'];
            }
            
            $label = 'Доставка по <strong>г.'.$shipping_settings['shipping_sets'][$key]['city'].' - Бесплатно</strong> (сумма заказа превышает <strong>'.$shipping_settings['free_shipping_amount'].' руб.</strong>)';
            $output .= '
            <div class="print-cart-item-field">
                <label id="l_t9z_shipping_1_free"><input type="radio" id="t9z_shipping_1_free" name="t9z_shipping_1" value="free" '.((isset($_POST['t9z_shipping_1']) && ($_POST['t9z_shipping_1'] == 'free')) ? 'checked="checked"' : '').' data-cost="0" data-label="'.$label.'" '.(($total < (int)$shipping_settings['free_shipping_amount']) ? 'disabled="disabled"' : '').'>'.$label.'</label>
            </div>';

            $label = 'Доставка по <strong>г.'.$shipping_settings['shipping_sets'][$key]['city'].' - '.($shipping_settings['shipping_sets'][$key]['amount'] > 0 ? $shipping_settings['shipping_sets'][$key]['amount'].' руб.' : 'Бесплатно').'</strong>';
            $output .= '
            <div class="print-cart-item-field">
                <label id="l_t9z_shipping_1_city"><input type="radio" id="t9z_shipping_1_city" name="t9z_shipping_1" value="city" '.((isset($_POST['t9z_shipping_1']) && ($_POST['t9z_shipping_1'] == 'city')) ? 'checked="checked"' : '').' data-cost="'.$shipping_settings['shipping_sets'][$key]['amount'].'" data-label="'.$label.'"'.(($total >= (int)$shipping_settings['free_shipping_amount']) ? 'disabled="disabled"' : '').'>'.$label.'</label>
            </div>';
    
            $label = 'Получение в офисе - <strong>Бесплатно</strong>';
            $output .= '
            <div class="print-cart-item-field">
                <label id="l_t9z_shipping_1_office"><input type="radio" id="t9z_shipping_1_office" name="t9z_shipping_1" value="office" '.((!isset($_POST['t9z_shipping_1']) || ($_POST['t9z_shipping_1'] == 'office')) ? 'checked="checked"' : '').' data-cost="0" data-label="'.$label.'">'.$label.'</label> 
                <div class="print-cart-item-subfields" style="'.((!isset($_POST['t9z_shipping_1']) || ($_POST['t9z_shipping_1'] == 'office')) ? 'display: none;' : '').'">';
                    
                    $office = explode('|', $shipping_settings['shipping_sets'][$key]['offices']);
                    for($i=0; $i < count($office); $i++) {
                        $label = 'Адрес точки самовывоза: г.'.$shipping_settings['shipping_sets'][$key]['city'].', '.$office[$i];
                        $output .= '<div class="print-cart-item-field"><label id="l_t9z_shipping_2_'.$i.'"><input type="radio" id="t9z_shipping_2_'.$i.'" name="t9z_shipping_2" value="'.$i.'" data-label="'.$label.'">г.'.$shipping_settings['shipping_sets'][$key]['city'].', '.$office[$i].', <a href="#map_canvas" onclick="ShowMap(\'' . $shipping_settings['shipping_sets'][$key]['city'].' '.$office[$i] . '\');">на карте</a></label></div>';
                    }
                    
            $output .= '<div id="map_canvas"></div>';
            $output .= '</div></div>';
        }
    } else {
        $output .= '<div class="woocommerce-error">Необходимо активировать метод доставки "T9Z" и выполнить настройку хотя бы для одного города.</div>';
    } 
    
    //$a1 = WC()->cart;
    //WC()->cart->shipping_total = $shipping_cost;
    //$a2 = WC()->cart;
    //$woocommerce->cart->add_fee( 'Стоимость доставки', $shipping_cost, true, '' );
    //$output .= '<div>';
    //$fee = WC()->cart->get_fees();
    //$output .= print_r($fee);
    //$output .= '</div>';
    
    //WC_AJAX::update_shipping_method();
    //WC_T9z_Shipping::calculate_shipping();
    //do_action( 'woocommerce_shipping_init');
    /*do_action('woocommerce_cart_calculate_fees');
    do_action('woocommerce_cart_total');
    
    ob_start();
    do_action( 'woocommerce_after_cart_table' );
    do_action( 'woocommerce_cart_collaterals' );
    do_action( 'woocommerce_after_cart' ); 
    $after_cart_html = ob_get_clean();*/
        
    return array(
        'shipping_method' => $output,
        'shipping_amount' => $shipping_cost,
        'free_shipping_amount' => (int)$shipping_settings['free_shipping_amount'],
        'bonus_amount' => $bonus_amount,
        'bonus_percent' => (int)$shipping_settings['bonus_rate'],
        'surcharge' => $surcharge,
        'natsenka_percent' => (int)$shipping_settings['natsenka_rate'],
        'total' => ($total + $surcharge + $shipping_cost),
        //'after_cart_html' => $after_cart_html,
    );
}

// Remove some checkout billing fields
function ksk_wc_filter_billing_fields($fields){
    unset( $fields["billing_country"] );
    unset( $fields["billing_company"] );
    unset( $fields["billing_address_1"] );
    unset( $fields["billing_address_2"] );
    unset( $fields["billing_city"] );
    unset( $fields["billing_state"] );
    unset( $fields["billing_postcode"] );
    //unset( $fields["billing_phone"] );
    return $fields;
}
add_filter( 'woocommerce_billing_fields', 'ksk_wc_filter_billing_fields' );

/*
 * Заказ выполнен - статус complete
 * Обновление суммы бонусов в аккаунте пользователя
 * @param int $order_id
 */
function ksk_wc_order_status_completed( $order_id ) {
    $order = new WC_Order($order_id);
    $user_id = (int)$order->user_id;
    $order_total = $order->get_total();
    
    $user_bonus_amount = get_user_meta($user_id, 'bonus_amount', true);
    $user_bonus_amount = !empty($bonus_amount) ? $bonus_amount : 0;
    
    //$shipping_settings = maybe_unserialize(get_option('woocommerce_t9z_shipping_settings', null));
    //$order_bonus_amount = round(($order_total * (int)$shipping_settings['bonus_rate']) / 100, 2);
    $order_bonus_amount = get_post_meta($order_id, 'ksk_wc_order_bonus-amount', true);
    $order_bonus_amount = !empty($order_bonus_amount) ? $order_bonus_amount : 0;
    
    $user_bonus_amount = $user_bonus_amount + $order_bonus_amount;
    
    // Обновление суммы бонусов в аккаунте пользователя
    update_user_meta($user_id, 'bonus_amount', $user_bonus_amount);
}
add_action( 'woocommerce_order_status_completed', 'ksk_wc_order_status_completed' );


/*
 * Заказ на удержании - статус on-hold
 * Добавим meta-поля заказа, спишем сумму использованных бонусов в аккаунте пользователя
 * @param int $order_id
 */
function ksk_wc_order_status_hold( $order_id ) {
    $order = new WC_Order($order_id);
    $user_id = (int)$order->user_id;
    $order_total = $order->get_total();
    
    $shipping_city = ksk_get_var_from_session_post_get('shipping_city', '');
    $shipping_method = ksk_get_var_from_session_post_get('t9z_shipping_1', 0);
    $shipping_punkt = (int)ksk_get_var_from_session_post_get('t9z_shipping_2', 0) + 1;
    
    // Необходимо выполнять проверку на присвоение номера заказу
    $order_number_meta = get_post_meta($order_id, 'ksk_wc_order_number', true);
    if (empty($order_number_meta)) {
        $order_last_number = (int)get_option('ksk_wc_order_last_number', 0);
        $order_number = $order_last_number + 1;
        update_option('ksk_wc_order_last_number', $order_number);

        $order_number_txt = mb_substr($shipping_city, 0, 1, 'UTF-8');
        $order_number_txt .= ($shipping_method === 'office' ? $shipping_punkt : '');
        $order_number_txt .= '-'.$order_number;
        update_post_meta($order_id, 'ksk_wc_order_number', $order_number_txt);
    }
    
    
    // Запишем meta-поля заказа
    update_post_meta($order_id, 'ksk_wc_order_natsenka-30', ksk_get_var_from_session_post_get('natsenka-30', 0));
    update_post_meta($order_id, 'ksk_wc_order_natsenka-amount', ksk_get_var_from_session_post_get('natsenka-amount', 0));
    update_post_meta($order_id, 'ksk_wc_order_natsenka-percent', ksk_get_var_from_session_post_get('natsenka-percent', 0));
    update_post_meta($order_id, 'ksk_wc_order_user-bonus', ksk_get_var_from_session_post_get('user-bonus', 0));
    update_post_meta($order_id, 'ksk_wc_order_bonus-amount', ksk_get_var_from_session_post_get('bonus-amount', 0));
    update_post_meta($order_id, 'ksk_wc_order_bonus-percent', ksk_get_var_from_session_post_get('bonus-percent', 0));
    update_post_meta($order_id, 'ksk_wc_order_t9z_shipping_1', ksk_get_var_from_session_post_get('t9z_shipping_1', ''));
    update_post_meta($order_id, 'ksk_wc_order_t9z_shipping_2', ksk_get_var_from_session_post_get('t9z_shipping_2', ''));
    update_post_meta($order_id, 'ksk_wc_order_shipping_city', $shipping_city);
    update_post_meta($order_id, 'ksk_wc_order_shipping-amount', ksk_get_var_from_session_post_get('shipping-amount', 0));
    update_post_meta($order_id, 'ksk_wc_order_shipping-office', ksk_get_var_from_session_post_get('shipping-office', ''));
    update_post_meta($order_id, 'ksk_wc_order_pay-method', ksk_get_var_from_session_post_get('pay-method', 0));
    update_post_meta($order_id, 'ksk_wc_order_comments', ksk_get_var_from_session_post_get('comments', ''));
    
    // Если выбран пункт списания бонусов в оплату заказа
    if (ksk_check_var_in_session_post_get('user-bonus', 'on')) {
        $user_bonus_amount = get_user_meta($user_id, 'bonus_amount', true);
        $user_bonus_amount = !empty($bonus_amount) ? $bonus_amount : 0;
        
        $order_total_real = (int)ksk_get_var_from_session_post_get('subtotal-amount', 0) + (int)ksk_get_var_from_session_post_get('natsenka-amount', 0) + (int)ksk_get_var_from_session_post_get('shipping-amount', 0);
        
        if ($order_total_real >= $user_bonus_amount) {
            $order_bonus_amount = $user_bonus_amount;
            $user_bonus_amount = 0;
        } else {
            $order_bonus_amount = $order_total_real;
            $user_bonus_amount = $user_bonus_amount - $order_total_real;
        }
        
        update_post_meta($order_id, 'ksk_wc_order_bonus-amount-use', $order_bonus_amount);
        
        // Обновление суммы бонусов в аккаунте пользователя
        update_user_meta($user_id, 'bonus_amount', $user_bonus_amount);
    }
    
    // Очистим поля в $_SESSION
    //ksk_clear_t9z_cart_new_field_from_session();
}
add_action( 'woocommerce_order_status_pending', 'ksk_wc_order_status_hold' );
add_action( 'woocommerce_order_status_on-hold', 'ksk_wc_order_status_hold' );
add_action( 'woocommerce_order_status_processing', 'ksk_wc_order_status_hold' );
add_action( 'woocommerce_order_status_completed', 'ksk_wc_order_status_hold' );
add_action( 'woocommerce_checkout_order_processed', 'ksk_wc_order_status_hold' );

/*
 * Заказ отменен - статус cancelled
 * Обновление суммы бонусов в аккаунте пользователя
 * @param int $order_id
 */
function ksk_wc_order_status_cancelled( $order_id ) {
    $order = new WC_Order($order_id);
    $user_id = (int)$order->user_id;
    $order_total = $order->get_total();
    
    $user_bonus_amount = get_user_meta($user_id, 'bonus_amount', true);
    $user_bonus_amount = !empty($bonus_amount) ? $bonus_amount : 0;
    
    //$shipping_settings = maybe_unserialize(get_option('woocommerce_t9z_shipping_settings', null));
    //$order_bonus_amount = round(($order_total * (int)$shipping_settings['bonus_rate']) / 100, 2);
    $order_bonus_amount = get_post_meta($order_id, 'ksk_wc_order_bonus-amount', true);
    $order_bonus_amount = !empty($order_bonus_amount) ? $order_bonus_amount : 0;
    
    $order_bonus_amount_use = get_post_meta($order_id, 'ksk_wc_order_bonus-amount-use', true);
    $order_bonus_amount_use = !empty($order_bonus_amount_use) ? $order_bonus_amount_use : 0;
    
    $user_bonus_amount = $user_bonus_amount + $order_bonus_amount_use - $order_bonus_amount;
    
    // Обновление суммы бонусов в аккаунте пользователя
    update_user_meta($user_id, 'bonus_amount', $user_bonus_amount);
}
add_action( 'woocommerce_order_status_completed', 'ksk_wc_order_status_completed' );

// Очистим поля в $_SESSION после того, как корзина была очищена
function ksk_wc_cart_emptied() {
    // Очистим поля в $_SESSION
    ksk_clear_t9z_cart_new_field_from_session();
}
add_action( 'woocommerce_cart_emptied', 'ksk_wc_cart_emptied');

/*
 * Формирование номера заказа
 * @param int $oldnumber
 * @param object $order
 */
function ksk_wc_order_number( $oldnumber, $order ) {
    $order_id = (int)$order->id;
    $order_number = get_post_meta($order_id, 'ksk_wc_order_number', true);
    return empty($order_number) ? $oldnumber : $order_number;
}
add_filter( 'woocommerce_order_number', 'ksk_wc_order_number', 1, 2 );

/* 
 * Поиск переменной в массивах $_SESSION, $_POST, $_GET
 * @param string $name
 * @param $value_default
 * return $_SESSION[$name] OR $_POST[$name] OR $_GET[$name] OR $value_default
 */
function ksk_get_var_from_session_post_get($name, $value_default) {
    if (!isset($name) || ($name == '')) {
        $value = $value_default;
    } elseif (isset($_SESSION[$name])) {
        $value = $_SESSION[$name];
    } elseif (isset($_POST[$name])) {
        $value = $_POST[$name];
    } elseif (isset($_GET[$name])) {
        $value = $_GET[$name];
    } else {
        $value = $value_default;
    }
    
    return $value;
}

/* 
 * Поиск переменной в массивах $_SESSION, $_POST, $_GET и проверка ее значения с заданным
 * @param string $name
 * @param $value_check
 * return bool
 */
function ksk_check_var_in_session_post_get($name, $value_check=null) {
    if (!isset($name) || !is_string($name) ||($name == '')) {
        return false;
    } 
    
    if (!isset($value_check)) {
        $value = (isset($_SESSION[$name]) || isset($_POST[$name]) || isset($_GET[$name]));
    } else {
        $value = ((isset($_SESSION[$name]) && ($_SESSION[$name] === $value_check)) || (isset($_POST[$name]) && ($_POST[$name] === $value_check)) || (isset($_GET[$name]) && ($_GET[$name] === $value_check)));
    }
    
    return $value;
}


/* 
 * Сохранение значения переменной в $_SESSION, если оно установлено в $_POST
 * @param string $name
 */
function ksk_set_var_from_post_to_session($name) {
    if (isset($name) && is_string($name) && ($name != '') && isset($_POST[$name]) && (!isset($_SESSION[$name]) || ($_SESSION[$name] != $_POST[$name]))) {
        $_SESSION[$name] = $_POST[$name];
    }
}

/* 
 * Сохранение значения переменных в $_SESSION, если они установлены в $_POST
 * @param string OR array $names
 */
function ksk_set_vars_from_post_to_session($names) {
    if (!isset($names) || !is_array($names) || !is_string($names)) {
        return;
    }
    
    if (is_string($names) && ($names != '')) {
        ksk_set_var_from_post_to_session($names);
    } elseif (is_array($names) && (count($names) > 0)) {
        foreach ($names as $key) {
            ksk_set_var_from_post_to_session($key);
        }
    }
}

/*
 * Сохранение значения новых полей формы в корзине из $_POST в $_SESSION
 */
function ksk_save_t9z_cart_new_field_to_session() {
    $names = array(
        'natsenka-30' => 'off',
        'natsenka-amount' => 0,
        'natsenka-percent' => 0,
        'user-bonus' => 'off',
        'bonus-amount' => 0,
        'bonus-percent' => 0,
        't9z_shipping_1' => 'city',
        't9z_shipping_2' => 0,
        'shipping-amount' => 0,
        'shipping-office' => isset($_POST['shipping-text-2']) ? $_POST['shipping-text-2'] : '',
        'pay-method' => 2,
        'subtotal-amount' => 0,
        'total-amount' => 0,
        'comments' => '',
    );
    
    foreach ($names as $key => $value) {
        if (isset($_POST[$key])) {
            ksk_set_var_from_post_to_session($key);
        } else {
            $_SESSION[$key] = $value;
        }
    }
}
add_action("wp_ajax_ksk_save_t9z_cart_new_field_to_session", "ksk_save_t9z_cart_new_field_to_session");
add_action("wp_ajax_nopriv_ksk_save_t9z_cart_new_field_to_session", "ksk_save_t9z_cart_new_field_to_session");

/*
 * Удаление значений новых полей формы в корзине из $_SESSION
 */
function ksk_clear_t9z_cart_new_field_from_session() {
    $names = array(
        'natsenka-30' => 'off',
        'natsenka-amount' => 0,
        'natsenka-percent' => 0,
        'user-bonus' => 'off',
        'bonus-amount' => 0,
        'bonus-percent' => 0,
        't9z_shipping_1' => 'city',
        't9z_shipping_2' => 0,
        'shipping-amount' => 0,
        'shipping-office' => '',
        'pay-method' => 2,
        'subtotal-amount' => 0,
        'total-amount' => 0,
        'comments' => '',
    );
    
    foreach ($names as $key => $value) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
}



/****************************************************************************************
 * Desi4ik
 ****************************************************************************************/
function pdfCount ($filename) {
//Получаем количество страниц из заголовка с помощью регулярного выражения
    $fp = fopen($filename, 'r');
    if ($fp) {
        $count = 0;
        while(!feof($fp)) {
            $line = fgets($fp,255);
            if (preg_match('|/Count [0-9]+|', $line, $matches)){
                preg_match('|[0-9]+|', $matches[0], $matches2);
                if ($count < $matches2[0]) {
                    $count = trim($matches2[0]);
                }
            }
        }
        fclose($fp);
        if ((is_numeric($count)) && ($count > 0)) return $count;
        else {
            $pdf_content = file_get_contents($filename);
            $count = preg_match_all("/\/Page\W/", $pdf_content, $matches);
            //if ((is_numeric($count)) && ($count == 0)) $count = 9999999;
            return $count;
        }
    }
}

function txtCount ($filename) {
    $handle = @fopen($filename, "r");
    $i = 0; $kol_page = 0;
    if ($handle) {
        while (($buffer = fgets($handle)) !== false) {
            $i++;
            if ($i == 66){
                $kol_page++;
                $i = 0;
            }
        }
        if (($kol_page == 0) && ($i <=66)) $kol_page = 1;
        if (!feof($handle)) {
            echo "Error: unexpected fgets() fail\n";
        }
        fclose($handle);
    }
    return $kol_page;
}

function docxCount ($filename) {
    // Создаёт "реинкарнацию" zip-архива...
    $zip = new ZipArchive;
    // И пытаемся открыть переданный zip-файл
    if ($zip->open($filename)) {
        // В случае успеха ищем в архиве файл с данными
        if (($index = $zip->locateName("docProps/app.xml")) !== false) {
            // Если находим, то читаем его в строку
            $content = $zip->getFromIndex($index);
            // Закрываем zip-архив, он нам больше не нужен
            $zip->close();
            //$dom = simplexml_load_string($content);
           /* libxml_use_internal_errors(true);
            $dom = simplexml_load_string($content);
            foreach( libxml_get_errors() as $error ) {

                return $error;

                }*/
            $page_str = explode ("<Pages>", $content);
            $page_str = explode ("</Pages>", $page_str[1]);
            $pages = $page_str[0];
            //$pages = $dom->Pages; // Получаем элемента
            //$pages = strlen($content);
            return $pages;
        }
 
    }
    else return $pages = 99999;

    
}

function xlsCount ($filename){
    require_once '/classes/PHPExcel/IOFactory.php';
    require_once '/classes/ChunkReadFilter.php';

    $objReader = PHPExcel_IOFactory::createReader(PHPExcel_IOFactory::identify($filename));

    $spreadsheetInfo = $objReader->listWorksheetInfo($filename);

    // Загружаем файл XLS
    $objPHPExcel = $objReader->load($filename);

    $totalRows = $spreadsheetInfo[0]['totalRows'];

    if ($totalRows > 62) {
        $kolPages = ceil($totalRows / 62);
    }
    else $kolPages = 1;

    return $kolPages;


}

// on submit AJAX form of the cart
// after calculate cart form items
add_action('woocommerce_cart_updated', 'wac_update');
function wac_update() {
    // is_wac_ajax: flag defined on wooajaxcart.js
    
    if ( !empty($_POST['is_wac_ajax'])) {
        $resp = array();
  //      $resp['update_label'] = __( 'Update Cart', 'woocommerce' );
  //      $resp['checkout_label'] = __( 'Proceed to Checkout', 'woocommerce' );
          $resp['update_label'] = 'Обновить корзину';
          $resp['checkout_label'] = 'Оформить заказ';
        $resp['price'] = 0;
        
        // render the cart totals (cart-totals.php)
        ob_start();
        do_action( 'woocommerce_after_cart_table' );
        do_action( 'woocommerce_cart_collaterals' );
        do_action( 'woocommerce_after_cart' );
        $resp['html'] = ob_get_clean();
        $resp['price'] = 0;
        $resp['price_subtotal'] = 0;
        
        // calculate the item price
        if ( !empty($_POST['cart_item_key']) ) {
            $items = WC()->cart->get_cart();
            $cart_item_key = $_POST['cart_item_key'];
            
            if ( array_key_exists($cart_item_key, $items)) {
                $cart_item = $items[$cart_item_key];
                $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                $price = apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
                $resp['price'] = $price;
                $product_price = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
                $resp['special_price'] = $product_price;
                $resp['price_subtotal'] = WC()->cart->subtotal;
            }
        }

        echo json_encode($resp);
        exit;
    }
}


function ksk_custom_redirect()
{
    if (isset($_GET['wc-login-before-checkout']) || isset($_POST['wc-login-before-checkout'])) {
        return esc_url( get_permalink( wc_get_page_id( 'cart' ) ). '?wc-login-before-checkout=1' );
        //return esc_url( get_permalink( wc_get_page_id( 'checkout' ) ) );
    } else {
        return esc_url( get_permalink( wc_get_page_id( 'myaccount' ) ) );
    }
}
add_filter( 'woocommerce_registration_redirect', 'ksk_custom_redirect' );
add_filter( 'woocommerce_login_redirect', 'ksk_custom_redirect' );

function ksk_get_variation_price_html($variation_id)
{
    $ret_str = '';
    $ret_str1 = '';
    $ret_str2 = '';
    $product_id = wp_get_post_parent_id($variation_id);
    
    if (($product_id > 0) && ($variation_id > 0)) {
        $ret_str .= '<table>';
        
        $locale_info = localeconv();
        
        $pr_attributes = (array) maybe_unserialize( get_post_meta( $product_id, '_product_attributes', true ) );
        $pr_quantity = explode('|', $pr_attributes['kolichestvo']['value']);
        
        $var_ksk_prices = get_post_meta( $variation_id, '_ksk_var_price', true );
        $var_ksk_prices = explode('|', $var_ksk_prices);

        $ret_str1 .= '<tr><td>Количество<br>страниц</td>';
        $ret_str2 .= '<tr><td>Стоимость<br>страницы</td>';
        for ($i=0;$i < count($pr_quantity); $i++) {
            //$pr_quantity[$i] = explode('-', $pr_quantity[$i]);
            if ($locale_info['decimal_point'] !== ',') {
                $var_ksk_prices[$i] = str_replace(',', $locale_info['decimal_point'], $var_ksk_prices[$i]);
            }
            $ret_str1 .= '<td>' . $pr_quantity[$i] . '</td>';
            $ret_str2 .= '<td>' . $var_ksk_prices[$i] . '<br>руб.</td>';
        }
        $ret_str1 .= '</tr>';
        $ret_str2 .= '</tr>';
        
        $ret_str .= $ret_str1 . $ret_str2 . '</table>';
    }
    
    return $ret_str;
}
//add_filter( 'woocommerce_show_variation_price', 'ksk_show_variation_price' );

function ksk_shop_order_columns($columns)
{
    $new_columns = array();
    /*$new_columns = (is_array($columns)) ? $columns : array();
    unset( $new_columns['order_actions'] );

    //edit this for you column(s)
    //all of your columns will be added before the actions column
    $new_columns['ksk_wc_order_shipping_city'] = 'Город';
    $new_columns['ksk_wc_order_shipping-office'] = 'Точка самовывоза';
    //stop editing

    $new_columns['order_actions'] = $columns['order_actions'];*/
    
    foreach ($columns as $key => $value) {
        if ($key == 'shipping_address') {
            $new_columns['ksk_wc_order_shipping_city'] = 'Город';
            $new_columns['ksk_wc_order_shipping-office'] = 'Офис';
        } else {
            $new_columns[$key] = $value;
        }
    }
    
    return $new_columns;
}
add_filter( 'manage_edit-shop_order_columns', 'ksk_shop_order_columns' );


function ksk_shop_order_column_values($column)
{
    global $post;
    $data = get_post_meta( $post->ID );

    //start editing, I was saving my fields for the orders as custom post meta
    //if you did the same, follow this code
    if ( $column == 'ksk_wc_order_shipping_city' ) {    
        echo (isset($data['ksk_wc_order_shipping_city']) ? $data['ksk_wc_order_shipping_city'][0] : '');
    }
    if ( $column == 'ksk_wc_order_shipping-office' ) {   
        //echo (isset($data['ksk_wc_order_shipping-office']) ? $data['ksk_wc_order_shipping-office'][0] : '');
        if (isset($data['ksk_wc_order_shipping-office'])) {
            $d1 = explode(':', $data['ksk_wc_order_shipping-office'][0]);
            $d2 = explode(',', $d1[1]);
            echo $d2[1];
        }
    }
    //stop editing    
}
add_action( 'manage_shop_order_posts_custom_column', 'ksk_shop_order_column_values', 2 );

function ksk_shop_order_columns_sort($columns)
{
    $custom = array(
        //start editing
        'ksk_wc_order_shipping_city' => 'ksk_wc_order_shipping_city',
        'ksk_wc_order_shipping-office' => 'ksk_wc_order_shipping-office'
        //stop editing
    );
    
    return wp_parse_args( $custom, $columns );    
}
add_filter( "manage_edit-shop_order_sortable_columns", 'ksk_shop_order_columns_sort' );

function ksk_shop_filter_orders($query) 
{
    global $pagenow;
    $qv = &$query->query_vars;

    $user = wp_get_current_user();
    $user_shipping_city = get_user_meta($user->ID, 'shipping_city', true);

    if ($pagenow == 'edit.php' && isset($qv['post_type']) && $qv['post_type'] == 'shop_order') {            
        if (in_array('shop_manager', (array) $user->roles ) && ($user_shipping_city !== '')) {
            $query->set('meta_key', 'ksk_wc_order_shipping_city');
            $query->set('meta_value', $user_shipping_city);
        }
    }

    return $query;
}
add_filter('pre_get_posts', 'ksk_shop_filter_orders');
