<?php

$custom_listing_types = ['motor', 'caravan', 'mc-atv'];
add_filter( 'stm_add_car_validation', 'stm_child_replace_add_listing_notifications_item' );
function stm_child_replace_add_listing_notifications_item( $validation ) {
	global $custom_listing_types ;
	$custom_listing_type = esc_attr( $_REQUEST['custom_listing_type'] );
	$stm_edit            = esc_attr( $_REQUEST['stm_edit'] );

	if ( stm_me_get_wpcfto_mod( 'enable_plans', false ) && stm_is_multiple_plans() && 'edit-ppl' === $_POST['btn-type'] ) {
		if ( empty( $_POST['selectedPlan'] ) ) {
			$validation['error']               = false;
		}
	}

	if ( (! empty( $custom_listing_type ) && !in_array($custom_listing_type, $custom_listing_types) ) || $stm_edit == 'update' ) {
		return $validation;
	}


	$check_plans = [
		'stm_set_pricing_option' => esc_html( 'Plan Point 1, Point 2 Or Point 3', 'motors-child' ),
		'stm_pricing_option'     => esc_html( 'Plan Point360.dk or DBA & Billbasen or DBA & GULOGGRATIS', 'motors-child' ),
	];
	foreach ( $check_plans as $check_plan => $check_plan_title ) {
		if ( empty( $_REQUEST[ $check_plan ] ) ) {
			$validation['error']    = true;
			$validation['response'] = [ 'message' => esc_html( 'Please select ', 'motors-child' ) . $check_plan_title ];
		}
	}

	return $validation;
}

add_filter( 'stm_listing_save_post_meta', function ( $meta, $post_id, $update ) {
		global $custom_listing_types ;
	$custom_listing_type = esc_attr( $_REQUEST['custom_listing_type'] );
	if ( ! empty( $custom_listing_type ) && !in_array($custom_listing_type, $custom_listing_types) ) {
		return $meta;
	}
	$check_plans = [
		'stm_set_pricing_option' => esc_html( 'Plan Point 1, Point 2 Or Point 3', 'motors-child' ),
		'stm_pricing_option'     => esc_html( 'Plan Point360.dk or DBA & Billbasen or DBA & GULOGGRATIS', 'motors-child' ),
	];
	foreach ( $check_plans as $check_plan => $check_plan_title ) {
		if ( ! empty( $_REQUEST[ $check_plan ] ) ) {
			$value = esc_attr( $_REQUEST[ $check_plan ] );
			update_post_meta( $post_id, $check_plan, $value );
		}
	}
	if ( ! empty( $_REQUEST['stm_pricing_option'] ) ) {
		$_SESSION["safeproduct"] = $_REQUEST['stm_pricing_option'];
	}
	if ( ! empty( $_REQUEST['stm_set_pricing_option'] ) ) {
		$_SESSION["safeSellsproduct"] = $_REQUEST['stm_set_pricing_option'];
	}

	$custom_metas = [
		'stm_van_price', 'stm_add_leasing',  'stm_car_leasing_vat',
		'stm_van_leasing_vat', 'stm_add_van', 'stm_van_vat', 'stm_parts',
		'stm_leasing_van_price', 'stm_leasing_car_price',
	];

	foreach ( $custom_metas as $custom_meta  ) {
		if ( ! empty( $_REQUEST[ $custom_meta ] ) ) {
			$value = is_array($_REQUEST[ $custom_meta ])? $_REQUEST[ $custom_meta ] : esc_attr($_REQUEST[ $custom_meta ] );
			if ('stm_parts' == $custom_meta )
			{
				$custom_meta = 'control_stm-transport-parts';
				$value = implode( ',', $value );
			}
			update_post_meta( $post_id, $custom_meta, $value );
			$retrieved_value = get_post_meta($post_id, $custom_meta, true);
		}
	}

	return $meta;
}, 10, 3 );
function stm_add_safe_product() {

	$plan_id = $_SESSION["safeproduct"];
	$safeSellsproduct =  $_SESSION["safeSellsproduct"];

	if ( is_numeric( $plan_id ) ) {
		WC()->cart->add_to_cart( $plan_id, 1, 0, array(), array() );
		if (!empty($safeSellsproduct))
		WC()->cart->add_to_cart( 	$safeSellsproduct , 1, 0, array(), array() ); // Task for P1, P2 P3 to appear on the last confirmation page by adding car
		return;
	}
	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
		if (in_array($product_id,array(93708,93884,93886,93887)))
		{
			WC()->cart->remove_cart_item( $cart_item_key );
		}
	}
	if ($_SESSION["safeprice"] != 0)
	{
		switch ( $_SESSION["safeproduct"] ) {
			case 'Basic 100':
				WC()->cart->add_to_cart( 93708, 1, 0, array(), array( 'safe_custom_price' => $_SESSION["safeprice"] ) );
				break;
			case 'Comfort 100':
				WC()->cart->add_to_cart( 93884, 1, 0, array(), array( 'safe_custom_price' => $_SESSION["safeprice"] ) );
				break;
			case 'Comfort plus 100':
				WC()->cart->add_to_cart( 93886, 1, 0, array(), array( 'safe_custom_price' => $_SESSION["safeprice"] ) );
				break;
			case 'Comfort plus 150':
				WC()->cart->add_to_cart( 93887, 1, 0, array(), array( 'safe_custom_price' => $_SESSION["safeprice"] ) );
				break;
			default:
				WC()->cart->add_to_cart( 93887, 1, 0, array(), array( 'safe_custom_price' => $_SESSION["safeprice"] ) );
				break;
		}
	}
	
}

