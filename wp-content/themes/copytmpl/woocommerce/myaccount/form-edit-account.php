<?php
/**
 * Edit account form
 */

?>

<?php wc_print_notices(); ?>

<h1><?php _e( 'Personal data', 'copytmpl' ); ?></h1>

<div class="lk-info cf">
	<div class="lk-info-col">
		<div class="lk-info-form">
			<form class="edit-account" action="" method="post">
				<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

				<div class="form-item">
					<label for="account_first_name" class="form-label"><?php _e( 'First name', 'copytmpl' ); ?> <span class="required">*</span></label>
					<input type="text" class="input-text" name="account_first_name" id="account_first_name" value="<?php echo esc_attr( $user->first_name ); ?>" />
				</div>
				<div class="form-item">
					<label for="account_last_name" class="form-label"><?php _e( 'Last name', 'copytmpl' ); ?> <span class="required">*</span></label>
					<input type="text" class="input-text" name="account_last_name" id="account_last_name" value="<?php echo esc_attr( $user->last_name ); ?>" />
				</div>
				<div class="form-item">
					<label for="account_email" class="form-label"><?php _e( 'Email address', 'copytmpl' ); ?> <span class="required">*</span></label>
					<input type="email" class="input-text" name="account_email" id="account_email" value="<?php echo esc_attr( $user->user_email ); ?>" />
				</div>

				<div class="form-item">
					<span class="lk-info-change-password" href="/">Сменить пароль</span>
				</div>

				<fieldset class="form-item lk-info-change-password-content">
					<h3><?php _e( 'Password Change', 'copytmpl' ); ?></h3>
					<div class="form-item">
						<label for="password_current" class="form-label"><?php _e( 'Current Password (leave blank to leave unchanged)', 'copytmpl' ); ?></label>
						<input type="password" class="input-text" name="password_current" id="password_current" />
					</div>
					<div class="form-item">
						<label for="password_1" class="form-label"><?php _e( 'New Password (leave blank to leave unchanged)', 'copytmpl' ); ?></label>
						<input type="password" class="input-text" name="password_1" id="password_1" />
					</div>
					<div class="form-item">
						<label for="password_2" class="form-label"><?php _e( 'Confirm New Password', 'copytmpl' ); ?></label>
						<input type="password" class="input-text" name="password_2" id="password_2" />
					</div>
				</fieldset>

				<?php do_action( 'woocommerce_edit_account_form' ); ?>

				<div class="form-item">
					<?php wp_nonce_field( 'save_account_details' ); ?>
					<input type="submit" class="button" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>" />
					<input type="hidden" name="action" value="save_account_details" />
				</div>

				<?php do_action( 'woocommerce_edit_account_form_end' ); ?>
			</form>
		</div>
	</div>
	<div class="lk-info-col">
		<div class="lk-info-bonus">
			<div class="lk-info-bonus-label"><?php _e( 'Bonus', 'copytmpl' ); ?>:</div>
			<div class="lk-info-bonus-value">0</div>
			<div class="lk-info-bonus-info"><?php _e( 'You can use them in the following order, offsetting the amount of the order by the number of bonuses.', 'copytmpl' ); ?></div>
		</div>
	</div>
</div>
