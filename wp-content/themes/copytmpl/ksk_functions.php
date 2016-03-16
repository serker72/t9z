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
    foreach ( $cart_object->cart_contents as $key => $value ) {
        $pr_attributes = (array) maybe_unserialize( get_post_meta( $value['product_id'], '_product_attributes', true ) );
        $post_name = $value['data']->post;
        $post_name = $post_name->post_name;
        
        $var_ksk_prices = get_post_meta( $value['variation_id'], '_ksk_var_price', true );

        $pr_quantity = explode('|', $pr_attributes['kolichestvo']['value']);
        $var_ksk_prices = explode('|', $var_ksk_prices);

        for ($i=0;$i < count($pr_quantity); $i++) {
            $pr_quantity[$i] = explode('-', $pr_quantity[$i]);
        }

        for ($i=0;$i < count($pr_quantity); $i++) {
            $orig_price = (int)$value['data']->price;
            $calc_price = $var_ksk_prices[$i];

            if (count($pr_quantity[$i]) == 2) {
                if ($value['quantity']>=$pr_quantity[$i][0] && $value['quantity']<=$pr_quantity[$i][1]) {
                    $value['data']->price = $var_ksk_prices[$i];
                }
            } else {
                if ($value['quantity']>=$pr_quantity[$i][0]) {
                    $value['data']->price = $var_ksk_prices[$i];
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
                        $value = apply_filters('wpf_umf_cart_uploaded_file', [
                            'name' => $value['name'],
                            'extension' => $value['extension'],
                            'path' =>  $value['path'],
                            'thumb' => $value['thumb'],
                            'status' => $value['status'],
                            'type' => $value['type'],
                            'pages' => $value['pages'],
                            'copies' => $value['copies'],
                        ], $value);
                        
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
        if (isset($_POST['natsenka-30']) || isset($_GET['natsenka-30'])) {
            $_SESSION['natsenka-30'] = 'on';
        } else {
            unset($_SESSION['natsenka-30']);
        }
    }
}

add_action( 'woocommerce_cart_calculate_fees', 'ksk_woocommerce_custom_surcharge' );
function ksk_woocommerce_custom_surcharge() {
    global $woocommerce;
 
    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;

    if (isset($_POST['natsenka-30']) || isset($_GET['natsenka-30'])) {
        $_SESSION['natsenka-30'] = 'on';
    }
    
    if (isset($_SESSION['natsenka-30']) && ($_SESSION['natsenka-30']== 'on')) {
        $shipping_settings = maybe_unserialize(get_option('woocommerce_t9z_shipping_settings', null));
        if ((count($shipping_settings) > 0) && ($shipping_settings['enabled'] == 1) && (count($shipping_settings['shipping_sets']) > 0)) {
            $percentage = (int)$shipping_settings['natsenka_rate'] / 100;
        } else {
            $percentage = 0.3;
        }
	//$surcharge = ( $woocommerce->cart->cart_contents_total + $woocommerce->cart->shipping_total ) * $percentage;	
	$surcharge = $woocommerce->cart->cart_contents_total * $percentage;	
	$woocommerce->cart->add_fee( 'Срочное выполнение', $surcharge, true, '' );
    }
    
    if (isset($_POST['shipping_cost'])) {
        $shipping_cost = $_POST['shipping_cost'];
    } else {
        $shipping_cost = ksk_shipping_cost_calc();
    }
    
    $woocommerce->cart->add_fee( 'Стоимость доставки', $shipping_cost, true, '' );
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

// Расчет стоимости доставки в зависимости от выбранного города из списка настроек метода доставки 'woocommerce_t9z_shipping_settings'
function ksk_shipping_cost_calc() {
    global $woocommerce;
 
    $shipping_cost = 0;
    $total = $woocommerce->cart->subtotal;
    $shipping_settings = maybe_unserialize(get_option('woocommerce_t9z_shipping_settings', null));
    $city = isset($_SESSION['shipping_city']) ? $_SESSION['shipping_city'] : (isset($_POST['shipping_city']) ? $_POST['shipping_city'] : '');
    
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
        
    wp_die();
}
add_action("wp_ajax_ksk_shipping_city_session_set", "ksk_shipping_city_session_set");
add_action("wp_ajax_nopriv_ksk_shipping_city_session_set", "ksk_shipping_city_session_set");

include_once ABSPATH . '/wp-content/themes/copytmpl/geo.php';

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
    $geo = new Geo(['ip' => '77.66.129.10']); // запускаем класс
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
            $surcharge = $woocommerce->cart->cart_contents_total * (int)$shipping_settings['natsenka_rate'] / 100;
                    
            if ($total >= (int)$shipping_settings['free_shipping_amount']) {
                $output .= '
                <div class="print-cart-item-field">
                    <label><input type="radio" id="t9z_shipping_1_free" name="t9z_shipping_1" value="free" checked="checked" data-cost="0"> Доставка по <strong>г.'.$shipping_settings['shipping_sets'][$key]['city'].' - Бесплатно</strong> (сумма заказа превышает <strong>'.$shipping_settings['free_shipping_amount'].' руб.</strong>)</label>
                </div>';
            } else {
                $shipping_cost = $shipping_settings['shipping_sets'][$key]['amount'];
                $output .= '
                <div class="print-cart-item-field">
                    <label><input type="radio" id="t9z_shipping_1_city" name="t9z_shipping_1" value="city" checked="checked" data-cost="'.$shipping_settings['shipping_sets'][$key]['amount'].'">Доставка по <strong>г.'.$shipping_settings['shipping_sets'][$key]['city'].' - '.($shipping_settings['shipping_sets'][$key]['amount'] > 0 ? $shipping_settings['shipping_sets'][$key]['amount'].' руб.' : 'Бесплатно').'</strong></label>
                </div>';
            }
    
            $output .= '
            <div class="print-cart-item-field">
                <label><input type="radio" id="t9z_shipping_1_office" name="t9z_shipping_1" value="office" data-cost="0"> Получение в офисе - <strong>Бесплатно</strong></label> 
                <div class="print-cart-item-subfields" style="display: none;">';
                    
                    $office = explode('|', $shipping_settings['shipping_sets'][$key]['offices']);
                    for($i=0; $i < count($office); $i++) {
                        $output .= '<div class="print-cart-item-field"><label><input type="radio" name="t9z_shipping_2" value="'.$i.'">г.'.$shipping_settings['shipping_sets'][$key]['city'].', '.$office[$i].', <a href="/">на карте</a></label></div>';
                    }
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
    do_action('woocommerce_cart_calculate_fees');
    do_action('woocommerce_cart_total');
    
    ob_start();
    do_action( 'woocommerce_after_cart_table' );
    do_action( 'woocommerce_cart_collaterals' );
    do_action( 'woocommerce_after_cart' ); 
    $after_cart_html = ob_get_clean();
        
    return array(
        'shipping_method' => $output,
        'shipping_cost' => $shipping_cost,
        'bonus_amount' => $bonus_amount,
        'bonus_percent' => (int)$shipping_settings['bonus_rate'],
        'surcharge' => $surcharge,
        'natsenka_percent' => (int)$shipping_settings['natsenka_rate'],
        'total' => $woocommerce->cart->total,
        'after_cart_html' => $after_cart_html,
    );
}