<?php

$_id     = stm_listings_input( 'item_id' );
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

$stm_add_leasing = get_post_meta($_id, 'stm_add_leasing', true);
$stm_car_leasing_vat = get_post_meta($_id, 'stm_car_leasing_vat', true);
$stm_van_leasing_vat = get_post_meta($_id, 'stm_van_leasing_vat', true);
?>
<div class="stm-form-leasing-edit">
	<div class="stm-car-listing-data-single stm-border-top-unit stm-row-flex">
		<div class="title heading-font"><?php esc_html_e( 'Opret k&oslash;ret&oslash;jet som leasing bil eller leasing varebil', 'motors-child' ); ?></div>
		<div class="stm-checkbox-flex">
			<div class="feature-single stm-radio">
				<label>
					<span><?php esc_html_e( 'Tilbyd dette k&oslash;ret&oslash;j som leasing, Ja', 'motors-child' ); ?></span>
					<div class="checker">
						<span>
							<input type="checkbox" <?php echo ($stm_add_leasing == 'yes') ? 'checked' : ''; ?> value="yes" name="stm_add_leasing">
						</span>
					</div>

				</label>
			</div>
			
		</div>
	</div><p>&Oslash;NSKER DU, AT TILBYDE K&Oslash;RET&Oslash;JET DU OPRETTER SOM LEASING? S&Aring; UDFYLD FELTERNE I DENNE SEKTION </p><br/>
	<div class="stm-vat-row">
		<div class="stm-checkbox-flex">
			<div class="feature-single stm-radio">
				<label>
					<span><?php esc_html_e( 'Bil inkl. moms', 'motors-child' ); ?></span>
					<div class="checker">
						<span>
							<input type="checkbox" <?php echo ($stm_car_leasing_vat == 'yes') ? 'checked' : ''; ?> value="yes" name="stm_car_leasing_vat">
						</span>
					</div>

				</label>
			</div>
			<div class="feature-single stm-radio">
				<label>
					<span><?php esc_html_e( 'Bil plus moms', 'motors-child' ); ?></span>
					<div class="checker">
						<span>
							<input type="checkbox" <?php echo ($stm_car_leasing_vat == 'no') ? 'checked' : ''; ?> value="no" name="stm_car_leasing_vat">
						</span>
					</div>

				</label>
			</div>
			<div class="feature-single stm-radio">
				<label>
					<span><?php esc_html_e( 'Varebil inkl. moms', 'motors-child' ); ?></span>
					<div class="checker">
						<span>
							<input type="checkbox" <?php echo ($stm_van_leasing_vat == 'yes') ? 'checked' : ''; ?> value="yes" name="stm_van_leasing_vat">
						</span>
					</div>

				</label>
			</div>
			<div class="feature-single stm-radio">
				<label>
					<span><?php esc_html_e( 'Varebil ekskl. moms', 'motors-child' ); ?></span>
					<div class="checker">
						<span>
							<input type="checkbox" <?php echo ($stm_van_leasing_vat == 'no') ? 'checked' : ''; ?> value="no" name="stm_van_leasing_vat">
						</span>
					</div>

				</label>
			</div>

		</div>
	</div>

</div>

<?php

$_id = stm_listings_input('item_id');

$data = stm_listings_attributes( array( 'where' => array( 'stm_leasing_section_field' => true ) ) );

$filteredData = array_filter($data, function ($item) {
    return isset($item['stm_leasing_section_field']) && $item['stm_leasing_section_field'] == 1;
});

$data = $filteredData;
$terms_args = array(
	'orderby' => 'name',
	'order' => 'ASC',
	'hide_empty' => false,
	'fields' => 'all',
	'pad_counts' => true,
);

if ($custom_listing_type && $listing_types_options) {
	$_taxonomy = ($listing_types_options[$custom_listing_type . '_addl_required_fields']) ? $listing_types_options[$custom_listing_type . '_addl_required_fields'] : array();
	$number_as_input = ($listing_types_options[$custom_listing_type . '_addl_number_as_input']) ? $listing_types_options[$custom_listing_type . '_addl_number_as_input'] : '';
	$history_report = ($listing_types_options[$custom_listing_type . '_addl_history_report']) ? $listing_types_options[$custom_listing_type . '_addl_history_report'] : '';
	$details_location = ($listing_types_options[$custom_listing_type . '_addl_details_location']) ? $listing_types_options[$custom_listing_type . '_addl_details_location'] : false;
	$required_fields = multilisting_get_main_taxonomies_to_fill($custom_listing_type);
} else {
	$_taxonomy = stm_me_get_wpcfto_mod('addl_required_fields', array());
	$number_as_input = stm_me_get_wpcfto_mod('addl_number_as_input', '');
	$history_report = stm_me_get_wpcfto_mod('addl_history_report', '');
	$details_location = stm_me_get_wpcfto_mod('addl_details_location', false);
}

$_taxonomy = (!$_taxonomy) ? array() : $_taxonomy;


$leasing_van_price                = '';
$leasing_car_price          = '';

