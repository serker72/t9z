<?php
/*
Plugin Name: WooCommerce - Bytehand SMS Notifications
Version: 2.7.6.1
Description: Add to WooCommerce SMS notifications to your clients for order status changes.
Author URI: http://bytehand.com/
Author: Bytehand
Requires at least: 3.8
Tested up to: 4.3.1

Text Domain: bh_sms
Domain Path: /i18n/languages

@package WooCommerce - BH SMS Notifications
@category Core
@author Bytehand
*/

//Igual no deberías poder abrirme
if ( !defined( 'ABSPATH' ) ) {
    exit();
}

//Definimos constantes
define( 'DIRECCION_bh_sms', plugin_basename( __FILE__ ) );

//Definimos las variables
$bh_sms = array( 	
	'plugin' 		=> 'WooCommerce - BH SMS Notifications', 
	'plugin_uri' 	=> 'woocommerce-bh-sms-notifications',
	'ajustes' 		=> 'admin.php?page=bh_sms'
);

//Carga el idioma
load_plugin_textdomain( 'bh_sms', null, dirname( DIRECCION_bh_sms ) . '/i18n/languages' );

//Carga la configuración del plugin
$configuracion = get_option( 'bh_sms_settings' );

//Enlaces adicionales personalizados
function bh_sms_enlaces( $enlaces, $archivo ) {
	global $bh_sms;

	if ( $archivo == DIRECCION_bh_sms ) {
		$enlaces[] = '<a href="'. $bh_sms['plugin_url'] . '" target="_blank" title="' . $bh_sms['plugin'] . '"><strong class="bytehand">Bytehand</strong></a>';
		$enlaces[] = '<a href="mailto:support@bytehand.com" title="' . __( 'Обратная связь ', 'bh_sms' ) . 'e-mail"><span class="genericon genericon-mail"></span></a>';
		$enlaces[] = bh_sms_plugin( $bh_sms['plugin_uri'] );
	}

	return $enlaces;
}
add_filter( 'plugin_row_meta', 'bh_sms_enlaces', 10, 2 );

//Añade el botón de configuración
function bh_sms_enlace_de_ajustes( $enlaces ) { 
	global $bh_sms;

	$enlaces_de_ajustes = array( 
		'<a href="' . $bh_sms['ajustes'] . '" title="' . __( 'Settings of ', 'bh_sms' ) . $bh_sms['plugin'] .'">' . __( 'Settings', 'bh_sms' ) . '</a>'
	);
	foreach( $enlaces_de_ajustes as $enlace_de_ajustes )	{
		array_unshift( $enlaces, $enlace_de_ajustes );
	}

	return $enlaces; 
}
$plugin = DIRECCION_bh_sms; 
add_filter( "plugin_action_links_$plugin", 'bh_sms_enlace_de_ajustes' );

