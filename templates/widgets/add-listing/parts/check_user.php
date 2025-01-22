<?php
$restricted = false;
$_id        = stm_listings_input( 'item_id' );
$post_ids = [1755, 93719, 93740,93728];
if ( is_user_logged_in() ) {
	$user         = wp_get_current_user();
	$user_id      = $user->ID;
	$restrictions = stm_get_post_limits( $user_id );
} else {
	$restrictions = stm_get_post_limits( '' );
}

if ( $restrictions['posts'] < 1 && stm_enablePPL() ) {
	$restricted = true;
}

if ( get_post_meta( $_id, 'pay_per_listing', true ) && stm_enablePPL() ) {
	$restricted = false;
}

if ( ! empty( $_id ) && 'publish' === get_post_status( $_id ) ) {
	$restricted = false;
}

if ( $custom_listing_type && $listing_types_options ) {
	$dealer_pay_per_listing = ( $listing_types_options[ $custom_listing_type . '_dealer_pay_per_listing' ] ) ? $listing_types_options[ $custom_listing_type . '_dealer_pay_per_listing' ] : false;
} else {
	$dealer_pay_per_listing = stm_me_get_wpcfto_mod( 'dealer_pay_per_listing', false );
}
?>
<div class="stm-form-checking-user">
	<div class="stm-form-inner">
		<i class="stm-icon-load1"></i>
		<?php
		if ( is_user_logged_in() ) :
			$disabled = 'enabled';
			$user     = wp_get_current_user();
			$user_id  = $user->ID;
			?>
			<div id="stm_user_info">
				<?php
				STM_E_W\Helpers\Helper::stm_ew_load_template(
					'widgets/add-listing/parts/user_info',
					MOTORS_ELEMENTOR_WIDGETS_PATH,
					array(
						'user_login' => '',
						'f_name'     => '',
						'l_name'     => '',
						'user_id'    => $user_id,
						'_id'        => $_id,
						'restricted' => $restricted,
						'dealer_ppl' => $dealer_pay_per_listing,
					)
				);
				?>
			</div>
			<?php
		else :
			$disabled = 'disabled';
			?>
			<div id="stm_user_info" style="display:none;"></div>
			<?php
		endif;
		?>

		<div class="stm-not-<?php echo esc_attr( $disabled ); ?>">
			<?php
			STM_E_W\Helpers\Helper::stm_ew_load_template( 'widgets/add-listing/parts/registration', MOTORS_ELEMENTOR_WIDGETS_PATH );
			?>
			<div class="stm-add-a-car-login-overlay"></div>
			<div class="stm-add-a-car-login">
				<div class="stm-login-form">
					<form method="post">
						<input type="hidden" name="redirect" value="disable">
						<input type="hidden" name="fetch_plans" value="true">
						<div class="form-group">
							<h4><?php esc_html_e( 'Login or E-mail', 'motors-elementor-widgets' ); ?></h4>
							<input type="text" name="stm_user_login" placeholder="<?php esc_attr_e( 'Enter login or E-mail', 'motors-elementor-widgets' ); ?>">
						</div>

						<div class="form-group">
							<h4><?php esc_html_e( 'Password', 'motors-elementor-widgets' ); ?></h4>
							<input type="password" name="stm_user_password" placeholder="<?php esc_attr_e( 'Enter password', 'motors-elementor-widgets' ); ?>">
						</div>

						<div class="form-group form-checker">
							<label>
								<input type="checkbox" name="stm_remember_me">
								<span><?php esc_attr_e( 'Remember me', 'motors-elementor-widgets' ); ?></span>
							</label>
						</div>
						<input type="submit" value="Login">
						<span class="stm-listing-loader"><i class="stm-icon-load1"></i></span>
						<div class="stm-validation-message"></div>
					</form>
				</div>
			</div>
		</div>
		<?php
		if ( class_exists( '\\STM_GDPR\\STM_GDPR' ) ) {
			echo do_shortcode( '[motors_gdpr_checkbox]' );
		}
		?>
		<?php if ( ! is_user_logged_in() ) : ?>
			<ul class="add-car-btns-wrap">
				<?php
				$post_id = get_the_ID();
				if ( false === $restricted ) :
					if ( in_array($post_id, $post_ids )) {
						$btn_type = ( ! empty( $_id ) ) ? 'edit' : 'pay';
					} else {
						$btn_type = ( ! empty( $_id ) ) ? 'edit' : 'add';
					}

					$btn_type = ( ! empty( get_post_meta( $_id, 'pay_per_listing', true ) ) ) ? 'edit-ppl' : $btn_type;
					$post_id = get_the_ID();
					if ( $post_id != 1755 ) : ?>
					<li class="btn-add-edit">
						<button type="submit" class="heading-font <?php echo esc_attr( $disabled ); ?>" data-load="<?php echo esc_attr( $btn_type ); ?>"
							<?php
							if ( empty( $_id ) ) {
								echo 'data-toggle="tooltip" data-placement="top" title="' . esc_html__( 'Add a Listing using Free or Paid Plan limits', 'motors-elementor-widgets' ) . '"';
							}
							?>
						>
							<?php if ( ! empty( $_id ) ) : ?>
								<i class="stm-service-icon-add_check"></i><?php esc_html_e( 'Edit Listing', 'motors-elementor-widgets' ); ?>
							<?php else : ?>
								<i class="stm-service-icon-add_check"></i><?php esc_html_e( 'OPRET KØRETØJ', 'motors-elementor-widgets' ); ?>
							<?php endif; ?>
						</button>
						<span class="stm-add-a-car-loader add"><i class="stm-icon-load1"></i></span>
					</li>
					<?php endif; ?>
				<?php endif; ?>
				<?php if ( $dealer_pay_per_listing && empty( $_id ) ) : ?>
					<li class="btn-ppl">
						<button type="submit" class="heading-font <?php echo esc_attr( $disabled ); ?>"
								data-load="pay"
							<?php
							if ( empty( $_id ) ) {
								echo 'data-toggle="tooltip" data-placement="top" title="' . esc_html__( 'OPRET KØRETØJ', 'motors-child' ) . '"';
							}
							?>
						>
							<i class="stm-service-icon-payment_listing"></i><?php esc_html_e( 'OPRET KØRETØJ', 'motors-child' ); ?>
						</button>
						<span class="stm-add-a-car-loader pay"><i class="stm-icon-load1"></i></span>
					</li>
				<?php endif; ?>
			</ul>
		<?php endif; ?>


	</div>
</div>
<div class="stm-add-a-car-message heading-font"></div>
