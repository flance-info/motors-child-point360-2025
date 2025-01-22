<?php

if ( false === stm_me_get_wpcfto_mod( 'enable_plans', false ) || false === stm_is_multiple_plans() ) {
	return;
}

$_id = stm_listings_input( 'item_id' );

$plans         = MultiplePlan::getPlans();
$selected_plan = MultiplePlan::getCurrentPlan( $_id );
$is_editing    = ( ! empty( $_GET['edit_car'] ) && ! empty( $_GET['item_id'] ) ) ? true : false;
$post_id = get_the_ID();  // 1755 is add car page id
$post_ids = [1755, 93719, 93740,93728]; // page id for car, motors, caravan


if (!in_array($post_id, $post_ids)){
?>
<div class="stm-form-plans">
	<div class="stm-car-listing-data-single stm-border-top-unit ">
		<div class="title heading-font"><?php esc_html_e( 'Choose plan', 'motors-elementor-widgets' ); ?></div>
	</div>
	<div id="user_plans_select_wrap">
		<?php if ( is_user_logged_in() ) { ?>
			<div class="user-plans-list" >
				<select name="selectedPlan">
					<option value=""><?php echo esc_html__( 'Select Plan', 'motors-elementor-widgets' ); ?></option>
					<?php
					foreach ( $plans['plans'] as $plan ) :
						$selected = '';
						if ( $plan['plan_id'] === $selected_plan && $plan['used_quota'] < $plan['total_quota'] ) {
							$selected = 'selected';
						} elseif ( $plan['used_quota'] === $plan['total_quota'] ) {
							$selected = 'disabled';
						}

						if ( $is_editing && $plan['plan_id'] === $selected_plan && $plan['used_quota'] <= $plan['total_quota'] ) {
							$selected = 'selected';
						}
						?>

						<option value="<?php echo esc_attr( $plan['plan_id'] ); ?>" <?php echo esc_attr( $selected ); ?>>
							<?php echo wp_kses_post( sprintf( ( '%s %s / %s' ), $plan['label'], $plan['used_quota'], $plan['total_quota'] ) ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		<?php } else { ?>
			<p style="color: #888888; font-size: 13px;"><?php echo esc_html__( 'Please, log in to view your available plans', 'motors-elementor-widgets' ); ?></p>
		<?php } ?>
	</div>
</div>
<?php }else{ ?>

	<div class="stm-form-plans">
		<div class="stm-car-listing-data-single stm-border-top-unit ">
			<div class="title heading-font"><?php esc_html_e( 'V&AElig;lg salgsmetode', 'motors-elementor-widgets' ); ?></div>
		</div>
		<div id="user_plans_selection">
		<!--<div id="user_plans_select_wrap">-->
			<?php if ( is_user_logged_in() ) {
				get_template_part( 'templates/widgets/add-listing/parts/item_plan' );
				?>

			<?php } else { ?>
			<p class="stm-login-hide" style="color: #888888; font-size: 13px;"><?php echo esc_html__( 'Log ind med eksisterende konto eller opret en ny konto for, at v&aelig;lge salgsmetode', 'motors-elementor-widgets' ); ?></p>

				<div class="stm-login-display">
				<?php
				get_template_part( 'templates/widgets/add-listing/parts/item_plan' );
				}
				?>
			</div>
		</div>
	</div>
<?php } ?>