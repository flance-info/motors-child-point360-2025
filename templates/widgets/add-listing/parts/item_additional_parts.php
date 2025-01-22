<?php

$_id     = stm_listings_input( 'item_id' );
$data    = stm_get_single_car_listings();
$post_id = get_the_ID();  // 1755 is add car page id
if ( $post_id !== 1755 ) {
	return;
}
$current_user = wp_get_current_user();
$display_section = false;
if ( in_array( 'administrator', $current_user->roles ) || in_array( 'stm_dealer', $current_user->roles ) ) {
	$display_section = true;
}
if ( $display_section === false ) {
	return;
}
$choices = get_parts_choices();
$selected = get_post_meta($_id, 'control_stm-transport-parts', true);
$selected = explode(',', $selected);
?>
<div class="stm_add_car_form_1">
	<div class="stm-car-listing-data-single stm-border-top-unit ">
		<div class="title heading-font"><?php esc_html_e( 'Tilf&oslash;j Ekstraudstyr', 'motors-child' ); ?></div>
		<p>&Oslash;NSKER DU, AT DER KAN TILK&Oslash;BES EKSTRAUDSTYR TIL DIT K&Oslash;RET&Oslash;J? S&Aring; V&AElig;LG DEM HER</p>

		
	</div>
	<?php
	if ( empty( $_id ) ) :
		?>
	<div id="butterbean-manager-stm_car_manager" class="butterbean-manager butterbean-manager-default">
		<div id="butterbean-stm_car_manager-section-stm_additional_parts" class="butterbean-section butterbean-section-default" aria-hidden="false">

			<div id="butterbean-control-control_stm-transport-parts" class="butterbean-control butterbean-control-multiselect">
				<div class="stm-multiselect-wrapper">

					<div class="labels">
						<div class="select-from-label">

							<span class="butterbean-label"><?php esc_html_e( 'Tilg&aelig;ngelige ekstraudstyr', 'motors-child' ); ?></span>

						</div>

						<div class="select-to-label">

							<span class="butterbean-label"><?php esc_html_e( 'Valgte ekstraudstyr', 'motors-child' ); ?></span>

						</div>

					</div>

					<select class="stm-multiselect has-value" multiple="multiple" name="stm_parts[]" id="496multiselect" style="position: absolute; left: -9999px;">
						<?php foreach ($choices as $k=>$v): ?>
						<option value="<?php echo esc_attr($k); ?>"><?php  echo $v; ?></option>
						<?php endforeach; ?>
					</select>

					<div class="stm_add_new_optionale">
						<div class="stm_add_new_inner">
							<input placeholder="Opret ekstraudstyr" value="">
							<i class="fas fa-plus"></i>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
		<script>
			jQuery(document).ready(function ($) {
				var $example = $(".stm-multiselect").select2();
				setTimeout(function () {
					$example.select2('destroy');
				}, 1000);
			});
		</script>

	<?php else: ?>
	<div id="butterbean-manager-stm_car_manager" class="butterbean-manager butterbean-manager-default">
		<div id="butterbean-stm_car_manager-section-stm_additional_parts" class="butterbean-section butterbean-section-default" aria-hidden="false">

			<div id="butterbean-control-control_stm-transport-parts" class="butterbean-control butterbean-control-multiselect">
				<div class="stm-multiselect-wrapper">

					<div class="labels">
						<div class="select-from-label">

							<span class="butterbean-label"><?php esc_html_e( 'Parts', 'motors-child' ); ?></span>

						</div>

						<div class="select-to-label">

							<span class="butterbean-label"><?php esc_html_e( 'Selected Parts', 'motors-child' ); ?></span>

						</div>

					</div>

					<select class="stm-multiselect has-value" multiple="multiple" name="stm_parts[]" id="496multiselect" style="position: absolute; left: -9999px;">
						<?php foreach ($choices as $k=>$v): ?>
						<option <?php if (in_array($k, $selected)) echo 'selected'; ?> value="<?php echo esc_attr($k); ?>"><?php  echo $v; ?></option>
						<?php endforeach; ?>
					</select>

					<div class="stm_add_new_optionale">
						<div class="stm_add_new_inner">
							<input placeholder="Add new" value="">
							<i class="fas fa-plus"></i>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
		<script>
			jQuery(document).ready(function ($) {
				var $example = $(".stm-multiselect").select2();
				setTimeout(function () {
					$example.select2('destroy');
				}, 1000);
			});
		</script>

	<?php endif; ?>


</div>