//¿Está activo WooCommerce?
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	//Comprobamos si está instalado y activo WPML
	$wpml_activo = function_exists( 'icl_object_id' );
	
	//Registramos los textos en WPML
	if ( $wpml_activo && function_exists( 'icl_register_string' ) ) {
		icl_register_string( 'bh_sms', 'mensaje_pedido', $configuracion['mensaje_pedido'] );
		icl_register_string( 'bh_sms', 'mensaje_recibido', $configuracion['mensaje_recibido'] );
		icl_register_string( 'bh_sms', 'mensaje_procesando', $configuracion['mensaje_procesando'] );
		icl_register_string( 'bh_sms', 'mensaje_completado', $configuracion['mensaje_completado'] );
		icl_register_string( 'bh_sms', 'mensaje_nota', $configuracion['mensaje_nota'] );
	} else if ( $wpml_activo ) {
		do_action( 'wpml_register_single_string', 'bh_sms', 'mensaje_pedido', $configuracion['mensaje_pedido'] );
		do_action( 'wpml_register_single_string', 'bh_sms', 'mensaje_recibido', $configuracion['mensaje_recibido'] );
		do_action( 'wpml_register_single_string', 'bh_sms', 'mensaje_procesando', $configuracion['mensaje_procesando'] );
		do_action( 'wpml_register_single_string', 'bh_sms', 'mensaje_completado', $configuracion['mensaje_completado'] );
		do_action( 'wpml_register_single_string', 'bh_sms', 'mensaje_nota', $configuracion['mensaje_nota'] );
	}
	 $statuses = array( 
						'pending' => 'В ожидании',
						'failed' => 'Ошибка',
						'on-hold' => 'На удержании',
						'processing' => 'Обработка',
						'completed' => 'Завершено',
						'refunded' => 'Возврат',
						'cancelled' => 'Отменено',
					);
	 foreach ($statuses as $key => $value) {
			
				
				if ( $wpml_activo && function_exists( 'icl_register_string' ) ) {
					icl_register_string( 'bh_sms', 'status_' . $key, $configuracion['status_' . $key] );
				} else if ( $wpml_activo ) {
					do_action( 'wpml_register_single_string', 'bh_sms', 'status_' . $key, $configuracion['status_' . $key] );
				}


	 }
	
	//Cargamos los proveedores SMS
	include( 'includes/admin/proveedores.php' );

	//Pinta el formulario de configuración
	function bh_sms_tab() {
		include( 'includes/formulario.php' );
	}

	//Añade en el menú a WooCommerce
	function bh_sms_admin_menu() {
		add_submenu_page( 'woocommerce', __( 'BH SMS Notifications', 'bh_sms' ),  __( 'SMS уведомления', 'bh_sms' ) , 'manage_woocommerce', 'bh_sms', 'bh_sms_tab' );
	}
	add_action( 'admin_menu', 'bh_sms_admin_menu', 15 );

	//Carga los scripts y CSS de WooCommerce
	function bh_sms_screen_id( $woocommerce_screen_ids ) {
		global $woocommerce;

		$woocommerce_screen_ids[] = 'woocommerce_page_bh_sms';

		return $woocommerce_screen_ids;
	}
	add_filter( 'woocommerce_screen_ids', 'bh_sms_screen_id' );

	//Registra las opciones
	function bh_sms_registra_opciones() {
		global $configuracion;
	
		register_setting( 'bh_sms_settings_group', 'bh_sms_settings' );

		if ( ( class_exists( 'WC_Custom_Status' ) || class_exists( 'AppZab_Woo_Advance_Order_Status' ) || isset( $GLOBALS['advorder_lite_orderstatus'] ) ) && isset( $configuracion['estados_personalizados'] ) ) {
			foreach ( $configuracion['estados_personalizados'] as $estado ) {
				add_action( "woocommerce_order_status_{$estado}", 'bh_sms_procesa_estados', 10 ); //Funciona cuando se ejecuta WooCommerce Custom Order Statuses & Actions
			}
		}
	}
	add_action( 'admin_init', 'bh_sms_registra_opciones' );

	//Procesa el SMS
	function bh_sms_procesa_estados( $pedido, $notificacion = false ) {
		global $woocommerce, $configuracion, $wpml_activo;

		$pedido = new WC_Order( $pedido );
		$status = $pedido->status;
		//var_dump($status,  $configuracion['status_' . $status], $mensaje); die();
		
		
	
		$telefono = bh_sms_procesa_el_telefono( $pedido, $pedido->billing_phone, $configuracion['servicio'] );
		$internacional = false;
		$telefono_propietario = bh_sms_procesa_el_telefono( $pedido, $configuracion['telefono'], $configuracion['servicio'], true );
		
		//WPML
		if ( function_exists( 'icl_register_string' ) || !$wpml_activo ) { //Versión anterior a la 3.2
			$mensaje_pedido		= ( $wpml_activo ) ? icl_translate( 'bh_sms', 'mensaje_pedido', $configuracion['mensaje_pedido'] ) : $configuracion['mensaje_pedido'];
			$mensaje_recibido	= ( $wpml_activo ) ? icl_translate( 'bh_sms', 'mensaje_recibido', $configuracion['mensaje_recibido'] ) : $configuracion['mensaje_recibido'];
			$mensaje_procesando	= ( $wpml_activo ) ? icl_translate( 'bh_sms', 'mensaje_procesando', $configuracion['mensaje_procesando'] ) : $configuracion['mensaje_procesando'];
			$mensaje_completado	= ( $wpml_activo ) ? icl_translate( 'bh_sms', 'mensaje_completado', $configuracion['mensaje_completado'] ) : $configuracion['mensaje_completado'];
		} else if ( $wpml_activo ) { //Versión 3.2 o superior
			$mensaje_pedido		= apply_filters( 'wpml_translate_single_string', $configuracion['mensaje_pedido'], 'bh_sms', 'mensaje_pedido' );
			$mensaje_recibido	= apply_filters( 'wpml_translate_single_string', $configuracion['mensaje_recibido'], 'bh_sms', 'mensaje_recibido' );
			$mensaje_procesando	= apply_filters( 'wpml_translate_single_string', $configuracion['mensaje_procesando'], 'bh_sms', 'mensaje_procesando' );
			$mensaje_completado	= apply_filters( 'wpml_translate_single_string', $configuracion['mensaje_completado'], 'bh_sms', 'mensaje_completado' );
		}
		

		if ( $status == 'on-hold' ) {
			if ( isset( $configuracion['notificacion'] ) && $configuracion['notificacion'] == 1 ) {
				bh_sms_envia_sms( $configuracion, $telefono_propietario, bh_sms_procesa_variables( $mensaje_pedido, $pedido, $configuracion['variables'] ) ); //Mensaje para el propietario
			}
		}
		$mensaje = bh_sms_procesa_variables( $configuracion['status_' . $status], $pedido, $configuracion['variables'] );
		
		if ( ( !$internacional || ( isset( $configuracion['internacional'] ) && $configuracion['internacional'] == 1 ) ) && !$notificacion ) {
			if ($mensaje) {
				bh_sms_envia_sms( $configuracion, $telefono, $mensaje );
			}
		}
	}
	$statuses = array( 
						'pending' => 'В ожидании',
						'failed' => 'Ошибка',
						'on-hold' => 'На удержании',
						'processing' => 'Обработка',
						'completed' => 'Завершено',
						'refunded' => 'Возврат',
						'cancelled' => 'Отменено',
					);
	//add_action( 'woocommerce_order_status_pending_to_on-hold_notification', 'bh_sms_procesa_estados', 10 );//when receiverd
	foreach ($statuses as $key => $value) {
		add_action( 'woocommerce_order_status_' . $key, 'bh_sms_procesa_estados', 10 );
	}

	function bh_sms_notificacion( $pedido ) {
		bh_sms_procesa_estados( $pedido, true );
	}
	add_action( 'woocommerce_order_status_pending_to_processing_notification', 'bh_sms_notificacion', 10 );//Funciona cuando el pedido es marcado directamente como procesando

	//Envía las notas de cliente por SMS
	function bh_sms_procesa_notas( $datos ) {
		global $woocommerce, $configuracion, $wpml_activo;
	
		$pedido = new WC_Order( $datos['order_id'] );
	
		$telefono = bh_sms_procesa_el_telefono( $pedido, $pedido->billing_phone, $configuracion['servicio'] );
		$internacional = false;
		
		if ( !$internacional || ( isset( $configuracion['internacional'] ) && $configuracion['internacional'] == 1 ) ) {
			//WPML
			if ( function_exists( 'icl_register_string' ) || !$wpml_activo ) { //Versión anterior a la 3.2
				$mensaje_nota		= ( $wpml_activo ) ? icl_translate( 'bh_sms', 'mensaje_nota', $configuracion['mensaje_nota'] ) : $configuracion['mensaje_nota'];
			} else if ( $wpml_activo ) { //Versión 3.2 o superior
				$mensaje_nota		= apply_filters( 'wpml_translate_single_string', $configuracion['mensaje_nota'], 'bh_sms', 'mensaje_nota' );
			}

			bh_sms_envia_sms( $configuracion, $telefono, bh_sms_procesa_variables( $mensaje_nota, $pedido, $configuracion['variables'], wptexturize( $datos['customer_note'] ) ) );
		}
	}
	add_action( 'woocommerce_new_customer_note', 'bh_sms_procesa_notas', 10 );

	//Normalizamos el texto
	function bh_sms_normaliza_mensaje( $mensaje ) {
		$reemplazo = array( 
			'Š'			=> 'S', 
			'š'			=> 's', 
			'Đ'			=> 'Dj', 
			'đ'			=> 'dj', 
			'Ž'			=> 'Z', 
			'ž'			=> 'z', 
			'Č'			=> 'C', 
			'č'			=> 'c', 
			'Ć'			=> 'C', 
			'ć'			=> 'c', 
			'À'			=> 'A', 
			'Á'			=> 'A', 
			'Â'			=> 'A', 
			'Ã'			=> 'A', 
			'Ä'			=> 'A', 
			'Å'			=> 'A', 
			'Æ'			=> 'A', 
			'Ç'			=> 'C', 
			'È'			=> 'E', 
			'É'			=> 'E', 
			'Ê'			=> 'E', 
			'Ë'			=> 'E', 
			'Ì'			=> 'I', 
			'Í'			=> 'I', 
			'Î'			=> 'I', 
			'Ï'			=> 'I', 
			'Ñ'			=> 'N', 
			'Ò'			=> 'O', 
			'Ó'			=> 'O', 
			'Ô'			=> 'O', 
			'Õ'			=> 'O', 
			'Ö'			=> 'O', 
			'Ø'			=> 'O', 
			'Ù'			=> 'U', 
			'Ú'			=> 'U', 
			'Û'			=> 'U', 
			'Ü'			=> 'U', 
			'Ý'			=> 'Y', 
			'Þ'			=> 'B', 
			'ß'			=> 'Ss', 
			'à'			=> 'a', 
			'á'			=> 'a', 
			'â'			=> 'a', 
			'ã'			=> 'a', 
			'ä'			=> 'a', 
			'å'			=> 'a', 
			'æ'			=> 'a', 
			'ç'			=> 'c', 
			'è'			=> 'e', 
			'é'			=> 'e', 
			'ê'			=> 'e',  
			'ë'			=> 'e', 
			'ì'			=> 'i', 
			'í'			=> 'i', 
			'î'			=> 'i', 
			'ï'			=> 'i', 
			'ð'			=> 'o', 
			'ñ'			=> 'n', 
			'ò'			=> 'o', 
			'ó'			=> 'o', 
			'ô'			=> 'o', 
			'õ'			=> 'o', 
			'ö'			=> 'o', 
			'ø'			=> 'o', 
			'ù'			=> 'u', 
			'ú'			=> 'u', 
			'û'			=> 'u', 
			'ý'			=> 'y',  
			'ý'			=> 'y', 
			'þ'			=> 'b', 
			'ÿ'			=> 'y', 
			'Ŕ'			=> 'R', 
			'ŕ'			=> 'r', 
			"`"			=> "'", 
			"´"			=> "'", 
			"„"			=> ",", 
			"`"			=> "'",
			"´"			=> "'", 
			"“"			=> "\"", 
			"”"			=> "\"", 
			"´"			=> "'", 
			"&acirc;€™"	=> "'", 
			"{"			=> "", 
			"~"			=> "", 
			"–"			=> "-", 
			"’"			=> "'", 
			"!"			=> ".", 
			"¡"			=> "", 
			"?"			=> ".", 
			"¿"			=> "" 
		);
	 
		$mensaje = str_replace( array_keys( $reemplazo ), array_values( $reemplazo ), htmlentities( $mensaje, ENT_QUOTES, "UTF-8" ) );
	 
		return $mensaje;
	}

	//Codifica el mensaje
	function bh_sms_codifica_el_mensaje( $mensaje ) {
		return urlencode( html_entity_decode( $mensaje, ENT_QUOTES, "UTF-8" ) );
	}
	
	//Procesa el teléfono y le añade, si lo necesita, el prefijo
	function bh_sms_procesa_el_telefono( $pedido, $telefono, $servicio, $propietario = false ) {
		global $woocommerce;
		
		$prefijo = bh_sms_prefijo( $servicio );
		
		$telefono = str_replace( array( '+','-' ), '', filter_var( $telefono, FILTER_SANITIZE_NUMBER_INT ) );
		if ( !$propietario && $pedido->billing_country && ( $woocommerce->countries->get_base_country() != $pedido->billing_country || $prefijo ) ) {
			$prefijo_internacional = dame_prefijo_pais( $pedido->billing_country );
		} else if ( $propietario && $prefijo ) {
			$prefijo_internacional = dame_prefijo_pais( $woocommerce->countries->get_base_country() );
		}
	
		preg_match( "/(\d{1,4})[0-9.\- ]+/", $telefono, $prefijo );
		if ( isset( $prefijo_internacional ) ) {
			if ( strpos( $prefijo[1], $prefijo_internacional ) === false ) {
				//$telefono = $prefijo_internacional . $telefono;
			}
		}
		
	
		return $telefono;
	}
	
	//Procesa las variables
	function bh_sms_procesa_variables( $mensaje, $pedido, $variables, $nota = '' ) {
		if (!$mensaje) {
			return false;
		}
		$bh_sms = array( 
			"id", 
			"status", 
			"prices_include_tax", 
			"tax_display_cart", 
			"display_totals_ex_tax", 
			"display_cart_ex_tax", 
			"order_date", 
			"modified_date", 
			"customer_message", 
			"customer_note", 
			"post_status", 
			"shop_name", 
			"note", 
			"order_product" 
		);
		$bh_sms_variables = array( //Hay que añadirles un guión
			"order_key", 
			"billing_first_name", 
			"billing_last_name", 
			"billing_company", 
			"billing_address_1", 
			"billing_address_2", 
			"billing_city", 
			"billing_postcode", 
			"billing_country", 
			"billing_state", 
			"billing_email", 
			"billing_phone", 
			"shipping_first_name", 
			"shipping_last_name", 
			"shipping_company", 
			"shipping_address_1", 
			"shipping_address_2", 
			"shipping_city", 
			"shipping_postcode", 
			"shipping_country", 
			"shipping_state", 
			"shipping_method", 
			"shipping_method_title", 
			"payment_method", 
			"payment_method_title", 
			"order_discount", 
			"cart_discount", 
			"order_tax", 
			"order_shipping", 
			"order_shipping_tax", 
			"order_total" 
		);
		$variables_personalizadas = explode( "\n", str_replace( array( 
			"\r\n", 
			"\r" 
		), "\n", $variables ) );
	
		$variables_de_pedido = get_post_custom( $pedido->id ); //WooCommerce 2.1
	
		preg_match_all( "/%(.*?)%/", $mensaje, $busqueda );
		foreach ( $busqueda[1] as $variable ) { 
			$variable = strtolower( $variable );
	
			if ( !in_array( $variable, $bh_sms ) && !in_array( $variable, $bh_sms_variables ) && !in_array( $variable, $variables_personalizadas ) ) {
				continue;
			}
	
			$especiales = array(  //Variables especiales (no éstandar y no personalizadas)
				"order_date", 
				"modified_date", 
				"shop_name", 
				"note", 
				"id", 
				"order_product" 
			);
			if ( !in_array( $variable, $especiales ) ) {
				if ( in_array( $variable, $bh_sms ) ) {
					$mensaje = str_replace( "%" . $variable . "%", $pedido->$variable, $mensaje ); //Variables estándar - Objeto
				} else if ( in_array( $variable, $bh_sms_variables ) ) {
					$mensaje = str_replace( "%" . $variable . "%", $variables_de_pedido["_" . $variable][0], $mensaje ); //Variables estándar - Array
				} else if ( isset( $variables_de_pedido[$variable] ) || in_array( $variable, $variables_personalizadas ) ) {
					$mensaje = str_replace( "%" . $variable . "%", $variables_de_pedido[$variable][0], $mensaje ); //Variables de pedido y personalizadas
				}
			} else if ( $variable == "order_date" || $variable == "modified_date" ) {
				$mensaje = str_replace( "%" . $variable . "%", date_i18n( woocommerce_date_format(), strtotime( $pedido->$variable ) ), $mensaje );
			} else if ( $variable == "shop_name" ) {
				$mensaje = str_replace( "%" . $variable . "%", get_bloginfo( 'name' ), $mensaje );
			} else if ( $variable == "note" ) {
				$mensaje = str_replace( "%" . $variable . "%", $nota, $mensaje );
			} else if ( $variable == "id" ) {
				$mensaje = str_replace( "%" . $variable . "%", $pedido->get_order_number(), $mensaje );
			} else if ( $variable == "order_product" ) {
				$productos = $pedido->get_items();
				$nombre = $productos[key( $productos )]['name'];
				if ( strlen( $nombre ) > 10 ) {
					$nombre = substr( $nombre, 0, 10 ) . "...";
				}
				if ( count( $productos ) > 1 ) {
					$nombre .= " (+" .  ( count( $productos ) - 1 ) .")";
				}
				$mensaje = str_replace( "%" . $variable . "%", $nombre, $mensaje );
			}
		}
		
		$mensaje = apply_filters( 'bh_sms_message' , $mensaje , $pedido->id );
		
		return $mensaje;
	}
	
	//Devuelve el código de prefijo del país
	function dame_prefijo_pais( $pais = '' ) {
		$paises = array( 
			'AC' => '247', 
			'AD' => '376', 
			'AE' => '971', 
			'AF' => '93', 
			'AG' => '1268', 
			'AI' => '1264', 
			'AL' => '355', 
			'AM' => '374', 
			'AO' => '244', 
			'AQ' => '672', 
			'AR' => '54', 
			'AS' => '1684', 
			'AT' => '43', 
			'AU' => '61', 
			'AW' => '297', 
			'AX' => '358', 
			'AZ' => '994', 
			'BA' => '387', 
			'BB' => '1246', 
			'BD' => '880', 
			'BE' => '32', 
			'BF' => '226', 
			'BG' => '359', 
			'BH' => '973', 
			'BI' => '257', 
			'BJ' => '229', 
			'BL' => '590', 
			'BM' => '1441', 
			'BN' => '673', 
			'BO' => '591', 
			'BQ' => '599', 
			'BR' => '55', 
			'BS' => '1242', 
			'BT' => '975', 
			'BW' => '267', 
			'BY' => '375', 
			'BZ' => '501', 
			'CA' => '1', 
			'CC' => '61', 
			'CD' => '243', 
			'CF' => '236', 
			'CG' => '242', 
			'CH' => '41', 
			'CI' => '225', 
			'CK' => '682', 
			'CL' => '56', 
			'CM' => '237', 
			'CN' => '86', 
			'CO' => '57', 
			'CR' => '506', 
			'CU' => '53', 
			'CV' => '238', 
			'CW' => '599', 
			'CX' => '61', 
			'CY' => '357', 
			'CZ' => '420', 
			'DE' => '49', 
			'DJ' => '253', 
			'DK' => '45', 
			'DM' => '1767', 
			'DO' => '1809', 
			'DO' => '1829', 
			'DO' => '1849', 
			'DZ' => '213', 
			'EC' => '593', 
			'EE' => '372', 
			'EG' => '20', 
			'EH' => '212', 
			'ER' => '291', 
			'ES' => '34', 
			'ET' => '251', 
			'EU' => '388', 
			'FI' => '358', 
			'FJ' => '679', 
			'FK' => '500', 
			'FM' => '691', 
			'FO' => '298', 
			'FR' => '33', 
			'GA' => '241', 
			'GB' => '44', 
			'GD' => '1473', 
			'GE' => '995', 
			'GF' => '594', 
			'GG' => '44', 
			'GH' => '233', 
			'GI' => '350', 
			'GL' => '299', 
			'GM' => '220', 
			'GN' => '224', 
			'GP' => '590', 
			'GQ' => '240', 
			'GR' => '30', 
			'GT' => '502', 
			'GU' => '1671', 
			'GW' => '245', 
			'GY' => '592', 
			'HK' => '852', 
			'HN' => '504', 
			'HR' => '385', 
			'HT' => '509', 
			'HU' => '36', 
			'ID' => '62', 
			'IE' => '353', 
			'IL' => '972', 
			'IM' => '44', 
			'IN' => '91', 
			'IO' => '246', 
			'IQ' => '964', 
			'IR' => '98', 
			'IS' => '354', 
			'IT' => '39', 
			'JE' => '44', 
			'JM' => '1876', 
			'JO' => '962', 
			'JP' => '81', 
			'KE' => '254', 
			'KG' => '996', 
			'KH' => '855', 
			'KI' => '686', 
			'KM' => '269', 
			'KN' => '1869', 
			'KP' => '850', 
			'KR' => '82', 
			'KW' => '965', 
			'KY' => '1345', 
			'KZ' => '7', 
			'LA' => '856', 
			'LB' => '961', 
			'LC' => '1758', 
			'LI' => '423', 
			'LK' => '94', 
			'LR' => '231', 
			'LS' => '266', 
			'LT' => '370', 
			'LU' => '352', 
			'LV' => '371', 
			'LY' => '218', 
			'MA' => '212', 
			'MC' => '377', 
			'MD' => '373', 
			'ME' => '382', 
			'MF' => '590', 
			'MG' => '261', 
			'MH' => '692', 
			'MK' => '389', 
			'ML' => '223', 
			'MM' => '95', 
			'MN' => '976', 
			'MO' => '853', 
			'MP' => '1670', 
			'MQ' => '596', 
			'MR' => '222', 
			'MS' => '1664', 
			'MT' => '356', 
			'MU' => '230', 
			'MV' => '960', 
			'MW' => '265', 
			'MX' => '52', 
			'MY' => '60', 
			'MZ' => '258', 
			'NA' => '264', 
			'NC' => '687', 
			'NE' => '227', 
			'NF' => '672', 
			'NG' => '234', 
			'NI' => '505', 
			'NL' => '31', 
			'NO' => '47', 
			'NP' => '977', 
			'NR' => '674', 
			'NU' => '683', 
			'NZ' => '64', 
			'OM' => '968', 
			'PA' => '507', 
			'PE' => '51', 
			'PF' => '689', 
			'PG' => '675', 
			'PH' => '63', 
			'PK' => '92', 
			'PL' => '48', 
			'PM' => '508', 
			'PR' => '1787', 
			'PR' => '1939', 
			'PS' => '970', 
			'PT' => '351', 
			'PW' => '680', 
			'PY' => '595', 
			'QA' => '974', 
			'QN' => '374', 
			'QS' => '252', 
			'QY' => '90', 
			'RE' => '262', 
			'RO' => '40', 
			'RS' => '381', 
			'RU' => '7', 
			'RW' => '250', 
			'SA' => '966', 
			'SB' => '677', 
			'SC' => '248', 
			'SD' => '249', 
			'SE' => '46', 
			'SG' => '65', 
			'SH' => '290', 
			'SI' => '386', 
			'SJ' => '47', 
			'SK' => '421', 
			'SL' => '232', 
			'SM' => '378', 
			'SN' => '221', 
			'SO' => '252', 
			'SR' => '597', 
			'SS' => '211', 
			'ST' => '239', 
			'SV' => '503', 
			'SX' => '1721', 
			'SY' => '963', 
			'SZ' => '268', 
			'TA' => '290', 
			'TC' => '1649', 
			'TD' => '235', 
			'TG' => '228', 
			'TH' => '66', 
			'TJ' => '992', 
			'TK' => '690', 
			'TL' => '670', 
			'TM' => '993', 
			'TN' => '216', 
			'TO' => '676', 
			'TR' => '90', 
			'TT' => '1868', 
			'TV' => '688', 
			'TW' => '886', 
			'TZ' => '255', 
			'UA' => '380', 
			'UG' => '256', 
			'UK' => '44', 
			'US' => '1', 
			'UY' => '598', 
			'UZ' => '998', 
			'VA' => '379', 
			'VA' => '39', 
			'VC' => '1784', 
			'VE' => '58', 
			'VG' => '1284', 
			'VI' => '1340', 
			'VN' => '84', 
			'VU' => '678', 
			'WF' => '681', 
			'WS' => '685', 
			'XC' => '991', 
			'XD' => '888', 
			'XG' => '881', 
			'XL' => '883', 
			'XN' => '857', 
			'XN' => '858', 
			'XN' => '870', 
			'XP' => '878', 
			'XR' => '979', 
			'XS' => '808', 
			'XT' => '800', 
			'XV' => '882', 
			'YE' => '967', 
			'YT' => '262', 
			'ZA' => '27', 
			'ZM' => '260', 
			'ZW' => '263' 
		);
	
		return ( $pais == '' ) ? $paises : ( isset( $paises[$pais] ) ? $paises[$pais] : '' );
	}
} else {
	add_action( 'admin_notices', 'bh_sms_requiere_wc' );
}

