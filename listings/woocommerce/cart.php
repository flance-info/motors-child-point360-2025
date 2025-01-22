<?php
    use Motors\Elementor\Widgets\Init;

    global $post;

//    $listings   = new STMMultiListing;
//    $post_types = $listings->stm_get_listing_type_slugs();
    $posts__in  = array();

    $args = array(
        'post_type'   => 'stm-transport-parts',
        'post_status' => 'publish'
    );

    $_ids = get_post_meta( $post->ID, 'control_stm-transport-parts', true );

    if ( ! empty( $_ids ) ) {
        $_ids = explode( ',', $_ids );

        if ( ! empty( $_ids ) ) {
            $posts__in = array_merge( $posts__in, $_ids );
        }
    }

    if ( ! empty( $posts__in ) ) {
        $args['post__in'] = $posts__in;
    }
    else {
        $args['post__in'] = array(0);
    }

    $posts      = new WP_Query( $args );

 //   if ( $posts->have_posts() ) :
?>

<div id="cart" style="padding: 20px 0 0;">
    <div class="stm-motors-woocommerce-cart__row">
        <div class="stm-motors-woocommerce-cart__column stm-motors-woocommerce-cart__options">
            <?php
                wp_nonce_field( 'stm-motors-woocommerce-cart', '_ajax_nonce' );

                while ( $posts->have_posts() ) : $posts->the_post();
                    $_post_id = get_the_ID();
                    $_title   = get_the_title();
                    $_name    = 'additional-option-' . $_post_id;

                    $price      = get_post_meta( $_post_id, 'price', true );
                    $sale_price = get_post_meta( $_post_id, 'sale_price', true );

                    $checked    = Init::find_cart_item( $_post_id );
                    $attr       = '';

                    if ( $checked ) {
                        $attr = 'checked="checked"';
                    }

                    if ( ! empty( $sale_price ) ) {
                        $price = $sale_price;
                    }
            ?>
                        <div class="stm-motors-woocommerce-cart__option">
                            <div class="stm-motors-woocommerce-cart__option--checkbox">
                                <input
                                    type="checkbox"
                                    name="<?php echo esc_attr( $_name ); ?>"
                                    id="<?php echo esc_attr( $_name ); ?>"
                                    value="<?php echo esc_attr( $_post_id ); ?>"
                                    <?php echo $attr; ?>
                                >
                            </div>
                            <label for="<?php echo esc_attr( $_name ); ?>" class="stm-motors-woocommerce-cart__option--title">
                                <?php echo $_title; ?>
                                <span class="stm-motors-woocommerce-cart__option--price">
                                    <?php echo '+ ' . stm_listing_price_view( $price ); ?>
                                </span>
                                <div class="stm-motors-woocommerce-cart__option--image">
                                    <?php the_post_thumbnail( 'full', array( 'class' => 'img-responsive' ) ); ?>
                                </div>
                            </label>
                        </div>
            <?php
                endwhile;

                wp_reset_query();
            ?>
        </div>
        <div class="stm-motors-woocommerce-cart__column stm-motors-woocommerce-cart__basket">
            <?php echo do_shortcode( '[woocommerce_cart]' ); ?>
        </div>
    </div>
<!--    <div class="stm-motors-woocommerce-cart__bottom">-->
<!--        <a href="--><?php //echo wc_get_checkout_url(); ?><!--" class="stm-motors-woocommerce-checkout">-->
<!--            --><?php //esc_html_e('Buy', 'motors-child'); ?>
<!--        </a>-->
<!--    </div>-->
</div>
<?php // endif; ?>