<?php
function custom_stm_listings_page_options($options) {
	$options['stm_text_field'] = array(
		'label'       => esc_html__('Stm text field', 'stm_vehicles_listing'),
		'description' => esc_html__('Stm text field', 'stm_vehicles_listing'),
		'value'       => '',
		'type'        => 'checkbox',
	);

	$options['stm_date_field'] = array(
		'label'       => esc_html__('Stm Date field', 'stm_vehicles_listing'),
		'description' => esc_html__('Stm Date field', 'stm_vehicles_listing'),
		'value'       => '',
		'type'        => 'checkbox',
	);

	$options['stm_textarea_field'] = array(
		'label'       => esc_html__('Stm textarea', 'stm_vehicles_listing'),
		'description' => esc_html__('Stm textarea', 'stm_vehicles_listing'),
		'value'       => '',
		'type'        => 'checkbox',
	);

	return $options;
}

add_filter('stm_listings_page_options_filter', 'custom_stm_listings_page_options');
