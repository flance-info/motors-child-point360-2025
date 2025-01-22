<?php

function stm_listings_multiselect_child( $value, $butterbean ) {
	$listing_id = $butterbean->manager->post_id;
	$v[]        = get_points_plan( $listing_id );
	wp_set_object_terms( $butterbean->manager->post_id, $v, $butterbean->name );
	return $v ? implode( ',', $v ) : false;
}

function stm_listings_multiselect_child_layout( $value, $butterbean ) {
	 $post_id = $butterbean->manager->post_id;
	$leasing  = get_post_meta( $post_id, 'stm_add_leasing', true );
	$van      = get_post_meta( $post_id, 'stm_add_van', true );

	$v      = [];
	if ($leasing == 'yes'){
		$v[] = 'stm_add_leasing';
	}
	if ($van == 'yes'){
		$v[] = 'stm_add_van';
	}
	wp_set_object_terms( $butterbean->manager->post_id, $v, $butterbean->name );
	return $v ? implode( ',', $v ) : false;
}

add_action( 'stm_after_listing_saved', function ( $post_id, $response, $update ) {
	handle_listing_save( $post_id );
	handle_listing_layout_save( $post_id );
}, 10, 3 );

function handle_listing_save( $post_id ) {
	$sell_method = get_points_plan( $post_id );
	$terms       = [];
	$terms[]     = $sell_method;
	global $stmsetpricingoptions ;
	if ( ! empty( $terms ) ) {
		foreach ( $stmsetpricingoptions as $stmsetpricingoption ) {
			wp_set_object_terms( $post_id, $terms, $stmsetpricingoption );
			update_post_meta( $post_id, $stmsetpricingoption, $sell_method );
		}
	}
}

function handle_listing_layout_save( $post_id ) {
	$leasing  = get_post_meta( $post_id, 'stm_add_leasing', true );
	$van      = get_post_meta( $post_id, 'stm_add_van', true );

	$terms       = [];
	if ($leasing == 'yes'){
		$terms[] = 'stm_add_leasing';
	}
	if ($van == 'yes'){
		$terms[] = 'stm_add_van';
	}

	if ( ! empty( $terms ) ) {
		wp_set_object_terms( $post_id, $terms, 'stm-layout' );
		update_post_meta( $post_id, 'stm-layout', implode(',', $terms) );
	}
}

function process_all_listings() {
	if ( get_option( 'process_all_listings_done_new_1' ) ) {
		return;
	}

	$post_types = [ 'listings', 'caravan', 'motor', 'mc-atv' ];
	$args       = array(
		'post_type'      => $post_types,
		'posts_per_page' => - 1,
		'post_status'    => 'publish'
	);
	$query = new WP_Query( $args );
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$post_id = get_the_ID();
			handle_listing_save( $post_id );
			handle_listing_layout_save( $post_id );
		}
		wp_reset_postdata();
	}
	update_option( 'process_all_listings_done_new_1', true );
}

add_action( 'init', 'process_all_listings' );
function custom_rewrite_rule() {

	add_rewrite_rule(
		'^biler-til-salg/leasing/?$',
		'index.php?pagename=biler-til-salg&stm-layout=stm_add_leasing&page',
		'top'
	);
	add_rewrite_rule(
		'^biler-til-salg-leasing/?$',
		'index.php?pagename=biler-til-salg-leasing&stm-layout=stm_add_leasing&page',
		'top'
	);

}

add_action( 'init', 'custom_rewrite_rule' );
function custom_query_vars( $vars ) {
	$vars[] = 'stm-layout';

	return $vars;
}

add_filter( 'query_vars', 'custom_query_vars' );
function custom_template_redirect() {

	if ( get_query_var( 'stm-layout' ) == 'stm_add_leasing' ) {
		$_GET['stm-layout']='stm_add_leasing';
	}
}

add_action( 'template_redirect', 'custom_template_redirect', 0 );