//Muestra el mensaje de activación de WooCommerce y desactiva el plugin
function bh_sms_requiere_wc() {
	global $bh_sms;
		
	echo '<div class="error fade" id="message"><h3>' . $bh_sms['plugin'] . '</h3><h4>' . __( "This plugin require WooCommerce active to run!", 'bh_sms' ) . '</h4></div>';
	deactivate_plugins( DIRECCION_bh_sms );
}

//Obtiene toda la información sobre el plugin
function bh_sms_plugin( $nombre ) {
	global $bh_sms;
	
	$argumentos = ( object ) array( 
		'slug' => $nombre 
	);
	$consulta = array( 
		'action' => 'plugin_information', 
		'timeout' => 15, 
		'request' => serialize( $argumentos )
	);
	$respuesta = get_transient( 'bh_sms_plugin' );
	if ( false === $respuesta ) {
		$respuesta = wp_remote_post( 'http://api.wordpress.org/plugins/info/1.0/', array( 
			'body' => $consulta)
		);
		set_transient( 'bh_sms_plugin', $respuesta, 24 * HOUR_IN_SECONDS );
	}
	if ( !is_wp_error( $respuesta ) ) {
		$plugin = get_object_vars( unserialize( $respuesta['body'] ) );
	} else {
		$plugin['rating'] = 100;
	}

	$rating = array(
	   'rating'	=> $plugin['rating'],
	   'type'	=> 'percent',
	   'number'	=> $plugin['num_ratings'],
	);
	ob_start();
	wp_star_rating( $rating );
	$estrellas = ob_get_contents();
	ob_end_clean();

	return '<a title="' . sprintf( __( 'Please, rate %s:', 'bh_sms' ), $bh_sms['plugin'] ) . '" href="' . $bh_sms['puntuacion'] . '?rate=5#postform" class="estrellas">' . $estrellas . '</a>';
}

