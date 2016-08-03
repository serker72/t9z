<?php
//Envía el mensaje SMS
function bh_sms_envia_sms( $configuracion, $telefono, $mensaje ) {

	$respuesta = wp_remote_get( "http://bytehand.com:3800/send?id=" . $configuracion['id'] . "&key=" . $configuracion['key'] . "&from=" . $configuracion['from'] . "&to=" . $telefono . "&text=" . bh_sms_codifica_el_mensaje( $mensaje ) );

}


//Mira si necesita el prefijo telefónico internacional
function bh_sms_prefijo( $servicio ) {
	$prefijo = array( 
		"bytehand"
	);
	
	return in_array( $servicio, $prefijo );
}
?>