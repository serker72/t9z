<?php
/**
 * Login Form
 */

?>
	<?php wc_print_notices(); ?>

	<?php do_action( 'woocommerce_before_customer_login_form' ); ?>

	<?php
		$login_page = ( empty($part) || $part == 'login' )? true : false;
		$registration_page = ( empty($part) || $part == 'registration' )? true : false;
	?>

        <?php if ((isset($_GET['wc-login-before-checkout']) && ($_GET['wc-login-before-checkout'] == '1')) || (isset($_POST['wc-login-before-checkout']) && ($_POST['wc-login-before-checkout'] == '1'))) { ?>
            <div id="wpf-umf-before-uploads-needed" style="display: block;">
                <p>Пожалуйста, <strong>войдите</strong> или <strong>зарегистрируйтесь</strong>, прежде чем приступить к оформлению заказа.</p>
            </div>
        <?php } ?>

	<?php if ( $login_page ) : ?>
		<div class="my-account-login">
			<h2 class="my-account-title-or"><?php _e( 'Login', 'copytmpl' ) ?></h2>
			<div class="popup-cols">
				<div class="popup-col popup-content">
					<form action="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" method="post" class="login">
						<?php do_action( 'woocommerce_login_form_start' ); ?>
						
						<div class="form-item">
							<label class="form-label" for="username"><?php _e( 'Username or email address', 'woocommerce' ); ?> <span class="required">*</span></label>
							<input type="text" class="input-text" name="username" id="username" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" />
						</div>
						<div class="form-item">
							<label class="form-label" for="password"><?php _e( 'Password', 'woocommerce' ); ?> <span class="required">*</span> <span class="form-item-sub"><a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php _e( 'Lost your password?', 'woocommerce' ); ?></a></span></label>
							<input class="input-text" type="password" name="password" id="password" />
						</div>

						<?php do_action( 'woocommerce_login_form' ); ?>

						<div class="form-item">
							<?php wp_nonce_field( 'woocommerce-login' ); ?>

							<input type="submit" class="button" name="login" value="<?php echo esc_attr( __( 'Login my account', 'copytmpl' ) ); ?>" /> 
						</div>
						<div class="form-item">
							<label for="rememberme" class="inline"><input name="rememberme" type="checkbox" id="rememberme" value="forever" /> <?php _e( 'Remember me', 'woocommerce' ); ?></label>
						</div>

                                                <?php
                                                // KSK
                                                if ((isset($_GET['wc-login-before-checkout']) && ($_GET['wc-login-before-checkout'] == '1')) || (isset($_POST['wc-login-before-checkout']) && ($_POST['wc-login-before-checkout'] == '1'))) {
                                                    echo '<input type="hidden" id="wc-login-before-checkout" name="wc-login-before-checkout" value="1">';
                                                }
                                                ?>
						<?php do_action( 'woocommerce_login_form_end' ); ?>
					</form>	
				</div>
				<div class="popup-col popup-content popup-info popup-info-reg">
					<div class="popup-info-title"><?php _e( 'Login and!', 'copytmpl' ); ?></div>
					<ul class="well-list">
						<li><?php _e( 'track status of orders', 'copytmpl' ); ?></li>
						<li><?php _e( 'use the bonus for every purchase', 'copytmpl' ); ?></li>
						<li><?php _e( 'get best offers', 'copytmpl' ); ?></li>
					</ul>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) && $registration_page ) : ?>
		<div class="my-account-reg">
			<h2 class="my-account-title-or"><?php _e( 'Or create an account', 'copytmpl' ) ?></h2>
			
			<div class="popup-cols">
				<div class="popup-col popup-content">
					<form action="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" method="post" class="register">
						
						<?php do_action( 'woocommerce_register_form_start' ); ?>

						<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
							<div class="form-item">
								<label for="reg_username" class="form-label"><?php _e( 'Username', 'woocommerce' ); ?> <span class="required">*</span></label>
								<input type="text" class="input-text" name="username" id="reg_username" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" />
							</div>
						<?php endif; ?>

						<div class="form-item">
							<label for="reg_email" class="form-label"><?php _e( 'Email address', 'woocommerce' ); ?> <span class="required">*</span></label>
							<input type="email" class="input-text" name="email" id="reg_email" value="<?php if ( ! empty( $_POST['email'] ) ) echo esc_attr( $_POST['email'] ); ?>" />
						</div>
						
						<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
							<div class="form-item">
								<label for="reg_password" class="form-label"><?php _e( 'Password', 'woocommerce' ); ?> <span class="required">*</span></label>
								<input type="password" class="input-text" name="password" id="reg_password" />
							</div>
						<?php endif; ?>
						
						<!-- Spam Trap -->
						<div style="<?php echo ( ( is_rtl() ) ? 'right' : 'left' ); ?>: -999em; position: absolute;"><label for="trap"><?php _e( 'Anti-spam', 'woocommerce' ); ?></label><input type="text" name="email_2" id="trap" tabindex="-1" /></div>

						<?php do_action( 'woocommerce_register_form' ); ?>
						<?php do_action( 'register_form' ); ?>

						<div class="form-item">
							<?php wp_nonce_field( 'woocommerce-register' ); ?>
							<input type="submit" class="button" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>" />
						</div>

                                                <?php
                                                // KSK
                                                if ((isset($_GET['wc-login-before-checkout']) && ($_GET['wc-login-before-checkout'] == '1')) || (isset($_POST['wc-login-before-checkout']) && ($_POST['wc-login-before-checkout'] == '1'))) {
                                                    echo '<input type="hidden" id="wc-login-before-checkout" name="wc-login-before-checkout" value="1">';
                                                }
                                                ?>
                                                
						<?php do_action( 'woocommerce_register_form_end' ); ?>
					</form>
				</div>
				<div class="popup-col popup-content popup-info popup-info-reg">
					<div class="popup-info-title">Регистрация</div>
					<ul class="well-list">
						<li>отслеживайте состояние заказов</li>
						<li>используйте бонусы за каждую покупку</li>
						<li>получайте актуальные предложения</li>
					</ul>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
