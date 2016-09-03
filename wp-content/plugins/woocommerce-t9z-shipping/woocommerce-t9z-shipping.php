<?php

/*
 * Plugin Name: WooCommerce T9Z Shipping
 * Description: Shipping method plugin for T9Z
 * Version: 1.0.0
 * Author: KSK
 */

/**
 * Check if WooCommerce is active
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    function t9z_shipping_init() {
        if ( ! class_exists( 'WC_T9z_Shipping' ) ) {
            /**
             * Class WC_T9z_Shipping
             *
             * @package 
             */
            class WC_T9z_Shipping extends WC_Shipping_Method {
                /**
                 * Constructor for your shipping class
                 *
                 * @access public
                 * @return void
                 */
                public function __construct() {
                    $this->id = 't9z-shipping'; // Id for your shipping method. Should be uunique.
                    $this->plugin_id = 'woocommerce-t9z-shipping'; // Id for your shipping method. Should be uunique.
                    $this->plugin_slug = 'woocommerce_t9z_shipping';
                    $this->method_title = 'T9Z';  // Title shown in admin
                    $this->method_description = 'Доставка T9Z'; // Description shown in admin
                    $this->settings = array();

                    $this->init();
                }

                /**
                 * Init your settings
                 *
                 * @access public
                 * @return void
                 */
                function init() {
                    // Load the settings API
                    //$this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
                    $this->init_settings(); // This is part of the settings API. Loads settings you previously init.

                    // Define user set variables
                    $this->enabled = $this->settings['enabled'] ? 'yes' : 'no';
                    $this->title = $this->settings['title'];

                    // Save settings in admin if you have any defined
                    //add_action( 'woocommerce_update_options_shipping_' . $this->plugin_slug, array( $this, 'process_admin_options' ) );
                    //add_action( 'woocommerce_update_options_shipping_methods', array( &$this, 'process_admin_options' ) );
                    
                    if (is_admin()) {
                        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
                    }
                }
                
                public function init_settings() {
                    $_settings = get_option( $this->plugin_slug . '_settings', null );
                    $this->settings = (array) maybe_unserialize($_settings);
                    
                    if ( ! $this->settings || ! is_array( $this->settings ) ) {
                        $this->settings = array(
                            'enabled' => 1,
                            'title' => "Доставка T9Z",
                            'free_shipping_amount' => 5000,
                            'bonus_rate' => 5,
                            'natsenka_rate' => 30,
                            'order_min_amount' => 500,
                            'default_city' => 'Краснодар',
                            'shipping_sets' => array(
                                '1' => array(
                                    'city' => 'Ростов-на-Дону',
                                    'amount' => 600,
                                    'offices' => 'Офис 1 | Офис 2',
                                ),
                                '2' => array(
                                    'city' => 'Краснодар',
                                    'amount' => 400,
                                    'offices' => 'Офис 1',
                                ),
                                '3' => array(
                                    'city' => 'Ставрополь',
                                    'amount' => 0,
                                    'offices' => '',
                                ),
                            ),
                        );
                    }
                }
                
                public function process_admin_options() {
                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST[$this->plugin_slug . '_page']) && $_POST[$this->plugin_slug . '_page'] == 'settings') {
                        //if (check_admin_referer($this->plugin_slug.'_post')) {
                        //if (check_admin_referer()) {
                        $this->settings['enabled'] = isset( $_POST['enabled'] ) ? '1' : '0'; 
                        $this->settings['title'] = isset( $_POST['title'] ) ? $_POST['title'] : ''; 
                        $this->settings['free_shipping_amount'] = isset( $_POST['free_shipping_amount'] ) ? $_POST['free_shipping_amount'] : ''; 
                        $this->settings['bonus_rate'] = isset( $_POST['bonus_rate'] ) ? $_POST['bonus_rate'] : ''; 
                        $this->settings['natsenka_rate'] = isset( $_POST['natsenka_rate'] ) ? $_POST['natsenka_rate'] : '';
                        $this->settings['order_min_amount'] = isset( $_POST['order_min_amount'] ) ? $_POST['order_min_amount'] : '';
                        $this->settings['default_city'] = isset( $_POST['default_city'] ) ? $_POST['default_city'] : '';
                        
                        foreach ($this->settings['shipping_sets'] AS $id => $data) {
                            $this->settings['shipping_sets'][$id] = array(
                                'city' => isset( $_POST[$this->plugin_slug][$id][city] ) ? $_POST[$this->plugin_slug][$id][city] : '',
                                'amount' => isset( $_POST[$this->plugin_slug][$id][amount] ) ? $_POST[$this->plugin_slug][$id][amount] : 0,
                                'offices' => isset( $_POST[$this->plugin_slug][$id][offices] ) ? $_POST[$this->plugin_slug][$id][offices] : '',
                            );
                        }

                        update_option( $this->plugin_slug . '_settings', apply_filters( 'woocommerce_settings_api_sanitized_fields_' . $this->plugin_slug, $this->settings) );
                        //$this->init_settings();
                        return true;
                        //} else {
                        //    die('Security error');
                        //}
                    }
                }
                
                /*
                 * Registers the plugin scripts and styles for the administration panel
                 *
                 * @return void
                 */
                public function admin_scripts($hook)
                {
                    global $wp_scripts;

                    // For admin page only
                    //if ($hook == 'woocommerce_page_'.$this->plugin_slug) {
                    if ($hook == 'woocommerce_page_wc-settings') {
                        //$a = plugins_url($this->plugin_id.'/assets/js/adminset.js');
                        wp_register_script($hook.'_admin', plugins_url($this->plugin_id.'/assets/js/adminset.js'), array('jquery', 'jquery-ui-sortable'));

                        wp_enqueue_script($hook.'_admin');
                    }
                }
                
                function admin_options() {
                    if ( ! empty( $_POST ) ) {
                        $this->process_admin_options();
                    }
                    /*value="<?php echo $this->settings['enabled']; ?>"  */
                    wp_nonce_field($this->plugin_slug.'_post'); 
?>
<input type="hidden" name="<?php echo $this->plugin_slug; ?>_page" value="settings" />
<div>
    <h3>Опции доставки T9Z</h3>
    <div>
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="enabled">Включить/Выключить</label>
                    </th>
                    <td class="forminp">
                        <fieldset>
                            <legend class="screen-reader-text"><span>Включить/Выключить</span></legend>
                            <label for="enabled">
                            <input  class="" type="checkbox" name="enabled" id="<?php echo $this->plugin_slug; ?>_enabled" style="" <?php echo $this->settings['enabled'] == '1' ? 'checked="checked"' : ''; ?>/> Активировать доставку T9Z</label><br/>
                        </fieldset>
                    </td>
                </tr>
                <tr valign="top">
			<th scope="row" class="titledesc">
                            <label for="title">Заголовок метода</label>
                            <span class="woocommerce-help-tip" data-tip="Заголовок, который видит пользователь в процессе оформления заказа."></span>
                        </th>
			<td class="forminp">
                            <fieldset>
                                <legend class="screen-reader-text"><span>Заголовок метода</span></legend>
                                <input class="input-text regular-input " type="text" name="title" id="<?php echo $this->plugin_slug; ?>_title" style="" value="<?php echo $this->settings['title']; ?>" placeholder=""/>
                            </fieldset>
			</td>
		</tr>                 
                <tr valign="top">
			<th scope="row" class="titledesc">
                            <label for="free_shipping_amount">Сумма заказа для бесплатной доставки</label>
                            <span class="woocommerce-help-tip" data-tip="Минимальна сумма заказа для активации бесплатной доставки."></span>
                        </th>
			<td class="forminp">
                            <fieldset>
                                <legend class="screen-reader-text"><span>Сумма заказа для бесплатной доставки</span></legend>
                                <input class="input-text regular-input " type="text" name="free_shipping_amount" id="<?php echo $this->plugin_slug; ?>_free_shipping_amount" style="" value="<?php echo $this->settings['free_shipping_amount']; ?>" placeholder=""/>
                            </fieldset>
			</td>
		</tr>                 
                <tr valign="top">
			<th scope="row" class="titledesc">
                            <label for="bonus_rate">% от суммы заказа для расчета бонусов</label>
                            <span class="woocommerce-help-tip" data-tip="Процент от суммы заказа для расчета бонусов в процессе оформления заказа."></span>
                        </th>
			<td class="forminp">
                            <fieldset>
                                <legend class="screen-reader-text"><span>% от суммы заказа для расчета бонусов</span></legend>
                                <input class="input-text regular-input " type="text" name="bonus_rate" id="<?php echo $this->plugin_slug; ?>_bonus_rate" style="" value="<?php echo $this->settings['bonus_rate']; ?>" placeholder=""/>
                            </fieldset>
			</td>
		</tr>                 
                <tr valign="top">
			<th scope="row" class="titledesc">
                            <label for="natsenka_rate">Наценка за срочное выполнение заказа</label>
                            <span class="woocommerce-help-tip" data-tip="Процент от суммы заказа для расчета наценки за срочное выполнение заказа."></span>
                        </th>
			<td class="forminp">
                            <fieldset>
                                <legend class="screen-reader-text"><span>Наценка за срочное выполнение заказа</span></legend>
                                <input class="input-text regular-input " type="text" name="natsenka_rate" id="<?php echo $this->plugin_slug; ?>_natsenka_rate" style="" value="<?php echo $this->settings['natsenka_rate']; ?>" placeholder=""/>
                            </fieldset>
			</td>
		</tr>                 
                <tr valign="top">
			<th scope="row" class="titledesc">
                            <label for="order_min_amount">Минимальная сумма заказа</label>
                            <span class="woocommerce-help-tip" data-tip="Минимальная сумма заказа, при достижении которой доступно оформление заказа."></span>
                        </th>
			<td class="forminp">
                            <fieldset>
                                <legend class="screen-reader-text"><span>Минимальная сумма заказа</span></legend>
                                <input class="input-text regular-input " type="text" name="order_min_amount" id="<?php echo $this->plugin_slug; ?>_order_min_amount" style="" value="<?php echo $this->settings['order_min_amount']; ?>" placeholder=""/>
                            </fieldset>
			</td>
		</tr>                 
                <tr valign="top">
			<th scope="row" class="titledesc">
                            <label for="default_city">Город по умолчанию</label>
                            <span class="woocommerce-help-tip" data-tip="Город по умолчанию в списке городов в шапке сайта."></span>
                        </th>
			<td class="forminp">
                            <fieldset>
                                <legend class="screen-reader-text"><span>% от суммы заказа для расчета бонусов</span></legend>
                                <input class="input-text regular-input " type="text" name="default_city" id="<?php echo $this->plugin_slug; ?>_default_city" style="" value="<?php echo $this->settings['default_city']; ?>" placeholder=""/>
                            </fieldset>
			</td>
		</tr>                 
            </tbody>
        </table>
    </div>
    
    <div>
        <div id="woocommerce-t9z-shipping-container">
            <a href="#" class="button button-green right" id="woocommerce-t9z-shipping-add-set" title="Добавить блок доставки для нового города">Добавить блок доставки для нового города</a>
            <div class="clear"></div>
            <div id="woocommerce-t9z-shipping-boxes">
                <?php foreach ($this->settings['shipping_sets'] AS $id => $data): ?>

                <div id="woocommerce-t9z-shipping-box-<?php echo $id; ?>" class="wpf-umf-upload-box" data-id="<?php echo $id; ?>">
                    <a class="wpf-umf-upload-box-delete button button-red">Удалить</a>
                    <a class="wpf-umf-upload-box-advanced button button-secondary" style="width: 120px;"><span class="dashicons dashicons-arrow-down"></span>Настройки</a>

                    <div class="wpf-umf-upload-row">
                        <label class="main-label" for="<?php echo $this->plugin_slug; ?>_<?php echo $id; ?>_title">Город доставки</label>
                        <div class="wpf-umf-upload-field">
                            <input id="<?php echo $this->plugin_slug; ?>_<?php echo $id; ?>_title" name="<?php echo $this->plugin_slug; ?>[<?php echo $id; ?>][city]" type="text" class="regular-input" value="<?php echo $data['city']; ?>" required />
                        </div>
                        <div class="clear"></div>
                    </div>

                    <div class="wpf-umf-upload-box-collapse hidden">
                        <div class="wpf-umf-upload-row">
                            <label class="main-label" for="<?php echo $this->plugin_slug; ?>_<?php echo $id; ?>_amount">Стоимость доставки</label>
                            <div class="wpf-umf-upload-field">
                                <input name="<?php echo $this->plugin_slug; ?>[<?php echo $id; ?>][amount]" id="<?php echo $this->plugin_slug; ?>_<?php echo $id; ?>_amount" type="number" value="<?php echo $data['amount']; ?>" class="small-text">
                            </div>
                            <div class="clear"></div>
                        </div>
                        
                        <div class="wpf-umf-upload-row">
                            <label class="main-label" for="<?php echo $this->plugin_slug; ?>_<?php echo $id; ?>_offices">Список офисов</label>
                            <div class="wpf-umf-upload-field">
                                <textarea id="<?php echo $this->plugin_slug; ?>_<?php echo $id; ?>_offices" name="<?php echo $this->plugin_slug; ?>[<?php echo $id; ?>][offices]" class="regular-input" rows="3"><?php echo $data['offices']; ?></textarea>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</div>
                    <?php
                }

                /**
                 * calculate_shipping function.
                 *
                 * @access public
                 * @param mixed $package
                 * @return void
                 */
                public function calculate_shipping( $package ) {
                    $shipping_cost = '0.00';
                    $total = WC()->cart->subtotal;
                    if ($total < (int)$shipping_settings['free_shipping_amount']) {
                        $city = isset($_SESSION['shipping_city']) ? $_SESSION['shipping_city'] : (isset($_POST['shipping_city']) ? $_POST['shipping_city'] : '');
                        $shipping_settings = maybe_unserialize(get_option('woocommerce_t9z_shipping_settings', null));

                        if (($city != '') && (count($shipping_settings) > 0) && ($shipping_settings['enabled'] == 1) && (count($shipping_settings['shipping_sets']) > 0)) {
                            foreach ($shipping_settings['shipping_sets'] as $key => $value) {
                                if ($value['city'] == $city) {
                                    $shipping_cost = number_format($value['amount'], 2);
                                    break;
                                }
                            }
                        }
                    }
                    
                    $rate = array(
                        'id' => $this->plugin_id,
                        'label' => $this->title,
                        'cost' => '12.55',
                        'calc_tax' => 'per_item'
                    );

                    // Register the rate
                    $this->add_rate( $rate );
                }
            }
        }
    }

    add_action( 'woocommerce_shipping_init', 't9z_shipping_init' );

    function add_t9z_shipping( $methods ) {
        $methods[] = 'WC_T9z_Shipping';
        return $methods;
    }

    add_filter( 'woocommerce_shipping_methods', 'add_t9z_shipping' );
}