remove_action( 'woocommerce_before_checkout_form', 'add_safe_product' );
add_action( 'woocommerce_before_checkout_form', 'stm_add_safe_product' );
/**
 * Code for enabling custom price.
 * Used for safe products
 */
function stm_safe_custom_price_refresh( $cart_object ) {
	$plan_id = $_SESSION["safeproduct"];
	if ( is_numeric( $plan_id ) ) {
		add_set_sell_method_price( $cart_object ) ;
		return;
	}
	$name = 'Safe ' . $_SESSION["safeproduct"] . ' - ' . $_SESSION["safemonths"] . ' Måneder';
	foreach ( $cart_object->get_cart() as $item ) {
		
		if ( array_key_exists( 'safe_custom_price', $item ) ) {
			$item['data']->set_price( $item['safe_custom_price'] );
			$item['data']->set_name( $name );
		}

		if ( array_key_exists( 'van_price', $item ) ) {
			$product_name = $item['data']->get_name(). esc_html(' As Van', 'motors-child');
			$item['data']->set_price( $item['van_price'] );
			$item['data']->set_name( $product_name );
		}
		if ( $item['data']->get_name()=='Byttebil' ) {
			$itemprice = floatval($item['data']->get_price());
			//var_dump($itemprice);
			if ($itemprice > 0)	$itemprice = $itemprice * -1;
			
			$item['data']->set_price( $itemprice );
			//var_dump($item['data']->get_price());			
		}
		//echo $item['data']->get_name()."<BR/>";

	}
	
}

function add_set_sell_method_price( $cart ) {
	$safeSellsproduct = $_SESSION["safeSellsproduct"];
	if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
		return;
	}
	foreach ( $cart->get_cart() as $cart_item ) {

		if ( $cart_item['product_id'] == $safeSellsproduct ) {

			//$cart_item['data']->set_price( 0 );
		}
		
	}
}

add_filter('woocommerce_cart_item_price', 'custom_cart_item_price_display', 10, 3);

function custom_cart_item_price_display( $price, $cart_item, $cart_item_key ) {
$safeSellsproduct = $_SESSION["safeSellsproduct"];
	if ( $cart_item['product_id'] == $safeSellsproduct ) {
		return '';
	}
	return $price;
}

add_filter( 'rightpress_product_price_cart_item_product_display_price_no_changes', 'maybe_change_cart_item_display_price_no_changes', 81, 3 );
function maybe_change_cart_item_display_price_no_changes( $display_price, $cart_item, $cart_item_key ) {
	$safeSellsproduct = $_SESSION["safeSellsproduct"];

	if ($cart_item['product_id'] == $safeSellsproduct){
		return '';
	}

	return $display_price;
}

remove_action( 'woocommerce_before_calculate_totals', 'safe_custom_price_refresh' );
add_action( 'woocommerce_before_calculate_totals', 'stm_safe_custom_price_refresh' );
if ( class_exists( 'WooCommerce' ) ) {
	function empty_woocommerce_cart_if_not_empty() {

		$cart = WC()->cart;
		if ( ! $cart->is_empty() ) {

			$cart->empty_cart();
		}
	}
}

function get_parts_choices() {
	$args = array(
		'post_type'   => 'stm-transport-parts',
		'post_status' => 'publish'
	);
	$posts = new WP_Query( $args );
	$choices = array();
	if ( $posts->have_posts() ) {
		while ( $posts->have_posts() ) : $posts->the_post();
			$choices[ get_the_ID() ] = get_the_title();
		endwhile;
	}
	wp_reset_postdata();

	return $choices;
}

function get_points_plan($listing_id) {
	$lowercase_sku = null;
	$stm_set_pricing_option = get_post_meta( $listing_id, 'stm_set_pricing_option', true );
	$product_id             = $stm_set_pricing_option;
	if ( $product_id ) {
		$product     = wc_get_product( $product_id );
		$product_sku = $product->get_sku();
		if ( ! empty( $product_sku ) ) {
			$lowercase_sku = strtolower( $product_sku );
		}
	}
	return $lowercase_sku;
}

if ( ! is_admin() ) {

	if ( !defined( 'ULISTING_VERSION' ) ) {
			add_action( 'wp_enqueue_scripts', 'stm_load_theme_ss_child' );
	}
}
function stm_load_theme_ss_child() {
	if ( ! stm_is_auto_parts() && ! stm_is_rental_two() ) {
		wp_enqueue_script( 'stm-theme-scripts-ajax-child', get_theme_file_uri( '/assets/js/app-ajax-child.js' ), array( 'jquery', 'stm-theme-scripts-ajax' ), time(), true );
	}
}