if ( ! empty( $_id ) ) {
	$stm_leasing_van_price                = (int) getConverPrice( get_post_meta( $_id, 'stm_leasing_van_price', true ) );
	$stm_leasing_car_price                = (int) getConverPrice( get_post_meta( $_id, 'stm_leasing_car_price', true ) );
}
$vars = array(
		'stm_leasing_van_price'             => $stm_leasing_van_price,
		'stm_leasing_car_price'             => $stm_leasing_car_price,
);
?>
<div class="stm_add_car_form_1 stm-leasing-box">

	<div class="stm-form-1-end-unit clearfix">
		<?php if (!empty($data) && is_array($_taxonomy)) : ?>
			<?php foreach ($data as $data_key => $data_unit) : ?>
				<?php


				if (!in_array($data_unit['slug'], $_taxonomy, true)) :
					$terms = get_terms($data_unit['slug'], $terms_args);
					?>
					<div class="col-md-6 col-sm-12 col-xs-12 stm-form-2-quarter <?php echo $data_unit['stm_textarea_field'] ? 'stm_textarea': ''?>">
						<div class="stm-label">
							<?php if (!empty($data_unit['font'])) : ?>
								<i class="<?php echo esc_attr($data_unit['font']); ?>"></i>
							<?php endif; ?>
							<?php stm_dynamic_string_translation_e('Add A Car Step 1 Taxonomy Label ' . $data_unit['single_name'], $data_unit['single_name']); ?>
						</div>

						<?php
						if (!empty($data_unit['numeric']) || !empty($data_unit['stm_text_field']) || !empty($data_unit['stm_date_field']) || !empty($data_unit['stm_textarea_field'])) :
							$value = '';
							if (!empty($_id)) {
								$value = get_post_meta($_id, $data_unit['slug'], true);
							}
							$type = '';


							if (!empty($data_unit['stm_date_field'])) {
								$type = 'date';
							}

							if (!empty($data_unit['numeric'])) {
								$type = 'number';
							}

							if (!empty($data_unit['stm_text_field'])) {
								$type = 'text';
							}


							if (!empty($data_unit['stm_textarea_field'])) {
								?>
								<textarea class="stm_textarea form-control <?php echo (!empty($value)) ? 'stm_has_value' : ''; ?>"
										  name="stm_s_s_<?php echo esc_attr($data_unit['slug']); ?>"
										  value="<?php echo esc_attr($value); ?>"
										  placeholder="<?php printf(esc_attr__('Enter %1$s %2$s', 'motors-elementor-widgets'), esc_attr__($data_unit['single_name'], 'motors-elementor-widgets'), (!empty($data_unit['number_field_affix'])) ? '(' . esc_attr__($data_unit['number_field_affix'], 'motors-elementor-widgets') . ')' : ''); ?>"<?php //phpcs:ignore
								?>
							></textarea>

								<?
							} else { ?>
								<input
										type="<?php echo $type ?>"
										class="form-control <?php echo (!empty($value)) ? 'stm_has_value' : ''; ?>"
										name="stm_s_s_<?php echo esc_attr($data_unit['slug']); ?>"
										value="<?php echo esc_attr($value); ?>"
										placeholder="<?php printf(esc_attr__('Indtast %1$s %2$s', 'motors-elementor-widgets'), esc_attr__($data_unit['single_name'], 'motors-elementor-widgets'), (!empty($data_unit['number_field_affix'])) ? '(' . esc_attr__($data_unit['number_field_affix'], 'motors-elementor-widgets') . ')' : ''); ?>"<?php //phpcs:ignore
								?>
								/>
							<? } ?>
						<?php else : ?>
							<select name="stm_s_s_<?php echo esc_attr($data_unit['slug']); ?>"
									class="add_a_car-select">
								<?php
								$selected = '';
								if (!empty($_id)) {
									$selected = get_post_meta($_id, $data_unit['slug'], true);
								}
								?>
								<option value=""><?php printf(esc_html__('V&aelig;lg %s', 'motors-elementor-widgets'), esc_html__($data_unit['single_name'], 'motors-elementor-widgets')) ?></option><?php //phpcs:ignore?>
								<?php
								if (!empty($terms)) :
									foreach ($terms as $_term) :
										?>
										<?php
										$selected_opt = '';
										if ($selected === $_term->slug) {
											$selected_opt = 'selected';
										}
										?>
										<option value="<?php echo esc_attr($_term->slug); ?>" <?php echo esc_attr($selected_opt); ?>><?php echo esc_attr($_term->name); ?></option>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>

			<style type="text/css">
				<?php
				foreach ( $data as $data_unit ) :
					?>

                .stm-form-1-end-unit .select2-selection__rendered[title="<?php echo esc_attr__( 'Select', 'motors-elementor-widgets' ); ?> <?php stm_dynamic_string_translation_e( 'Add A Car Step 1 Taxonomy Label', $data_unit['single_name'] ); ?>"] {
                    background-color: transparent !important;
                    border: 1px solid rgba(255, 255, 255, 0.5);
                    color: #888 !important;
                }

				<?php endforeach; ?>
			</style>




		<?php endif; ?>

	</div>
	<div class="stm_add_car_form">
		<div class="stm-form-price-edit">
			<div class="row stm-relative">
				<?php
				STM_E_W\Helpers\Helper::stm_ew_load_template( 'widgets/add-listing/parts/item_price_templates/leasing_price', MOTORS_ELEMENTOR_WIDGETS_PATH, $vars );
				?>
			</div>
		</div>
	</div>
</div>

