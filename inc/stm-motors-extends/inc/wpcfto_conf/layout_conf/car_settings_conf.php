<?php

add_filter(
	'motors_get_all_wpcfto_config',
	function ( $global_conf ) {

		$conf                                              = [];
		$conf_name                                         = ( apply_filters( 'stm_is_elementor_demo', false ) ) ? 'Single Listing Van Templates' : 'Single Listing Van';
		$conf                                              = array(
			'name'   => $conf_name,
			'fields' => apply_filters( 'me_van_car_settings_conf', $conf ),
		);
		$global_conf[ stm_me_modify_key( $conf['name'] ) ] = $conf;

		return $global_conf;
	},
	40,
	1
);
add_filter(
	'motors_get_all_wpcfto_config',
	function ( $global_conf ) {

		$conf                                              = [];
		$conf_name                                         = ( apply_filters( 'stm_is_elementor_demo', false ) ) ? 'Single Listing Leasing Templates' : 'Single Leasing Listing Van';
		$conf                                              = array(
			'name'   => $conf_name,
			'fields' => apply_filters( 'me_leasing_car_settings_conf', $conf ),
		);
		$global_conf[ stm_me_modify_key( $conf['name'] ) ] = $conf;
		flance_write_log( $global_conf );

		return $global_conf;
	},
	40,
	1
);
add_filter( 'motors_add_listing_config', function ( $config ) {

$config['sorted_steps']['options'][0]['options'][] = array(
								'id'    => 'item_additional_parts',
								'label' => esc_html__( 'Additional Parts', 'motors-child' ),
							);
$config['sorted_steps']['options'][0]['options'][] = array(
								'id'    => 'item_van',
								'label' => esc_html__( 'Van Section', 'motors-child' ),
							);
$config['sorted_steps']['options'][0]['options'][] = array(
								'id'    => 'item_leasing',
								'label' => esc_html__( 'Leasing Section', 'motors-child' ),
							);
	return $config;
} );

function stm_leasing_listings_page_options($options) {
	$options['stm_leasing_section_field'] = array(
		'label'       => esc_html__('Leasing Section Field', 'motors-child'),
		'description' => esc_html__('Leasing Section Field', 'motors-child'),
		'value'       => '',
		'type'        => 'checkbox',
	);

	return $options;
}

add_filter('stm_listings_page_options_filter', 'stm_leasing_listings_page_options');

