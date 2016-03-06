<div class="print-options-photo-upload-image-item-num">
	<span class="print-options-photo-upload-image-item-num-label">Количество:</span>
	<span class="print-options-photo-upload-image-item-num-selector quantity">
		<input type="text" step="<?php echo esc_attr( $step ); ?>" min="<?php echo esc_attr( $min_value ); ?>" max="<?php echo esc_attr( $max_value ); ?>" name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $input_value ); ?>" title="<?php echo esc_attr_x( 'Qty', 'Product quantity input tooltip', 'woocommerce' ) ?>" class="input-text qty text" size="4">
	</span>
</div>

<?php woocommerce_single_variation(); ?>