//Muestra el mensaje de actualización
function bh_sms_actualizacion() {
	global $bh_sms;
	
	echo '<div class="error fade" id="message"><h3>' . $bh_sms['plugin'] . '</h3><h4>' . sprintf( __( "Please, update your %s. It's very important!", 'bh_sms' ), '<a href="' . $bh_sms['ajustes'] . '" title="' . __( 'Settings', 'bh_sms' ) . '">' . __( 'settings', 'bh_sms' ) . '</a>' ) . '</h4></div>';
}

//Carga las hojas de estilo
function bh_sms_muestra_mensaje() {
	global $configuracion;

	wp_register_style( 'bh_sms_hoja_de_estilo', plugins_url( 'assets/css/style.css', __FILE__ ) ); //Carga la hoja de estilo
	wp_register_style( 'bh_sms_fuentes', plugins_url( 'assets/fonts/stylesheet.css', __FILE__ ) ); //Carga la hoja de estilo global
	wp_enqueue_style( 'bh_sms_fuentes' ); //Carga la hoja de estilo global

	/*if ( !isset( $configuracion['mensaje_pedido'] ) || !isset( $configuracion['mensaje_nota'] ) ) { //Comprueba si hay que mostrar el mensaje de actualización
		add_action( 'admin_notices', 'bh_sms_actualizacion' );
	}*/
}
add_action( 'admin_init', 'bh_sms_muestra_mensaje' );

//Eliminamos todo rastro del plugin al desinstalarlo
function bh_sms_desinstalar() {
	delete_option( 'bh_sms_settings' );
	delete_transient( 'bh_sms_plugin' );
}
register_uninstall_hook( __FILE__, 'bh_sms_desinstalar' );
?>
