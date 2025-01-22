<?php
// File: UserRoleProductMapper.php
class UserRoleProductMapper {
	public static function init() {
		add_action( 'init', [ __CLASS__, 'get_product_ids_for_current_user' ] );
		add_filter( 'stm_filter_custom_login', [ __CLASS__, 'stm_filter_custom_login' ]  );
		add_filter( 'stm_filter_custom_register', [ __CLASS__, 'stm_filter_custom_login' ]  );
	}

	public static function get_current_user_role() {
		global $current_user;
		if ( is_user_logged_in() ) {
			$user  = wp_get_current_user();
			$roles = (array) $user->roles;
			return $roles[0];
		}else{
			return 'guest';
		}

		return null;
	}

	public static function get_skus_by_user_role() {
		$role = self::get_current_user_role();
		$skus = array();
		switch ( $role ) {
			case 'stm_dealer':
				$skus = array( 'F' );
				break;
			case 'administrator':
			case 'guest':
				$skus = array( 'P1', 'p2', 'p3', 'F' );
				break;
			default:
				$skus = array( 'P1', 'p2', 'p3' );
				break;
		}

		return $skus;
	}

	public static function get_product_ids_by_skus( $skus ) {
		$product_ids = array();
		foreach ( $skus as $sku ) {
			$product_id = wc_get_product_id_by_sku( $sku );
			if ( $product_id ) {
				$product_ids[] = $product_id;
			}
		}

		return $product_ids;
	}

	public static function get_product_ids_for_current_user() {
		$skus = self::get_skus_by_user_role();

		return self::get_product_ids_by_skus( $skus );
	}

	 public static function stm_filter_custom_login($response){
		$skus = self::get_skus_by_user_role();
		$products_ids = self::get_product_ids_by_skus( $skus );

		$response['user_plans'] = $products_ids;
		return $response;
	 }
}

