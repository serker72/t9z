<?php global $bytehand_sms; ?>

<div class="wrap woocommerce">
  <h2>
    <?php _e( 'BYTEHAND SMS-уведомления.', 'bytehand_sms' ); ?>
  </h2>
  <?php 
	settings_errors(); 
	$tab = 1;
	$configuracion = get_option( 'bh_sms_settings' );
	//Traducciones ocultas    
	__( 'account Sid', 'bytehand_sms' );
	__( 'Account Sid:', 'bytehand_sms' );
	__( 'authentication Token', 'bytehand_sms' );
	__( 'Authentication Token:', 'bytehand_sms' );
	__( 'key', 'bytehand_sms' );
	__( 'Key:', 'bytehand_sms' );
	__( 'authentication key', 'bytehand_sms' );
	__( 'Authentication key:', 'bytehand_sms' );
	__( 'sender ID', 'bytehand_sms' );
	__( 'Sender ID:', 'bytehand_sms' );
	__( 'route', 'bytehand_sms' );
	__( 'Route:', 'bytehand_sms' );
	__( 'sender ID', 'bytehand_sms' );
	__( 'Sender ID:', 'bytehand_sms' );
	__( 'username', 'bytehand_sms' );
	__( 'Username:', 'bytehand_sms' );
	__( 'password', 'bytehand_sms' );
	__( 'Password:', 'bytehand_sms' );
	__( 'mobile number', 'bytehand_sms' );
	__( 'Mobile number:', 'bytehand_sms' );
	__( 'client', 'bytehand_sms' );
	__( 'Client:', 'bytehand_sms' );
	__( 'authentication ID', 'bytehand_sms' );
	__( 'Authentication ID:', 'bytehand_sms' );

	global $woocommerce, $wpml_activo;
	
	//WPML
	if ( function_exists( 'icl_register_string' ) || !$wpml_activo ) { //Versión anterior a la 3.2
		$mensaje_pedido		= ( $wpml_activo ) ? icl_translate( 'bytehand_sms', 'mensaje_pedido', $configuracion['mensaje_pedido'] ) : $configuracion['mensaje_pedido'];
		$mensaje_recibido	= ( $wpml_activo ) ? icl_translate( 'bytehand_sms', 'mensaje_recibido', $configuracion['mensaje_recibido'] ) : $configuracion['mensaje_recibido'];
		$mensaje_procesando	= ( $wpml_activo ) ? icl_translate( 'bytehand_sms', 'mensaje_procesando', $configuracion['mensaje_procesando'] ) : $configuracion['mensaje_procesando'];
		$mensaje_completado	= ( $wpml_activo ) ? icl_translate( 'bytehand_sms', 'mensaje_completado', $configuracion['mensaje_completado'] ) : $configuracion['mensaje_completado'];
		$mensaje_nota		= ( $wpml_activo ) ? icl_translate( 'bytehand_sms', 'mensaje_nota', $configuracion['mensaje_nota'] ) : $configuracion['mensaje_nota'];
	} else if ( $wpml_activo ) { //Versión 3.2 o superior
		$mensaje_pedido		= apply_filters( 'wpml_translate_single_string', $configuracion['mensaje_pedido'], 'bytehand_sms', 'mensaje_pedido' );
		$mensaje_recibido	= apply_filters( 'wpml_translate_single_string', $configuracion['mensaje_recibido'], 'bytehand_sms', 'mensaje_recibido' );
		$mensaje_procesando	= apply_filters( 'wpml_translate_single_string', $configuracion['mensaje_procesando'], 'bytehand_sms', 'mensaje_procesando' );
		$mensaje_completado	= apply_filters( 'wpml_translate_single_string', $configuracion['mensaje_completado'], 'bytehand_sms', 'mensaje_completado' );
		$mensaje_nota		= apply_filters( 'wpml_translate_single_string', $configuracion['mensaje_nota'], 'bytehand_sms', 'mensaje_nota' );
	}
  ?>
  <p>
    <?php _e( 'Добавьте в WooCommerce возможность отправлять <abbr title="Short Message Service" lang="en">SMS</abbr> уведомления каждый раз как статус заказа меняется. Уведомления администратору/клиенту при создании заказа.', 'bytehand_sms' ); ?>
  </p>
  <form method="post" action="options.php">
    <?php settings_fields( 'bh_sms_settings_group' ); ?>
    <input type="hidden" id="bh_sms_settings[servicio]" name="bh_sms_settings[servicio]" value="bytehand"></td>
	<table class="form-table bh-table">
    
      <?php         
		$proveedores = array( 
				"bytehand" 			=> "Bytehand"
			);
		
		asort( $proveedores, SORT_NATURAL | SORT_FLAG_CASE ); //Ordena alfabeticamente los proveedores
		$proveedores_campos = array( 
			"bytehand"			=> array( 
				"id" 				=> 'id',
				"key" 				=> 'Ключ',
				"from" 				=> 'Подпись',
			)
		);
	  
		//Pinta los campos de los proveedores
		foreach ( $proveedores as $valor => $proveedor ) {
			foreach ( $proveedores_campos[$valor] as $valor_campo => $campo ) {
				if ( $valor_campo == "ruta_msg91" ) {
					echo '
     <tr style="display: block;" valign="top" class="' . $valor . '"><!-- ' . $proveedor . ' -->
        <th scope="row" class="titledesc"> <label for="bh_sms_settings[' . $valor_campo . ']">' . __( ucfirst( $campo ) . ":", "bytehand_sms" ) . '</label>
          <img class="help_tip" data-tip="' . sprintf( __( "%s для вашего аккаунта в %s", "bytehand_sms" ), __( $campo, "bytehand_sms" ), $proveedor ) . '" src="' . plugins_url(  "woocommerce/assets/images/help.png" ) . '" height="16" width="16" /> </th>
        <td class="forminp forminp-number"><select id="bh_sms_settings[' . $valor_campo . ']" name="bh_sms_settings[' . $valor_campo . ']" tabindex="' . $tab++ . '">
					';
					$opciones = array( "default" => __( "Default", "bytehand_sms" ), 1 => 1, 4 => 4 );
					foreach ( $opciones as $valor => $opcion ) {
						$chequea = ( isset( $configuracion['ruta_msg91'] ) && $configuracion['ruta_msg91'] == $valor ) ? ' selected="selected"' : '';
				  		echo '<option value="' . $valor . '"' . $chequea . '>' . $opcion . '</option>' . PHP_EOL;
					}
					echo '          </select></td>
      </tr>
					';
				} else {
					echo '
    <tr  style="display: block;"  valign="top" class="' . $valor . '"><!-- ' . $proveedor . ' -->
        <th scope="row" class="titledesc"> <label for="bh_sms_settings[' . $valor_campo . ']">' . __( ucfirst( $campo ) . ":", "bytehand_sms" ) . '</label>
          <img class="help_tip" data-tip="' . sprintf( __( "%s для вашего аккаунта в %s", "bytehand_sms" ), __( $campo, "bytehand_sms" ), $proveedor ) . '" src="' . plugins_url(  "woocommerce/assets/images/help.png" ) . '" height="16" width="16" /> </th>
        <td class="forminp forminp-number"><input type="text" id="bh_sms_settings[' . $valor_campo . ']" name="bh_sms_settings[' . $valor_campo . ']" size="50" value="' . ( isset( $configuracion[$valor_campo] ) ? $configuracion[$valor_campo] : '' ) . '" tabindex="' . $tab++ . '" /></td>
      </tr>
					';
				}
			}
		}
      ?>
      <tr valign="top">
        <th scope="row" class="titledesc"> <label for="bh_sms_settings[telefono]">
            <?php _e( 'Номер мобильного администратора:', 'bytehand_sms' ); ?>
          </label>
          <img class="help_tip" data-tip="<?php _e( 'Номер, куда будут приходить СМС для администратора', 'bytehand_sms' ); ?>" src="<?php echo plugins_url(  'woocommerce/assets/images/help.png' );?>" height="16" width="16" /> </th>
        <td class="forminp forminp-number"><input type="number" id="bh_sms_settings[telefono]" name="bh_sms_settings[telefono]" size="50" value="<?php echo ( isset( $configuracion['telefono'] ) ? $configuracion['telefono'] : '' ); ?>" tabindex="<?php echo $tab++; ?>" /></td>
      </tr>
      <tr valign="top">
        <th scope="row" class="titledesc"> <label for="bh_sms_settings[notificacion]">
            <?php _e( 'Уведомлять о новом заказе?:', 'bytehand_sms' ); ?>
          </label>
          <img class="help_tip" data-tip="<?php _e( "Нажмите, если хотите получать СМС при создании нового заказа", 'bytehand_sms' ); ?>" src="<?php echo plugins_url(  'woocommerce/assets/images/help.png' );?>" height="16" width="16" /> </th>
        <td class="forminp forminp-number"><input id="bh_sms_settings[notificacion]" name="bh_sms_settings[notificacion]" type="checkbox" value="1" <?php echo ( isset( $configuracion['notificacion'] ) && $configuracion['notificacion'] == "1" ? 'checked="checked"' : '' ); ?> tabindex="<?php echo $tab++; ?>" /></td>
      </tr>
      
     
    
      <tr valign="top">
        <th scope="row" class="titledesc"> <label for="bh_sms_settings[mensaje_pedido]">
            <?php _e( 'Сообщение администратору при новом заказе:', 'bytehand_sms' ); ?>
          </label>
          <img class="help_tip" data-tip="<?php _e( 'Вы можете изменить сообщение администратору. Можно использовать следующие переменные: %id%, %order_key%, %billing_first_name%, %billing_last_name%, %billing_company%, %billing_address_1%, %billing_address_2%, %billing_city%, %billing_postcode%, %billing_country%, %billing_state%, %billing_email%, %billing_phone%, %shipping_first_name%, %shipping_last_name%, %shipping_company%, %shipping_address_1%, %shipping_address_2%, %shipping_city%, %shipping_postcode%, %shipping_country%, %shipping_state%, %shipping_method%, %shipping_method_title%, %payment_method%, %payment_method_title%, %order_discount%, %cart_discount%, %order_tax%, %order_shipping%, %order_shipping_tax%, %order_total%, %status%, %prices_include_tax%, %tax_display_cart%, %display_totals_ex_tax%, %display_cart_ex_tax%, %order_date%, %modified_date%, %customer_message%, %customer_note%, %post_status%, %shop_name%, %order_product% and %note%.', 'bytehand_sms' ); ?>" src="<?php echo plugins_url(  'woocommerce/assets/images/help.png' );?>" height="16" width="16" /> </th>
        <td class="forminp forminp-number"><textarea id="bh_sms_settings[mensaje_pedido]" name="bh_sms_settings[mensaje_pedido]" cols="50" rows="5" tabindex="<?php echo $tab++; ?>"><?php echo stripcslashes( isset( $mensaje_pedido ) ? $mensaje_pedido : sprintf( __( "Заказ № %s получен ", 'bytehand_sms' ), "%id%" ) . "%shop_name%" . "." ); ?></textarea></td>
      </tr>
	  <?php
	  $statuses = array( 
						'pending' => 'В ожидании',
						'failed' => 'Ошибка',
						'on-hold' => 'На удержании',
						'processing' => 'Обработка',
						'completed' => 'Завершено',
						'refunded' => 'Возврат',
						'cancelled' => 'Отменено',
					);
	?>
	<?php 
	foreach ($statuses as $key=>$value) {?>
		<?php
			
			if ( function_exists( 'icl_register_string' ) || !$wpml_activo ) { //Versión anterior a la 3.2
				$status_val		= ( $wpml_activo ) ? icl_translate( 'bytehand_sms', 'status_' . $key, $configuracion['status_' . $key] ) : $configuracion['status_' . $key];
			} else if ( $wpml_activo ) { //Versión 3.2 o superior
				$status_val		= apply_filters( 'wpml_translate_single_string', $configuracion['status_' . $key], 'bytehand_sms', 'status_' . $key );
			}

		?>

      <tr valign="top">
        <th scope="row" class="titledesc"> <label for="bh_sms_settings[status_<?php echo $key;?>]">
            <?php echo 'СМС для статуса "' . $value . '"'; ?>
          </label>
          <img class="help_tip" data-tip="<?php _e( 'Сообщение для статуса ' . $value . ' ('. $key . '). Можно использоватьеследующие переменные: %id%, %order_key%, %billing_first_name%, %billing_last_name%, %billing_company%, %billing_address_1%, %billing_address_2%, %billing_city%, %billing_postcode%, %billing_country%, %billing_state%, %billing_email%, %billing_phone%, %shipping_first_name%, %shipping_last_name%, %shipping_company%, %shipping_address_1%, %shipping_address_2%, %shipping_city%, %shipping_postcode%, %shipping_country%, %shipping_state%, %shipping_method%, %shipping_method_title%, %payment_method%, %payment_method_title%, %order_discount%, %cart_discount%, %order_tax%, %order_shipping%, %order_shipping_tax%, %order_total%, %status%, %prices_include_tax%, %tax_display_cart%, %display_totals_ex_tax%, %display_cart_ex_tax%, %order_date%, %modified_date%, %customer_message%, %customer_note%, %post_status%, %shop_name%, %order_product% and %note%.', 'bytehand_sms' ); ?>" src="<?php echo plugins_url(  'woocommerce/assets/images/help.png' );?>" height="16" width="16" /> </th>
        <td class="forminp forminp-number"><textarea id="bh_sms_settings[status_<?php echo $key;?>]" name="bh_sms_settings[status_<?php echo $key;?>]" cols="50" rows="5" tabindex="<?php echo $tab++; ?>"><?php echo stripcslashes( isset( $status_val ) ? $status_val : sprintf( __( 'Ваш заказ № %s получил статус %s. Спасибо за работу с нами! %s', 'bytehand_sms' ), "%id%", $value, "%shop_name%" ) ); ?></textarea></td>
      </tr>
      <?php } ?>
      <tr valign="top">
        <th scope="row" class="titledesc"> <label for="bh_sms_settings[mensaje_nota]">
            <?php _e( 'Сообщение при комментарии от админа:', 'bytehand_sms' ); ?>
          </label>
          <img class="help_tip" data-tip="<?php _e( 'Можно использовать следующие переменные: %id%, %order_key%, %billing_first_name%, %billing_last_name%, %billing_company%, %billing_address_1%, %billing_address_2%, %billing_city%, %billing_postcode%, %billing_country%, %billing_state%, %billing_email%, %billing_phone%, %shipping_first_name%, %shipping_last_name%, %shipping_company%, %shipping_address_1%, %shipping_address_2%, %shipping_city%, %shipping_postcode%, %shipping_country%, %shipping_state%, %shipping_method%, %shipping_method_title%, %payment_method%, %payment_method_title%, %order_discount%, %cart_discount%, %order_tax%, %order_shipping%, %order_shipping_tax%, %order_total%, %status%, %prices_include_tax%, %tax_display_cart%, %display_totals_ex_tax%, %display_cart_ex_tax%, %order_date%, %modified_date%, %customer_message%, %customer_note%, %post_status%, %shop_name%, %order_product% and %note%.', 'bytehand_sms' ); ?>" src="<?php echo plugins_url(  'woocommerce/assets/images/help.png' );?>" height="16" width="16" /> </th>
        <td class="forminp forminp-number"><textarea id="bh_sms_settings[mensaje_nota]" name="bh_sms_settings[mensaje_nota]" cols="50" rows="5" tabindex="<?php echo $tab++; ?>"><?php echo stripcslashes( isset( $mensaje_nota ) ? $mensaje_nota : sprintf( __( 'К вашему заказу №%s был добавлен комментарий: ', 'bytehand_sms' ), "%id%" ) . "%note%" ); ?></textarea></td>
      </tr>
    </table>
    <p class="submit">
      <input class="button-primary" type="submit" value="<?php _e( 'Сохранить', 'bytehand_sms' ); ?>"  name="submit" id="submit" tabindex="<?php echo $tab++; ?>" />
    </p>
  </form>
