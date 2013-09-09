<?php
/**
 * Login Form
 *
 * @author 		CartonThemes
 * @package 	CartoN/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $carton; ?>

<?php $carton->show_messages(); ?>

<?php do_action('carton_before_customer_login_form'); ?>


<div class="col2-set" id="customer_login">

	<div class="col-1">

		<h2><?php _e( 'Login', 'carton' ); ?></h2>
		<form method="post" class="login">
			<p class="form-row form-row-first">
				<label for="username"><?php _e( 'Username or email', 'carton' ); ?> <span class="required">*</span></label>
				<input type="text" class="input-text" name="username" id="username" />
			</p>
			<p class="form-row form-row-last">
				<label for="password"><?php _e( 'Password', 'carton' ); ?> <span class="required">*</span></label>
				<input class="input-text" type="password" name="password" id="password" />
			</p>
			<div class="clear"></div>

			<p class="form-row">
				<?php $carton->nonce_field('login', 'login') ?>
				<input type="submit" class="button" name="login" value="<?php _e( 'Login', 'carton' ); ?>" />
				<a class="lost_password" href="<?php

				$lost_password_page_id = carton_get_page_id( 'lost_password' );

				if ( $lost_password_page_id )
					echo esc_url( get_permalink( $lost_password_page_id ) );
				else
					echo esc_url( wp_lostpassword_url( home_url() ) );

				?>"><?php _e( 'Lost Password?', 'carton' ); ?></a>
			</p>
		</form>
	</div>

	<div class="col-2">

<?php if (get_option('carton_enable_myaccount_registration')=='yes') : ?>
		<h2><?php _e( 'Register', 'carton' ); ?></h2>
		<form method="post" class="register">

			<?php if ( get_option( 'carton_registration_email_for_username' ) == 'no' ) : ?>

				<p class="form-row form-row-first">
					<label for="reg_username"><?php _e( 'Username', 'carton' ); ?> <span class="required">*</span></label>
					<input type="text" class="input-text" name="username" id="reg_username" value="<?php if (isset($_POST['username'])) echo esc_attr($_POST['username']); ?>" />
				</p>

				<p class="form-row form-row-last">

			<?php else : ?>

				<p class="form-row form-row-wide">

			<?php endif; ?>

				<label for="reg_email"><?php _e( 'Email', 'carton' ); ?> <span class="required">*</span></label>
				<input type="email" class="input-text" name="email" id="reg_email" value="<?php if (isset($_POST['email'])) echo esc_attr($_POST['email']); ?>" />
			</p>

			<div class="clear"></div>

			<p class="form-row form-row-first">
				<label for="reg_password"><?php _e( 'Password', 'carton' ); ?> <span class="required">*</span></label>
				<input type="password" class="input-text" name="password" id="reg_password" value="<?php if (isset($_POST['password'])) echo esc_attr($_POST['password']); ?>" />
			</p>
			<p class="form-row form-row-last">
				<label for="reg_password2"><?php _e( 'Re-enter password', 'carton' ); ?> <span class="required">*</span></label>
				<input type="password" class="input-text" name="password2" id="reg_password2" value="<?php if (isset($_POST['password2'])) echo esc_attr($_POST['password2']); ?>" />
			</p>
			<div class="clear"></div>

			<!-- Spam Trap -->
			<div style="left:-999em; position:absolute;"><label for="trap">Anti-spam</label><input type="text" name="email_2" id="trap" tabindex="-1" /></div>

			<?php do_action( 'register_form' ); ?>

			<p class="form-row">
				<?php $carton->nonce_field('register', 'register') ?>
				<input type="submit" class="button" name="register" value="<?php _e( 'Register', 'carton' ); ?>" />
			</p>

		</form>
<?php else: ?>
		<h2><?php _e( 'Sorry, registration of new users is suspended', 'carton' ); ?></h2>

		<form method="post" class="register">
			<?php if ( get_option( 'carton_registration_email_for_username' ) == 'no' ) : ?>
				<p class="form-row form-row-first">
					<label for="reg_username"><?php _e( 'Username', 'carton' ); ?> <span class="required">*</span></label>
					<input type="text" class="input-text" name="username" id="reg_username" disabled="disabled" value="<?php if (isset($_POST['username'])) echo esc_attr($_POST['username']); ?>" />
				</p>
				<p class="form-row form-row-last">
			<?php else : ?>
				<p class="form-row form-row-wide">
			<?php endif; ?>
				<label for="reg_email"><?php _e( 'Email', 'carton' ); ?> <span class="required">*</span></label>
				<input type="email" class="input-text" name="email" id="reg_email" disabled="disabled" value="<?php if (isset($_POST['email'])) echo esc_attr($_POST['email']); ?>" />
			</p>

			<div class="clear"></div>
			<p class="form-row form-row-first">
				<label for="reg_password"><?php _e( 'Password', 'carton' ); ?> <span class="required">*</span></label>
				<input type="password" class="input-text" name="password" id="reg_password" disabled="disabled" value="<?php if (isset($_POST['password'])) echo esc_attr($_POST['password']); ?>" />
			</p>
			<p class="form-row form-row-last">
				<label for="reg_password2"><?php _e( 'Re-enter password', 'carton' ); ?> <span class="required">*</span></label>
				<input type="password" class="input-text" name="password2" id="reg_password2" disabled="disabled" value="<?php if (isset($_POST['password2'])) echo esc_attr($_POST['password2']); ?>" />
			</p>
			<div class="clear"></div>

			<p class="form-row">
				<?php $carton->nonce_field('register', 'register') ?>
				<input type="submit" class="button disabled" name="register" disabled="disabled" value="<?php _e( 'Register', 'carton' ); ?>" />
			</p>
		</form>
<?php endif; ?>
	</div>
</div>

<?php do_action('carton_after_customer_login_form'); ?>
