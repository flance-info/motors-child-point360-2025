<?php
    namespace Motors\Elementor\Widgets;

    use Elementor\Plugin;
    use STMMultiListing;
    use WC_Product;
    use WP_Query;

    class Init
    {
        public $parts_post_type = 'stm-transport-parts';

        public function __construct()
        {
            add_action( 'elementor/widgets/register', array( $this, 'add_widgets' ) );

            add_action( 'wp', array( $this, 'buy_car_online' ) );

            add_action( 'wp_ajax_stm_ajax_buy_additional_options', array( $this, 'add_cart_additional_options' ) );
            add_action( 'wp_ajax_nopriv_stm_ajax_buy_additional_options', array( $this, 'add_cart_additional_options' ) );

            add_filter( 'woocommerce_is_sold_individually', array( $this, 'is_sold_individually' ), 10, 5 );

            add_action( 'butterbean_register', array( $this, 'listings_parts' ), 10, 2 );

            add_action( 'init', array( $this, 'register_options_post_type' ) );

            add_filter( 'register_post_type_args', array( $this, 'post_type_args' ), 10, 2 );

            remove_action('wp_ajax_stm_ajax_buy_car_online', 'stm_ajax_buy_car_online');
            remove_action('wp_ajax_nopriv_stm_ajax_buy_car_online', 'stm_ajax_buy_car_online');

            add_action( 'wp_ajax_stm_ajax_buy_car_online', array( $this, 'stm_ajax_buy_car_online' ) );
            add_action( 'wp_ajax_nopriv_stm_ajax_buy_car_online', array( $this, 'stm_ajax_buy_car_online' ) );

			add_action( 'wp_ajax_stm_ajax_buy_car_online_options', array( $this, 'stm_ajax_buy_car_online_options' ) );
            add_action( 'wp_ajax_nopriv_stm_ajax_buy_car_online_options', array( $this, 'stm_ajax_buy_car_online_options' ) );

			add_action('woocommerce_cart_calculate_fees', array( $this, 'add_custom_purchase_fee') );
        }

        public function post_type_args( $args, $post_type )
        {
            if ( apply_filters( 'stm_listings_post_type', 'listings' ) === $post_type && is_array( $args ) ) {
                $args['labels']['menu_name'] = __('Listings Car', 'motors-child');
            }

            return $args;
        }

        public function register_options_post_type()
        {
            $labels = array(
                'name'               => __('Options', 'motors-child'),
                'singular_name'      => __('Option', 'motors-child'),
                'add_new'            => __('Add New', 'motors-child'),
                'add_new_item'       => __('Add New Item', 'motors-child'),
                'edit_item'          => __('Edit Option', 'motors-child'),
                'new_item'           => __('New Option', 'motors-child'),
                'view_item'          => __('View Option', 'motors-child'),
                'search_items'       => __('Search Option', 'motors-child'),
                'not_found'          => __('Not found option', 'motors-child'),
                'not_found_in_trash' => __('Not found option to trash', 'motors-child'),
                'menu_name'          => __('Transport Options', 'motors-child'),
            );

            $args = array(
                'label'                  => __('Options', 'motors-child'),
                'labels'                 => $labels,
                'public'                 => true,
                'publicly_queryable'     => false,
                'exclude_from_search'    => null,
                'show_in_menu'           => true,
                'capability_type'        => 'post',
                'map_meta_cap'           => true,
                'hierarchical'           => false,
                'menu_icon'              => 'dashicons-admin-generic',
                'menu_position'          => 10,
                'supports'               => array( 'title', 'thumbnail' ),
                'has_archive'            => false,
                'rewrite'                => false,
            );

            register_post_type( $this->parts_post_type, $args );

            add_filter( 'woocommerce_data_stores', array( $this, 'woocommerce_data_stores' ) );
        }

        public function woocommerce_data_stores( $stores ): array
        {
            require_once __DIR__ . '/parts-woo-stores.class.php';
            $stores['product'] = 'STM_Parts_Woo_Data_Store_CPT';

            return $stores;
        }

        public static function get_post_types(): array
        {
            $listings = array( stm_listings_post_type() );
            if( ! empty( STMMultiListing::stm_get_listing_type_slugs() ) ) {
                $listings = array_merge( $listings, STMMultiListing::stm_get_listing_type_slugs() );
            }

            return $listings;
        }

        public function listings_parts( $butterbean, $post_type )
        {
            if ( $this->parts_post_type === $post_type ) {
                $butterbean->register_manager(
                    'stm_parts_manager',
                    array(
                        'label'     => esc_html__( 'Option manager', 'motors-child' ),
                        'post_type' => $post_type,
                        'context'   => 'normal',
                        'priority'  => 'high',
                    )
                );

                $manager = $butterbean->get_manager( 'stm_parts_manager' );

                $manager->register_section(
                    'stm_price',
                    array(
                        'label' => esc_html__( 'Prices', 'motors-child' ),
                        'icon'  => 'fa fa-dollar',
                    )
                );

                $manager->register_control(
                    'price',
                    array(
                        'type'    => 'number',
                        'section' => 'stm_price',
                        'label'   => esc_html__( 'Price', 'motors-child' ),
                        'attr'    => array(
                            'class' => 'widefat',
                        ),
                    )
                );

                $manager->register_setting(
                    'price',
                    array(
                        'sanitize_callback' => 'wp_filter_nohtml_kses',
                    )
                );

                return;
            }

            $listings = self::get_post_types();

            // Register managers, sections, controls, and settings here.
            if ( ! in_array( $post_type, $listings ) ) {
                return;
            }

            if ( 'listings' === $post_type ) {
                $manager = $butterbean->get_manager( 'stm_car_manager' );
            }
            else {
                $manager = $butterbean->get_manager( "{$post_type}_manager" );
            }

            if ( ! empty( $manager ) ) {
                $butterbean->register_manager(
                    'stm_parts_manager',
                    array(
                        'label'     => esc_html__( 'Option manager', 'motors-child' ),
                        'post_type' => $post_type,
                        'context'   => 'normal',
                        'priority'  => 'high',
                    )
                );
            }

            $manager->register_section(
                'stm_additional_parts',
                array(
                    'label' => esc_html__( 'Additional Parts', 'motors-child' ),
                    'icon'  => 'fas fa-list-ul',
                )
            );

            $args = array(
                'post_type'   => $this->parts_post_type,
                'post_status' => 'publish'
            );

            $posts      = new WP_Query( $args );

            if ( $posts->have_posts() ) {

                $choices = array();

                while ( $posts->have_posts() ) : $posts->the_post();

                    $choices[ get_the_ID() ] = get_the_title();

                endwhile;

                $manager->register_control(
                    'control_' . $this->parts_post_type,
                    array(
                        'type'    => 'multiselect',
                        'section' => 'stm_additional_parts',
                        'label'   => __('Parts', 'motors-child'),
                        'choices' => $choices,
                    )
                );

                $manager->register_setting(
                    'control_' . $this->parts_post_type,
                    array(
                        'sanitize_callback' => 'stm_listings_multiselect',
                    )
                );
            }
        }

        public function is_sold_individually( $passed, WC_Product $product )
        {
            $_listing   = get_post( $product->get_id() );

            $listings   = new STMMultiListing;
            $post_types = $listings->stm_get_listing_type_slugs();
            $post_types[] = 'listings';
            $post_types[] = $this->parts_post_type;

            if ( $_listing && in_array( $_listing->post_type, $post_types ) ) {
                $passed = true;
            }

            return $passed;
        }

        public static function find_cart_item( $_post_id = 0 ): bool
        {
            global $woocommerce;
            $cart_items = $woocommerce->cart->get_cart();
            $finded = false;

            if ( ! empty( $cart_items ) ) {
                $cart_item = array_filter( $cart_items, function ( $_item ) use ( $_post_id ) {
                    $product_id = apply_filters( 'woocommerce_cart_item_product_id', $_item['product_id'], $_item );

                    return ( absint( $_post_id ) === absint( $product_id ) );
                });

                $finded  = ( ! empty( $cart_item ) );
            }

            return $finded;
        }

        public static function filter_cart_items( $cart_items )
        {
            if ( ! empty( $cart_items ) ) {
                uasort($cart_items, function ( $cart_item, $_cart_item ) {
                    $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item );
                    $product    = get_post( $product_id );

                    if ( ( $product ) && in_array( $product->post_type, self::get_post_types() ) ) {
                        return -1;
                    }

                    return 1;
                });
            }

            return $cart_items;
        }

        public function add_to_cart(): bool
        {
            if ( ! isset( $_REQUEST['add-to-cart'] ) || ! is_numeric( wp_unslash( $_REQUEST['add-to-cart'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                return false;
            }

            $product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( wp_unslash( $_REQUEST['add-to-cart'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $adding_to_cart    = wc_get_product( $product_id );

            if ( ! $adding_to_cart ) {
                return false;
            }

            $quantity          = empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( wp_unslash( $_REQUEST['quantity'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );


			if ( $passed_validation ) {
		        if ( $this->check_referer_url() ) {
			        $van_price = $this->calculate_custom_price( $product_id );
			        if ( false !== WC()->cart->add_to_cart( $product_id, $quantity, 0, array(), array( 'van_price' => $van_price ) ) ) {
				        wc_add_to_cart_message( array( $product_id => $quantity ), true );
				        return true;
			        }
		        }else{
			        if ( false !== WC()->cart->add_to_cart( $product_id, $quantity ) ) {
				        wc_add_to_cart_message( array( $product_id => $quantity ), true );
				        return true;
			        }
		        }
	        }


            return false;
        }

	    public function check_referer_url() {
			if ( !empty( $_REQUEST['stm-layout'] ) ) {
				if ( $_REQUEST['stm-layout'] == 'van' ) {
					return true;
				}
			}
		    if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
			    $referer_url    = $_SERVER['HTTP_REFERER'];
			    $url_components = parse_url( $referer_url );
			    if ( isset( $url_components['query'] ) ) {
				    parse_str( $url_components['query'], $query_params );
				    if ( isset( $query_params['stm-layout'] ) && $query_params['stm-layout'] === 'van' ) {
					    return true;
				    } else {
					    return false;
				    }
			    } else {
				    return false;
			    }
		    } else {
			    return false;
		    }
	    }
	    private function calculate_custom_price( $product_id ) {
		    $custom_price = get_post_meta( $product_id, 'stm_van_price', true );
		    return $custom_price;
	    }
        public function stm_ajax_buy_car_online()
        {
            check_ajax_referer( 'stm_security_nonce', 'security' );

            $response = array( 'status' => 'Error' );

            $car_id = intval( filter_var( wp_unslash( $_POST['car_id'] ), FILTER_SANITIZE_NUMBER_INT ) );

            if ( ! empty( $car_id ) && ( get_post( $car_id ) ) ) {

                if ( class_exists( 'WooCommerce' ) && stm_me_get_wpcfto_mod( 'enable_woo_online', false ) ) {
					empty_woocommerce_cart_if_not_empty();
                    $price      = get_post_meta( $car_id, 'price', true );
                    $sale_price = get_post_meta( $car_id, 'sale_price', true );

                    if ( ! empty( $sale_price ) ) {
                        $price = $sale_price;
                    }

                    update_post_meta( $car_id, '_price', $price );
                    update_post_meta( $car_id, 'price', $price );
                    update_post_meta( $car_id, 'is_sell_online_status', 'in_cart' );

                    $checkout_url = wc_get_checkout_url();

                    try {

                        $_REQUEST['add-to-cart'] = $car_id;
						if ( $this->check_referer_url() ) {
							//$_REQUEST['stm-layout'] = 'van';
						}
                        if ( ! $this->check_listing_cart( $car_id ) ) {

                            $this->add_to_cart();
                        }

                        $status  = 'success';
                    }
                    catch (\Exception $e) {
                        $status  = 'error';
                    }

                    $response['status'] = $status;

                    if ( 'success' === $status ) {
                        $response['redirect_url'] = $checkout_url;
                    }
                }
            }

            wp_send_json( $response );
        }
		public function stm_ajax_buy_car_online_options()
        {
            check_ajax_referer( 'stm_security_nonce', 'security' );

            $response = array( 'status' => 'Error' );

            $car_id = intval( filter_var( wp_unslash( $_POST['car_id'] ), FILTER_SANITIZE_NUMBER_INT ) );
			$options = isset($_POST['options']) ? explode(',', sanitize_text_field(wp_unslash($_POST['options']))) : [];

            if ( ! empty( $car_id ) && ( get_post( $car_id ) ) ) {

                if ( class_exists( 'WooCommerce' ) && stm_me_get_wpcfto_mod( 'enable_woo_online', false ) ) {
					empty_woocommerce_cart_if_not_empty();
                    $price      = get_post_meta( $car_id, 'price', true );
                    $sale_price = get_post_meta( $car_id, 'sale_price', true );

                    if ( ! empty( $sale_price ) ) {
                        $price = $sale_price;
                    }

                    update_post_meta( $car_id, '_price', $price );
                    update_post_meta( $car_id, 'price', $price );
                    update_post_meta( $car_id, 'is_sell_online_status', 'in_cart' );

					 $stm_set_pricing_option = get_points_plan( $car_id );
	                if ( in_array( $stm_set_pricing_option, [ 'p1', 'p2', 'p3', 'F' ] ) ) {
		                update_post_meta( $car_id, 'private_car_fee', 'true' );
	                } else {
		                update_post_meta( $car_id, 'private_car_fee', 'false' );
	                }


                    $checkout_url = wc_get_checkout_url();

                    try {

                        $_REQUEST['add-to-cart'] = $car_id;
						if ( $this->check_referer_url() ) {
							//$_REQUEST['stm-layout'] = 'van';
						}
                        if ( ! $this->check_listing_cart( $car_id ) ) {

                            $this->add_to_cart();
                        }
	                    if ( ! empty( $options ) ) {
		                    foreach ( $options as $listing_id ) {
			                    $this->add_cart_additional_options_for_listing( $listing_id );
		                    }
	                    }

                        $status  = 'success';
                    }
                    catch (\Exception $e) {
                        $status  = 'error';
                    }

                    $response['status'] = $status;

                    if ( 'success' === $status ) {
                        $response['redirect_url'] = $checkout_url;
                    }
                }
            }

            wp_send_json( $response );
        }

	    public function add_cart_additional_options_for_listing( $listing_id, $add_option = true ) {
		    if ( $listing_id && ( $listing = get_post( $listing_id ) ) ) {
			    if ( $add_option ) {
				    $price      = get_post_meta( $listing_id, 'price', true );
				    $sale_price = get_post_meta( $listing_id, 'sale_price', true );
				    if ( ! empty( $sale_price ) ) {
					    $price = $sale_price;
				    }
				    update_post_meta( $listing_id, '_price', $price );
				    update_post_meta( $listing_id, 'price', $price );
				    update_post_meta( $listing_id, 'is_sell_online_status', 'in_cart' );
				    try {
						$_REQUEST['add-to-cart'] = $listing_id;
					    $this->add_to_cart();
				    } catch ( \Exception $e ) {
					    $message = $e->getMessage();
					    $status  = 'error';
				    }
			    } else {
				    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
					    $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item );
					    if ( $product_id === absint( $listing_id ) ) {
						    WC()->cart->remove_cart_item( $cart_item_key );
					    }
				    }
			    }
		    }
	    }

	    function add_custom_purchase_fee() {

		    if ( WC()->cart->is_empty() ) {
			    return;
		    }
		    $fee_applied = false;
		    $fee_amount  = 2495;
		    foreach ( WC()->cart->get_cart() as $cart_item ) {
			    $listing_id      = $cart_item['product_id'];
			    $private_car_fee = get_post_meta( $listing_id, 'private_car_fee', true );
			    if ( $private_car_fee == 'true' ) {
				    $fee_applied = true;
				    break;
			    }
		    }
		    if ( $fee_applied ) {
			    WC()->cart->add_fee( esc_html__( 'Købsgebyr', 'motors-child' ), $fee_amount , false );
		    }
	    }


        public function check_listing_cart( $listing_id = 0 )
        {
            if ( ! $listing_id ) {
                return false;
            }

            foreach ( WC()->cart->get_cart() as $cart_item ) {
                $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item );

                if ( $product_id === absint( $listing_id ) ) {
                    return true;
                }
            }

            return false;
        }

        public function add_cart_additional_options()
        {
            check_ajax_referer( 'stm-motors-woocommerce-cart' );

            $listing_id = $_POST['listing_id'] ?? 0;
            $add        = $_POST['add'] ?? 0;
            $response   = array(
                'message' => esc_html__('An error occurred, please try again later', 'masterstudy-child'),
                'status'  => 'error',
            );

            if ( $listing_id && ( $listing = get_post( $listing_id ) ) ) {

                if ( "false" !== $add ) {
                    $listing_id = $listing->ID;
                    $price      = get_post_meta( $listing_id, 'price', true );
                    $sale_price = get_post_meta( $listing_id, 'sale_price', true );

                    if ( ! empty( $sale_price ) ) {
                        $price = $sale_price;
                    }

                    update_post_meta( $listing_id, '_price', $price );
                    update_post_meta( $listing_id, 'price', $price );
                    update_post_meta( $listing_id, 'is_sell_online_status', 'in_cart' );

                    try {
                        $_REQUEST['add-to-cart'] = $listing_id;
                        $this->add_to_cart();

                        $message = esc_html__('Successfully added option', 'motors-child');
                        $status  = 'success';
                    }
                    catch (\Exception $e) {
                        $message = $e->getMessage();
                        $status  = 'error';
                    }
                }
                else {
                    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                        $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item );

                        if ( $product_id === absint( $listing_id ) ) {
                            WC()->cart->remove_cart_item( $cart_item_key );
                        }
                    }

                    $message = esc_html__('Successfully removed option', 'motors-child');
                    $status  = 'success';
                }

                ob_start();

                get_template_part('partials/single-car/car', 'cart');

                $response   = array(
                    'message' => $message,
                    'html'    => ob_get_clean(),
                    'status'  => $status
                );
            }

            wp_send_json( $response );
        }

        public function buy_car_online()
        {
            global $post;

            if ( is_admin() || ! $post || ! in_array( $post->post_type, $this->get_post_types() ) ) {
                return;
            }

            $car_id = $post->ID;

            $price      = get_post_meta( $car_id, 'price', true );
            $sale_price = get_post_meta( $car_id, 'sale_price', true );

            if ( ! empty( $sale_price ) ) {
                $price = $sale_price;
            }

            if ( ! empty( $car_id ) && ! empty( $price ) ) {
                if ( class_exists( 'WooCommerce' ) && stm_me_get_wpcfto_mod( 'enable_woo_online', false ) ) {

                    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                        $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item );

                        if ( $product_id !== absint( $car_id ) ) {
                            WC()->cart->remove_cart_item( $cart_item_key );
                        }
                    }

                    update_post_meta( $car_id, '_price', $price );
                    update_post_meta( $car_id, 'is_sell_online_status', 'in_cart' );
					$stm_set_pricing_option = get_points_plan( $car_id );
	                if ( in_array( $stm_set_pricing_option, [ 'p1', 'p2', 'p3', 'F' ] ) ) {
		                update_post_meta( $car_id, 'private_car_fee', 'true' );
	                } else {
		                update_post_meta( $car_id, 'private_car_fee', 'false' );
	                }

                    try {
                        $_REQUEST['add-to-cart'] = $car_id;
                        $this->add_to_cart();
                    }
                    catch (\Exception $e) {
                        wc_add_notice( $e->getMessage(), 'error' );
                    }

                }
            }
        }

        public function add_widgets()
        {
            if ( self::is_active() ) {
                require_once __DIR__ . '/widgets/cart.php';

                Plugin::instance()->widgets_manager->register( new Cart() );
            }
        }

        public static function is_active() {
            return class_exists( 'woocommerce' );
        }
    }