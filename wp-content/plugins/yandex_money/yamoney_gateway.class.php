<?php
if(!class_exists('WC_Payment_Gateway')) return;
class WC_yam_Gateway extends WC_Payment_Gateway{
    protected $long_name;
    protected $payment_type;
    private $order;
    public function __construct(){
        $this -> has_fields = false;
        $this -> init_form_fields();
        $this -> init_settings();
        $this -> title = $this -> settings['title'];
        $this -> description = $this -> settings['description'];
        $this -> liveurl = '';
        $this -> msg['message'] = "";
        $this -> msg['class'] = "";

        if ( version_compare( WOOCOMMERCE_VERSION, '2.0.0', '>=' ) ) {
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( &$this, 'process_admin_options' ) );
        } else {
            add_action( 'woocommerce_update_options_payment_gateways', array( &$this, 'process_admin_options' ) );
        }
        add_action('woocommerce_receipt_'.$this->id, array(&$this, 'receipt_page'));
    }
    function init_form_fields(){

        $this -> form_fields = array(
            'enabled' => array(
                'title' => __('Включить/Выключить','yandex_money'),
                'type' => 'checkbox',
                'label' => $this ->long_name,
                'default' => 'no'),
            'title' => array(
                'title' => __('Заголовок','yandex_money'),
                'type'=> 'text',
                'description' => __('Название, которое пользователь видит во время оплаты','yandex_money'),
                'default' => $this ->method_title),
            'description' => array(
                'title' => __('Описание','yandex_money'),
                'type' => 'textarea',
                'description' => __('Описание, которое пользователь видит во время оплаты','yandex_money'),
                'default' => $this ->long_name)
        );
    }

    public function admin_options(){
        echo '<h3>'.$this ->long_name.'</h3>';
        echo '<h5>'.__('Для работы с модулем необходимо <a href="https://money.yandex.ru/joinups/">подключить магазин к Яндек.Кассе</a>. После подключения вы получите параметры для приема платежей (идентификатор магазина — shopId и номер витрины — scid).','yandex_money').'</h5>';
        echo '<table class="form-table">';
        // Generate the HTML For the settings form.
        $this -> generate_settings_html();
        echo '</table>';

    }

    /**
     *  There are no payment fields for payu, but we want to show the description if set.
     **/
    function payment_fields(){
        if($this -> description) echo wpautop(wptexturize($this -> description));
    }
    /**
     * Receipt Page
     **/
    function receipt_page($order){
        echo $this -> generate_payu_form($order);
    }
    protected function get_success_fail_url($name){
        switch (get_option($name)){
            case "wc_success":
                return $this->order->get_checkout_order_received_url();
                break;
            case "wc_checkout":
                return $this->order->get_view_order_url();
                break;
            case "wc_payment":
                return $this->order->get_checkout_payment_url();
                break;
            default:
                return get_page_link(get_option($name));
                break;
        }
    }
    protected function get_fail_url(){

    }
    /**
     * Generate payu button link
     **/
    public function generate_payu_form($order_id){

        global $woocommerce;
        $this->order = new WC_Order($order_id);
        $txnid = $order_id;
        $sendurl=get_option('ym_Demo')=='on'?'https://demomoney.yandex.ru/eshop.xml':'https://money.yandex.ru/eshop.xml';
        $result ='';
        $result .= '<form name=ShopForm method="POST" id="submit_'.$this->id.'_payment_form" action="'.$sendurl.'">';
        $result .= '<input type="hidden" name="firstname" value="'.$this->order -> billing_first_name.'">';
        $result .= '<input type="hidden" name="lastname" value="'.$this->order -> billing_last_name.'">';
        $result .= '<input type="hidden" name="scid" value="'.get_option('ym_Scid').'">';
        $result .= '<input type="hidden" name="ShopID" value="'.get_option('ym_ShopID').'"> ';
        $result .= '<input type="hidden" name="shopSuccessUrl" value="'.$this->get_success_fail_url('ym_success').'"> ';
        $result .= '<input type="hidden" name="shopFailUrl" value="'.$this->get_success_fail_url('ym_fail').'"> ';
        $result .= '<input type="hidden" name="CustomerNumber" value="'.$txnid.'" size="43">';
        if ($this->order ->billing_phone) $result .= '<input name="cps_phone" type="hidden" value="'.$this->order ->billing_phone.'">';
        if ($this->order ->billing_email) $result .= '<input name="cps_email" type="hidden" value="'.$this->order ->billing_email.'">';
        $result .= '<input type="hidden" name="Sum" value="'.number_format( $this->order->order_total, 2, '.', '' ).'" size="43">';
        $result .= '<input name="paymentType" value="'.$this->payment_type.'" type="hidden">';
        $result .= '<input name="cms_name" type="hidden" value="wp-woocommerce">';
        $result .= '<input type="submit" value="Оплатить">';
        $result .='<script type="text/javascript">';
        $result .='jQuery(function(){
		jQuery("body").block(
        {
            message: "Спасибо за заказ. Сейчас Вы будете перенаправлены на страницу оплаты.",
                overlayCSS:
        {
            background: "#fff",
                opacity: 0.6
		},
		css: {
			padding:        20,
				textAlign:      "center",
				color:          "#555",
				border:         "3px solid #aaa",
				backgroundColor:"#fff",
				cursor:         "wait",
				lineHeight:"32px"
		}
		});
		});
		';
        $result .='jQuery(document).ready(function ($){ jQuery("#submit_'.$this ->id.'_payment_form").submit(); });';
        $result .='</script></form>';
        $woocommerce->cart->empty_cart();
        return $result;
    }
    /**
     * Process the payment and return the result
     **/
    function process_payment($order_id){
        $order = new WC_Order($order_id);
        return array('result' => 'success', 'redirect' => $order->get_checkout_payment_url( true ));
    }

    function showMessage($content){
        return '<div class="box '.$this -> msg['class'].'-box">'.$this -> msg['message'].'</div>'.$content;
    }
    // get all pages
    function get_pages($title = false, $indent = true) {
        $wp_pages = get_pages('sort_column=menu_order');
        $page_list = array();
        if ($title) $page_list[] = $title;
        foreach ($wp_pages as $page) {
            $prefix = '';
            // show indented child pages?
            if ($indent) {
                $has_parent = $page->post_parent;
                while($has_parent) {
                    $prefix .=  ' - ';
                    $next_page = get_page($has_parent);
                    $has_parent = $next_page->post_parent;
                }
            }
            // add to page list array array
            $page_list[$page->ID] = $prefix . $page->post_title;
        }
        return $page_list;
    }
}
class WC_mpos_Gateway extends WC_yam_Gateway{
    public function __construct(){
        parent::__construct();
    }
    public function generate_payu_form($order_id){
        global $woocommerce;
        $order = new WC_Order($order_id);
        $txnid = $order_id;
        $result ='';
        $result .= '<form name=ShopForm method="POST" id="submit_'.$this->id.'_payment_form" action="'.get_page_link(get_option('ym_page_mpos')).'">';
        $result .= '<input type="hidden" name="CustomerNumber" value="'.$txnid.'" size="43">';
        $result .= '<input type="hidden" name="Sum" value="'.number_format( $order->order_total, 2, '.', '' ).'" size="43">';
        $result .= '<input name="paymentType" value="'.$this->payment_type.'" type="hidden">';
        $result .= '<input name="cms_name" type="hidden" value="wp-woocommerce">';
        $result .= '<input type="submit" value="Перейти к инcтрукции по оплате">';
        $result .='</form>';
        $woocommerce->cart->empty_cart();
        return $result;
    }
}
class WC_epl_Gateway extends WC_yam_Gateway{
    public function __construct(){
        parent::__construct();
    }
    public function admin_options(){
        echo '<h3>'.$this ->long_name.'</h3>';
        echo '<h5>'.__('<b>Внимание! Этот режим должен быть включен и на стороне сервиса Яндекс.Касса.</b>
                        <br>Чтобы активировать этот сценарий, напишите менеджеру Кассы на <a href="mailto:merchants@yamoney.ru">merchants@yamoney.ru</a> или позвоните по телефону 8 800 250-66-99.','yandex_money').'</h5>';
        echo '<table class="form-table">';
        // Generate the HTML For the settings form.
        $this -> generate_settings_html();
        echo '</table>';

    }
}
?>