</div>
<script type="text/javascript">
jQuery( document ).ready( function( $ ) {	
	$( '.servicio' ).on( 'change', function () { 
		control( $( this ).val() ); 
	} );
	var control = function( capa ) {
		if ( capa == '' ) {
			capa = $( '.servicio option:selected' ).val();
		}
		var proveedores= new Array();
		<?php 
		foreach( $proveedores as $indice => $valor ) {
			echo "proveedores['$indice'] = '$valor';" . PHP_EOL;
		}
		?>
		
		for ( var valor in proveedores ) {
    		if ( valor == capa ) {
				//$( '.' + capa ).show();
			} else {
				//$( '.' + valor ).hide();
			}
		}
	};
	control( $( '.servicio' ).val() );

	if (typeof chosen !== 'undefined' && $.isFunction(chosen)) {
		jQuery( "select.chosen_select" ).chosen();
	}
<?php if ( function_exists( 'wc_custom_status_init' ) || function_exists( 'AppZab_woo_advance_order_status_init' ) || isset( $GLOBALS['advorder_lite_orderstatus'] ) ) : ?>	
	$( '.estados_personalizados' ).on( 'change', function () { 
		control_personalizados( $( this ).val() ); 
	} );
	var control_personalizados = function( capa ) {
		var estados= new Array();
		<?php foreach( $lista_de_estados as $valor ) echo "estados['$valor'] = '$valor';" . PHP_EOL; ?>

		for ( var valor in estados ) {
			$( '.' + valor ).hide();
			for ( var valor_capa in capa ) {
				if ( valor == capa[valor_capa] ) {
					$( '.' + valor ).show();
				}
			}
		}
	};

	$( '.estados_personalizados' ).each( function( i, selected ) { 
	  control_personalizados( $( selected ).val() );
	} );
<?php endif; ?>	
} );
</script> 
